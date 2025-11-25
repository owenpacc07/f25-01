<?php

/**
 * OS Visuals AI Chatbot API Endpoint
 *
 * Purpose: Proxy requests to AI services with fallback support
 * Accepts: JSON POST with user message and mechanism context
 * Returns: JSON response from AI service
 *
 * Service Priority:
 * 1. Primary (Groq) - Fast, reliable, free cloud API
 * 2. Fallback (Hydra) - Automatic fallback if primary fails
 *
 * Authentication: Uses API keys from .env
 */

// Set response header
header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Load environment variables
$env = parse_ini_file("/var/www/projects/f25-01/.env");

if (!$env) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to load environment configuration',
        'error_code' => 'CONFIG_ERROR'
    ]);
    exit;
}

// Load service layer
require_once(__DIR__ . '/services/BaseAIService.php');
require_once(__DIR__ . '/services/GroqService.php');
require_once(__DIR__ . '/services/HydraService.php');
require_once(__DIR__ . '/config.php');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed. Use POST.',
        'error_code' => 'METHOD_NOT_ALLOWED'
    ]);
    exit;
}

// Get and parse JSON request
$input = file_get_contents('php://input');
$request = json_decode($input, true);

if (!$request || !isset($request['message'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request. Missing "message" field.',
        'error_code' => 'INVALID_REQUEST'
    ]);
    exit;
}

$user_message = $request['message'];
$mechanism_id = $request['mechanism'] ?? null;
$mode = $request['mode'] ?? 'core';
$context = $request['context'] ?? [];

// Validate message
$user_message = trim($user_message);
if (empty($user_message)) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Message cannot be empty',
        'error_code' => 'EMPTY_MESSAGE'
    ]);
    exit;
}

// Construct system prompt (3-layer approach)
$system_prompt = constructSystemPrompt($mechanism_id, $mode, $context);

// Try to get primary service
$primaryService = $env['AI_SERVICE_PRIMARY'] ?? 'groq';
$fallbackService = $env['AI_SERVICE_FALLBACK'] ?? 'hydra';

$aiResponse = null;
$usedService = null;

// Try primary service first
try {
    $service = getAIService($primaryService, $env);
    $aiResponse = $service->sendMessage($user_message, $system_prompt, [
        'mechanism' => $mechanism_id,
        'mode' => $mode
    ]);
    $usedService = $primaryService;
} catch (Exception $e) {
    // Log primary service failure
    logMessage("Primary service ({$primaryService}) failed: " . $e->getMessage(), $env);

    // Try fallback service
    if (!empty($fallbackService) && $fallbackService !== $primaryService) {
        try {
            $service = getAIService($fallbackService, $env);
            $aiResponse = $service->sendMessage($user_message, $system_prompt, [
                'mechanism' => $mechanism_id,
                'mode' => $mode
            ]);
            $usedService = $fallbackService;
            logMessage("Fallback service ({$fallbackService}) used successfully", $env);
        } catch (Exception $e2) {
            // Both services failed
            logMessage("Fallback service ({$fallbackService}) also failed: " . $e2->getMessage(), $env);
            $aiResponse = null;
        }
    }
}

// Return response
if ($aiResponse && $aiResponse['status'] === 'success') {
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'response' => $aiResponse['response'],
        'mechanism' => $mechanism_id,
        'mode' => $mode,
        'service' => $usedService,
        'timestamp' => date('c')
    ]);
} else {
    http_response_code(503);
    $errorMsg = ($aiResponse && isset($aiResponse['response'])) ?
        $aiResponse['response'] :
        'All AI services unavailable. Please try again later.';

    echo json_encode([
        'status' => 'error',
        'message' => $errorMsg,
        'error_code' => 'SERVICE_UNAVAILABLE',
        'service_attempted' => $usedService
    ]);
}

/**
 * Construct 3-layer system prompt
 *
 * Layer 1: Global instructions
 * Layer 2: Category-specific knowledge
 * Layer 3: Mechanism-specific context
 */
function constructSystemPrompt($mechanism_id, $mode, $context) {
    // Layer 1: Global Instructions
    $layer1 = "You are an educational assistant for OS Visuals, a platform for learning Operating Systems algorithms. " .
        "Help students understand how OS mechanisms work by explaining concepts clearly and providing relevant examples. " .
        "Be friendly, encouraging, and educational. " .
        "If a question is outside your scope of knowledge (not related to OS concepts or mechanisms), " .
        "politely respond: 'That question is not in my scope of possibilities.'";

    // Layer 2: Category Knowledge
    $layer2 = getCategoryKnowledge($mechanism_id);

    // Layer 3: Mechanism-Specific Context
    $layer3 = getMechanismContext($mechanism_id, $mode, $context);

    return $layer1 . "\n\n" . $layer2 . "\n\n" . $layer3;
}

/**
 * Get category-specific knowledge based on mechanism ID
 */
function getCategoryKnowledge($mechanism_id) {
    // Determine category from mechanism ID
    $id_num = (int)$mechanism_id;

    if ($id_num >= 1 && $id_num <= 8) {
        return "The student is learning about CPU Scheduling algorithms. " .
            "These include: FCFS (First Come First Served), SJF (Shortest Job First), " .
            "Priority Scheduling, Round Robin, and their preemptive variants. " .
            "Key concepts: processes, arrival time, burst time, waiting time, turnaround time, " .
            "response time, context switching, convoy effect, starvation, fairness, quantum size.";
    } elseif ($id_num >= 11 && $id_num <= 19) {
        return "The student is learning about Memory Allocation algorithms. " .
            "These include: First Fit, Best Fit, Worst Fit, and Next Fit strategies. " .
            "Key concepts: memory blocks, available space, external fragmentation, internal fragmentation, " .
            "allocation requests, compaction, memory utilization.";
    } elseif ($id_num >= 21 && $id_num <= 29) {
        return "The student is learning about Page Replacement algorithms. " .
            "These include: FIFO (First In First Out), LRU (Least Recently Used), " .
            "Optimal, Second Chance, and Clock algorithms. " .
            "Key concepts: page table, page frames, page faults, page hits, reference string, " .
            "working set, Belady's anomaly, locality of reference.";
    } elseif ($id_num >= 31 && $id_num <= 39) {
        return "The student is learning about File Allocation algorithms. " .
            "These include: Contiguous Allocation, Linked Allocation, and Indexed Allocation. " .
            "Key concepts: disk blocks, file fragmentation, allocation tables, inode structures, " .
            "file pointers, indirect blocks.";
    } elseif ($id_num >= 41 && $id_num <= 49) {
        return "The student is learning about Disk Scheduling algorithms. " .
            "These include: FCFS, SSTF (Shortest Seek Time First), SCAN, C-SCAN, LOOK, C-LOOK. " .
            "Key concepts: disk head position, seek time, rotational latency, disk requests, " .
            "cylinder, arm movement, request queue.";
    }

    return "The student is learning about Operating Systems mechanisms and algorithms.";
}

/**
 * Get mechanism-specific context
 */
function getMechanismContext($mechanism_id, $mode, $context) {
    if (!$mechanism_id) {
        return "No specific mechanism is currently being viewed. " .
            "The student can ask general questions about OS concepts.";
    }

    $mechanism_info = getMechanismMetadata($mechanism_id);

    $context_text = "Current mechanism: m-" . str_pad($mechanism_id, 3, '0', STR_PAD_LEFT);

    if ($mechanism_info) {
        $context_text .= " " . $mechanism_info['name'];
        $context_text .= "\n\nDefinition: " . $mechanism_info['description'];

        if (!empty($mechanism_info['pros'])) {
            $context_text .= "\n\nAdvantages: " . implode(", ", $mechanism_info['pros']);
        }

        if (!empty($mechanism_info['cons'])) {
            $context_text .= "\n\nDisadvantages: " . implode(", ", $mechanism_info['cons']);
        }
    }

    // Add current animation state if available
    if (!empty($context['currentStep']) || !empty($context['isPlaying'])) {
        $context_text .= "\n\nCurrent animation state:";
        if (!empty($context['currentStep'])) {
            $context_text .= " Step " . $context['currentStep'];
        }
        if (isset($context['isPlaying'])) {
            $context_text .= ($context['isPlaying'] ? " (playing)" : " (paused)");
        }
    }

    return $context_text;
}

/**
 * Get metadata for a mechanism
 */
function getMechanismMetadata($mechanism_id) {
    $mechanisms = [
        '001' => [
            'name' => 'FCFS (First Come First Served)',
            'description' => 'Processes are executed in the order they arrive in the ready queue. Non-preemptive scheduling.',
            'pros' => ['Simple to implement', 'Fair in arrival order', 'No starvation risk'],
            'cons' => ['Convoy effect', 'Does not minimize average wait time', 'Unfair to short processes']
        ],
        '002' => [
            'name' => 'SJF (Shortest Job First)',
            'description' => 'Processes with shorter burst times are scheduled first. Can be preemptive (SRTF) or non-preemptive.',
            'pros' => ['Minimizes average waiting time', 'Good for known burst times'],
            'cons' => ['Requires knowledge of burst times', 'Can starve long processes', 'May not be practical']
        ],
        '005' => [
            'name' => 'Round Robin (RR)',
            'description' => 'Each process gets a fixed time quantum. If not completed, it goes to the end of queue.',
            'pros' => ['Fair time distribution', 'Prevents starvation', 'Good for interactive systems'],
            'cons' => ['Context switching overhead', 'Quantum size selection critical', 'Longer wait for long processes']
        ],
        '011' => [
            'name' => 'First Fit',
            'description' => 'Allocates memory in the first available block large enough for the process.',
            'pros' => ['Fast allocation', 'Simple algorithm', 'Reasonably good fragmentation'],
            'cons' => ['May leave small unusable fragments', 'Not optimal memory usage']
        ],
        '012' => [
            'name' => 'Best Fit',
            'description' => 'Allocates memory in the smallest block that fits the process.',
            'pros' => ['Minimizes external fragmentation', 'Good memory utilization'],
            'cons' => ['Slower than First Fit', 'Still produces fragmentation', 'Must scan all blocks']
        ],
        '013' => [
            'name' => 'Worst Fit',
            'description' => 'Allocates memory in the largest available block.',
            'pros' => ['Leaves larger blocks for future allocations', 'Reduces external fragmentation'],
            'cons' => ['Slower than First Fit', 'May not be practical', 'Large fragments can be wasted']
        ],
        '021' => [
            'name' => 'FIFO (First In First Out)',
            'description' => 'Replaces the page that has been in memory the longest.',
            'pros' => ['Simple to implement', 'Low overhead', 'Easy to understand'],
            'cons' => ['Poor performance', 'Susceptible to Belady\'s anomaly', 'Ignores page usage']
        ],
        '023' => [
            'name' => 'LRU (Least Recently Used)',
            'description' => 'Replaces the page that has not been used for the longest time.',
            'pros' => ['Good performance', 'Considers page usage patterns', 'Respects locality principle'],
            'cons' => ['Expensive to implement', 'Requires tracking of page accesses', 'Overhead for frequent updates']
        ],
        '041' => [
            'name' => 'FCFS (Disk Scheduling)',
            'description' => 'Disk requests are serviced in the order they arrive.',
            'pros' => ['Simple to implement', 'Fair to all requests', 'No starvation'],
            'cons' => ['Causes excessive arm movement', 'Poor performance', 'High seek time']
        ],
        '043' => [
            'name' => 'C-SCAN (Circular SCAN)',
            'description' => 'Disk arm moves in one direction until end, then returns to beginning and repeats.',
            'pros' => ['Uniform wait time', 'Better performance than SCAN', 'Prevents starvation'],
            'cons' => ['More complex than SCAN', 'Unequal performance at track boundaries']
        ]
    ];

    return $mechanisms[$mechanism_id] ?? null;
}

/**
 * Log message to file
 */
function logMessage($message, $env) {
    $logFile = $env['LOG_FILE'] ?? '/var/www/projects/f25-01/html/v3b/api/logs/chatbot.log';
    $logDir = dirname($logFile);

    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $logEntry = date('Y-m-d H:i:s') . ' - ' . $message . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

?>
