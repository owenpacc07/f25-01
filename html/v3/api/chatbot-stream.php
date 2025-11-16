<?php
/**
 * Chatbot Stream API Endpoint
 *
 * This endpoint handles chatbot requests by:
 * 1. Receiving user message and mechanism context from frontend
 * 2. Constructing a smart system prompt with mechanism-specific knowledge
 * 3. Making a request to Hydra GPT API (gpt.hydra.newpaltz.edu)
 * 4. Returning the response to the frontend
 *
 * Request Format:
 * POST /api/chatbot-stream.php
 * Content-Type: application/json
 *
 * {
 *   "message": "Why does First Fit work?",
 *   "mechanism": "011",
 *   "mode": "core",
 *   "context": {
 *     "inputData": {...},
 *     "outputData": {...},
 *     "currentStep": 2
 *   }
 * }
 *
 * Response Format:
 * {
 *   "status": "success",
 *   "response": "First Fit allocation...",
 *   "mechanism": "011",
 *   "timestamp": "2025-11-10T15:30:45Z"
 * }
 */

// Load environment configuration
$env = parse_ini_file(__DIR__ . "/../../../.env");

// Enable CORS for local development (can be restricted later)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Only POST requests are allowed"
    ]);
    exit();
}

// Read and decode JSON request body
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate required fields
if (!isset($data['message']) || !isset($data['mechanism'])) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields: message and mechanism"
    ]);
    exit();
}

$userMessage = trim($data['message']);
$mechanism = trim($data['mechanism']);
$mode = isset($data['mode']) ? trim($data['mode']) : 'core';

// Validate API key is configured
$apiKey = $env['HYDRA_API_KEY'];
if (!$apiKey || $apiKey === 'sk-YOUR_API_KEY_HERE' || $apiKey === 'sk-YOUR_API_KEY_HERE_PLACEHOLDER') {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Chatbot API key not configured. Please contact administrator.",
        "error_code" => "API_KEY_NOT_CONFIGURED"
    ]);
    exit();
}

// Validate user message is not empty
if (empty($userMessage)) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => "User message cannot be empty"
    ]);
    exit();
}

// Construct the system prompt with context
$systemPrompt = constructSystemPrompt($mechanism, $mode, $data);

// Prepare Hydra API request
$hydraUrl = $env['HYDRA_API_URL'] . "/chat/completions";
$hydraModel = $env['HYDRA_MODEL'];
$temperature = floatval($env['HYDRA_TEMPERATURE']);
$maxTokens = intval($env['HYDRA_MAX_TOKENS']);

$payload = [
    "model" => $hydraModel,
    "messages" => [
        [
            "role" => "system",
            "content" => $systemPrompt
        ],
        [
            "role" => "user",
            "content" => $userMessage
        ]
    ],
    "temperature" => $temperature,
    "max_tokens" => $maxTokens
];

// Make curl request to Hydra API
$ch = curl_init($hydraUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Handle curl errors
if ($response === false) {
    http_response_code(503);
    echo json_encode([
        "status" => "error",
        "message" => "Failed to reach Hydra API service",
        "error_code" => "HYDRA_UNREACHABLE",
        "details" => $curlError
    ]);
    exit();
}

// Parse Hydra response
$hydraResponse = json_decode($response, true);

// Handle HTTP errors from Hydra
if ($httpCode !== 200) {
    http_response_code($httpCode >= 500 ? 503 : 400);
    echo json_encode([
        "status" => "error",
        "message" => "Error from Hydra API service",
        "error_code" => "HYDRA_API_ERROR",
        "http_code" => $httpCode,
        "details" => $hydraResponse
    ]);
    exit();
}

// Extract response text from Hydra
if (!isset($hydraResponse['choices'][0]['message']['content'])) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Invalid response format from Hydra API",
        "error_code" => "INVALID_RESPONSE_FORMAT"
    ]);
    exit();
}

$assistantResponse = $hydraResponse['choices'][0]['message']['content'];

// Return success response
http_response_code(200);
echo json_encode([
    "status" => "success",
    "response" => $assistantResponse,
    "mechanism" => $mechanism,
    "mode" => $mode,
    "timestamp" => date('c')
]);

/**
 * Constructs a 3-layer system prompt for the chatbot
 *
 * Layer 1: Global educational context
 * Layer 2: Category-specific knowledge
 * Layer 3: Mechanism-specific context
 */
function constructSystemPrompt($mechanism, $mode, $data) {
    // Layer 1: Global Instructions
    $globalPrompt = "You are an educational assistant for OS Visuals, a web platform for learning Operating Systems algorithms. " .
        "Help students understand how OS scheduling, memory allocation, and resource management algorithms work. " .
        "Be friendly, clear, and educational. Use examples when helpful. " .
        "If a question is outside your scope (not about OS concepts or the mechanisms on this site), politely say: " .
        "'That question is not in my scope of possibilities.' ";

    // Layer 2: Category Knowledge based on mechanism ID
    $categoryPrompt = getCategoryPrompt($mechanism);

    // Layer 3: Mechanism-Specific Context
    $mechanismPrompt = getMechanismPrompt($mechanism);

    // Add current context from page
    $contextPrompt = "";
    if (isset($data['context'])) {
        $context = $data['context'];
        if (isset($context['currentStep'])) {
            $contextPrompt .= "The student is currently viewing step {$context['currentStep']} of the algorithm animation. ";
        }
        if (isset($context['isPlaying'])) {
            $isPlaying = $context['isPlaying'] ? 'playing' : 'paused';
            $contextPrompt .= "The animation is currently {$isPlaying}. ";
        }
    }

    return $globalPrompt . "\n\n" . $categoryPrompt . "\n\n" . $mechanismPrompt . "\n\n" . $contextPrompt;
}

/**
 * Returns category-specific knowledge for the chatbot
 */
function getCategoryPrompt($mechanism) {
    $mechNum = intval($mechanism);

    if ($mechNum >= 1 && $mechNum <= 8) {
        return "The student is learning about CPU Scheduling algorithms. " .
            "These include: FCFS (First Come First Served), SJF (Shortest Job First), Priority Scheduling, " .
            "Round Robin, and their preemptive variants. " .
            "Key concepts include: processes, arrival time, burst time, waiting time, turnaround time, response time, " .
            "convoy effect, starvation, fairness, and context switching overhead.";
    } elseif ($mechNum >= 11 && $mechNum <= 13) {
        return "The student is learning about Memory Allocation algorithms. " .
            "These include: First Fit, Best Fit, and Worst Fit strategies. " .
            "Key concepts include: memory fragmentation, hole/partition management, " .
            "allocation efficiency, external/internal fragmentation, and memory utilization.";
    } elseif ($mechNum >= 21 && $mechNum <= 25) {
        return "The student is learning about Page Replacement algorithms. " .
            "These include: FIFO, Optimal, LRU (Least Recently Used), LFU (Least Frequently Used), and MFU (Most Frequently Used). " .
            "Key concepts include: page faults, page frames, reference strings, hit/miss ratios, " .
            "locality of reference, Belady's anomaly, and working set theory.";
    } elseif ($mechNum >= 31 && $mechNum <= 33) {
        return "The student is learning about File Allocation algorithms. " .
            "These include: Contiguous allocation, Linked allocation, and Indexed allocation. " .
            "Key concepts include: disk blocks, fragmentation, access time, seek time, and storage efficiency.";
    } elseif ($mechNum >= 41 && $mechNum <= 45) {
        return "The student is learning about Disk Scheduling algorithms. " .
            "These include: FCFS (First Come First Served), SSTF (Shortest Seek Time First), " .
            "SCAN, C-SCAN, LOOK, and C-LOOK. " .
            "Key concepts include: head movement, seek time, rotational latency, disk cylinders, and scheduling fairness.";
    }

    return "The student is learning about an OS mechanism. Help them understand how it works.";
}

/**
 * Returns mechanism-specific knowledge and context
 */
function getMechanismPrompt($mechanism) {
    $mechanisms = [
        '001' => [
            'name' => 'FCFS (First Come First Served)',
            'description' => 'Processes execute in the order they arrive. Simple but can cause the convoy effect.',
            'pros' => 'Simple to understand and implement. Fair in the order of arrival.',
            'cons' => 'Can cause convoy effect (short jobs waiting for long jobs). Poor average waiting time.',
            'example' => 'If P1 arrives and takes 24 units, P2 and P3 must wait for P1 to complete.'
        ],
        '005' => [
            'name' => 'Round Robin (RR)',
            'description' => 'Each process gets a small time quantum. If not finished, goes to back of queue.',
            'pros' => 'Fair distribution of CPU time. Good response time.',
            'cons' => 'Context switching overhead. Performance depends on quantum size.',
            'example' => 'With quantum=4, each process runs for 4 units then waits for others.'
        ],
        '011' => [
            'name' => 'First Fit Memory Allocation',
            'description' => 'Allocates process to the first hole large enough to fit it.',
            'pros' => 'Fast allocation (scans from start). Simple algorithm.',
            'cons' => 'Can lead to external fragmentation. May waste memory.',
            'example' => 'If process needs 50 units and first hole is 100 units, it allocates there.'
        ],
        '012' => [
            'name' => 'Best Fit Memory Allocation',
            'description' => 'Allocates process to the smallest hole that fits.',
            'pros' => 'Minimizes wasted space. Better fragmentation than First Fit.',
            'cons' => 'Slower (must scan all holes). Creates tiny unusable fragments.',
            'example' => 'If process needs 50 units, finds the smallest hole >= 50.'
        ],
        '013' => [
            'name' => 'Worst Fit Memory Allocation',
            'description' => 'Allocates process to the largest available hole.',
            'pros' => 'Leaves larger holes for future processes.',
            'cons' => 'Creates larger fragments. Often worse than Best Fit in practice.',
            'example' => 'If process needs 50 units, finds the largest hole and allocates there.'
        ],
        '021' => [
            'name' => 'FIFO (First In First Out) Page Replacement',
            'description' => 'Replaces the oldest page in memory.',
            'pros' => 'Simple to implement.',
            'cons' => 'Can cause Belady\'s anomaly (more frames = more faults). Not optimal.',
            'example' => 'Page that arrived first is replaced when page fault occurs.'
        ],
        '041' => [
            'name' => 'FCFS Disk Scheduling',
            'description' => 'Services disk requests in the order they arrive.',
            'pros' => 'Fair, simple, avoids starvation.',
            'cons' => 'Inefficient head movement. Poor average seek time.',
            'example' => 'Request for cylinder 55 then 25 then 75 services them in that order.'
        ]
    ];

    if (isset($mechanisms[$mechanism])) {
        $mech = $mechanisms[$mechanism];
        return "Mechanism: {$mech['name']}\n\n" .
            "Description: {$mech['description']}\n" .
            "Pros: {$mech['pros']}\n" .
            "Cons: {$mech['cons']}\n" .
            "Example: {$mech['example']}";
    }

    return "Provide accurate information about Operating Systems algorithms based on your knowledge.";
}
?>
