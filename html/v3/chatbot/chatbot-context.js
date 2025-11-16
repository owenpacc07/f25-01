/**
 * Chatbot Context Extraction Module
 *
 * Extracts context from the current page about:
 * - Which algorithm is being viewed (mechanism ID)
 * - What mode the user is in (View, Advanced, Research, etc.)
 * - Current animation state (playing/paused, current step)
 * - Input and output data from the algorithm
 * - Mechanism metadata (name, category, etc.)
 *
 * This module makes the chatbot context-aware and mechanism-specific.
 *
 * Usage:
 * import { getChatbotContext, getMechanismInfo } from './chatbot-context.js';
 * const context = getChatbotContext();
 * const info = getMechanismInfo(context.mechanism);
 */

/**
 * Get complete context from current page
 * Combines all available information for the chatbot
 *
 * @returns {object} - Complete context object
 */
export function getChatbotContext() {
  return {
    // Mechanism information
    mechanism: extractMechanismId(),
    mechanismName: extractMechanismName(),
    category: detectMechanismCategory(),

    // Mode information
    mode: detectCurrentMode(),
    modeLabel: getModeLabel(),

    // Animation state
    animationState: extractAnimationState(),
    currentStep: extractCurrentStep(),
    totalSteps: extractTotalSteps(),
    isPlaying: extractIsPlaying(),

    // Algorithm data
    inputData: extractInputData(),
    outputData: extractOutputData(),

    // Page information
    pageTitle: document.title,
    pageUrl: window.location.pathname,

    // Timestamp
    timestamp: new Date().toISOString()
  };
}

/**
 * Extract mechanism ID from window.mid global variable
 *
 * Mechanism IDs:
 * - 001-008: CPU Scheduling
 * - 011-013: Memory Allocation
 * - 021-025: Page Replacement
 * - 031-033: File Allocation
 * - 041-045: Disk Scheduling
 *
 * @returns {string|null} - Mechanism ID or null if not found
 */
export function extractMechanismId() {
  // Try window.mid first (most common)
  if (typeof window.mid !== 'undefined' && window.mid) {
    return String(window.mid).padStart(3, '0');
  }

  // Try to extract from URL path
  const pathMatch = window.location.pathname.match(/m-(\d{3})/);
  if (pathMatch) {
    return pathMatch[1];
  }

  // Try to extract from data attribute
  const element = document.querySelector('[data-mechanism]');
  if (element) {
    return element.getAttribute('data-mechanism');
  }

  return null;
}

/**
 * Get the human-readable name of the mechanism
 *
 * @returns {string|null} - Mechanism name or null
 */
export function extractMechanismName() {
  const mechanism = extractMechanismId();
  if (!mechanism) return null;

  const names = getMechanismNames();
  return names[mechanism] || null;
}

/**
 * Detect which mechanism category we're in
 *
 * @returns {string} - Category name (CPU_SCHEDULING, MEMORY_ALLOCATION, etc.)
 */
export function detectMechanismCategory() {
  const mechanism = extractMechanismId();
  if (!mechanism) return 'UNKNOWN';

  const mechNum = parseInt(mechanism);

  if (mechNum >= 1 && mechNum <= 8) {
    return 'CPU_SCHEDULING';
  } else if (mechNum >= 11 && mechNum <= 13) {
    return 'MEMORY_ALLOCATION';
  } else if (mechNum >= 21 && mechNum <= 25) {
    return 'PAGE_REPLACEMENT';
  } else if (mechNum >= 31 && mechNum <= 33) {
    return 'FILE_ALLOCATION';
  } else if (mechNum >= 41 && mechNum <= 45) {
    return 'DISK_SCHEDULING';
  }

  return 'UNKNOWN';
}

/**
 * Detect current mode (View, Advanced, Research, Submission, Compare)
 *
 * @returns {string} - Mode name
 */
export function detectCurrentMode() {
  const path = window.location.pathname;

  if (path.includes('/core-a/')) return 'core-a';
  if (path.includes('/core-e/')) return 'core-e';
  if (path.includes('/core-s/')) return 'core-s';
  if (path.includes('/core-c/')) return 'core-c';
  if (path.includes('/core/')) return 'core';

  return 'unknown';
}

/**
 * Get human-readable label for current mode
 *
 * @returns {string} - Mode label
 */
export function getModeLabel() {
  const mode = detectCurrentMode();

  const labels = {
    'core': 'View Mode',
    'core-a': 'Advanced Mode',
    'core-e': 'Research Mode',
    'core-s': 'Submission Mode',
    'core-c': 'Comparison Mode',
    'unknown': 'Unknown Mode'
  };

  return labels[mode] || 'Unknown Mode';
}

/**
 * Extract current animation state
 *
 * @returns {string} - State: 'playing', 'paused', 'beginning', 'end', or 'unknown'
 */
export function extractAnimationState() {
  // Check for common animation state variables
  if (typeof window.paused !== 'undefined') {
    return window.paused ? 'paused' : 'playing';
  }

  // Check for play button state
  const playButton = document.getElementById('play');
  if (playButton) {
    const text = playButton.textContent.toLowerCase();
    if (text.includes('pause')) return 'playing';
    if (text.includes('play')) return 'paused';
  }

  // Check for custom data attribute
  const element = document.querySelector('[data-animation-state]');
  if (element) {
    return element.getAttribute('data-animation-state');
  }

  return 'unknown';
}

/**
 * Extract current step in animation
 *
 * @returns {number|null} - Current step number or null
 */
export function extractCurrentStep() {
  // Try window.currStep (CPU scheduling)
  if (typeof window.currStep !== 'undefined' && window.currStep !== null) {
    return window.currStep;
  }

  // Try window.nextBurst (sometimes used)
  if (typeof window.nextBurst !== 'undefined' && window.nextBurst !== null) {
    return window.nextBurst;
  }

  // Try to find from DOM
  const stepElement = document.querySelector('[data-current-step]');
  if (stepElement) {
    const step = parseInt(stepElement.getAttribute('data-current-step'));
    if (!isNaN(step)) return step;
  }

  // Try to parse from button or display element
  const displayElement = document.querySelector('.step-display, .current-step, [class*="step"]');
  if (displayElement) {
    const match = displayElement.textContent.match(/(\d+)/);
    if (match) return parseInt(match[1]);
  }

  return null;
}

/**
 * Extract total number of steps
 *
 * @returns {number|null} - Total steps or null
 */
export function extractTotalSteps() {
  // Try window.output length (if output data exists)
  if (typeof window.output !== 'undefined' && Array.isArray(window.output)) {
    return window.output.length;
  }

  // Try to find from DOM text (e.g., "Step 2 of 4")
  const displayElement = document.querySelector('[class*="step"], .animation-info, .progress');
  if (displayElement) {
    const match = displayElement.textContent.match(/of\s+(\d+)/);
    if (match) return parseInt(match[1]);
  }

  return null;
}

/**
 * Check if animation is currently playing
 *
 * @returns {boolean} - True if playing, false if paused
 */
export function extractIsPlaying() {
  const state = extractAnimationState();
  return state === 'playing';
}

/**
 * Extract input data from window global
 * Format varies by mechanism type
 *
 * @returns {object|array|null} - Input data or null
 */
export function extractInputData() {
  // Try window.input first (most common)
  if (typeof window.input !== 'undefined' && window.input !== null) {
    return window.input;
  }

  // Try alternative names
  if (typeof window.inputData !== 'undefined') {
    return window.inputData;
  }

  if (typeof window.algorithms !== 'undefined' && window.algorithms.input) {
    return window.algorithms.input;
  }

  return null;
}

/**
 * Extract output data from window global
 * Format varies by mechanism type
 *
 * @returns {object|array|null} - Output data or null
 */
export function extractOutputData() {
  // Try window.output first (most common)
  if (typeof window.output !== 'undefined' && window.output !== null) {
    return window.output;
  }

  // Try alternative names
  if (typeof window.outputData !== 'undefined') {
    return window.outputData;
  }

  if (typeof window.result !== 'undefined' && window.result !== null) {
    return window.result;
  }

  if (typeof window.algorithms !== 'undefined' && window.algorithms.output) {
    return window.algorithms.output;
  }

  return null;
}

/**
 * Get metadata for a mechanism
 *
 * Provides information like category, common questions, etc.
 *
 * @param {string} mechanismId - Mechanism ID (e.g., "001")
 * @returns {object} - Metadata object
 */
export function getMechanismInfo(mechanismId) {
  if (!mechanismId) {
    return {
      id: null,
      name: 'Unknown',
      category: 'UNKNOWN',
      description: 'Unknown mechanism'
    };
  }

  const info = {
    '001': {
      id: '001',
      name: 'FCFS',
      fullName: 'First Come First Served',
      category: 'CPU_SCHEDULING',
      description: 'Executes processes in arrival order',
      keyMetrics: ['wait time', 'turnaround time', 'response time'],
      pros: ['Simple', 'Fair'],
      cons: ['Convoy effect', 'Poor average wait time'],
      commonQuestions: [
        'What is the convoy effect?',
        'How does FCFS select the next process?',
        'Why is FCFS not optimal?'
      ]
    },
    '002': {
      id: '002',
      name: 'SJF',
      fullName: 'Shortest Job First (Non-Preemptive)',
      category: 'CPU_SCHEDULING',
      description: 'Executes shortest burst time process first',
      keyMetrics: ['wait time', 'turnaround time'],
      pros: ['Minimizes average wait time', 'Optimal'],
      cons: ['Requires burst time knowledge', 'Starvation possible'],
      commonQuestions: [
        'Why is SJF optimal?',
        'How does it select processes?',
        'Can starvation occur?'
      ]
    },
    '005': {
      id: '005',
      name: 'RR',
      fullName: 'Round Robin',
      category: 'CPU_SCHEDULING',
      description: 'Each process gets a time quantum then goes to back of queue',
      keyMetrics: ['response time', 'turnaround time'],
      pros: ['Fair', 'Good response time'],
      cons: ['Context switching overhead', 'Performance depends on quantum'],
      commonQuestions: [
        'What is a time quantum?',
        'How does quantum size affect performance?',
        'Why is RR fair?'
      ]
    },
    '011': {
      id: '011',
      name: 'First Fit',
      fullName: 'First Fit Memory Allocation',
      category: 'MEMORY_ALLOCATION',
      description: 'Allocates to first hole large enough',
      keyMetrics: ['fragmentation', 'allocation time'],
      pros: ['Fast', 'Simple'],
      cons: ['External fragmentation', 'Memory waste'],
      commonQuestions: [
        'What is fragmentation?',
        'How does First Fit differ from Best Fit?',
        'Why does it cause fragmentation?'
      ]
    },
    '012': {
      id: '012',
      name: 'Best Fit',
      fullName: 'Best Fit Memory Allocation',
      category: 'MEMORY_ALLOCATION',
      description: 'Allocates to smallest hole that fits',
      keyMetrics: ['fragmentation', 'allocation time'],
      pros: ['Less fragmentation than First Fit'],
      cons: ['Slower (scans all holes)', 'Still fragments'],
      commonQuestions: [
        'Why is Best Fit slower?',
        'How does it compare to First Fit?',
        'What problem does it solve?'
      ]
    },
    '013': {
      id: '013',
      name: 'Worst Fit',
      fullName: 'Worst Fit Memory Allocation',
      category: 'MEMORY_ALLOCATION',
      description: 'Allocates to largest available hole',
      keyMetrics: ['fragmentation', 'allocation time'],
      pros: ['Leaves larger holes'],
      cons: ['Often worse than Best Fit'],
      commonQuestions: [
        'Why use Worst Fit?',
        'How does it compare to others?',
        'When is it useful?'
      ]
    },
    '021': {
      id: '021',
      name: 'FIFO',
      fullName: 'FIFO Page Replacement',
      category: 'PAGE_REPLACEMENT',
      description: 'Replaces the oldest page in memory',
      keyMetrics: ['page faults', 'hit ratio'],
      pros: ['Simple to implement'],
      cons: ['Belady\'s anomaly', 'Not optimal'],
      commonQuestions: [
        'What is Belady\'s anomaly?',
        'What is a page fault?',
        'Why is FIFO not optimal?'
      ]
    },
    '023': {
      id: '023',
      name: 'LRU',
      fullName: 'Least Recently Used (LRU)',
      category: 'PAGE_REPLACEMENT',
      description: 'Replaces the least recently used page',
      keyMetrics: ['page faults', 'hit ratio'],
      pros: ['Approximates optimal', 'No Belady\'s anomaly'],
      cons: ['Requires tracking access time'],
      commonQuestions: [
        'How does LRU work?',
        'What is temporal locality?',
        'How does it avoid Belady\'s anomaly?'
      ]
    },
    '041': {
      id: '041',
      name: 'FCFS',
      fullName: 'Disk Scheduling FCFS',
      category: 'DISK_SCHEDULING',
      description: 'Services requests in arrival order',
      keyMetrics: ['seek time', 'total head movement'],
      pros: ['Fair', 'Simple'],
      cons: ['Inefficient head movement'],
      commonQuestions: [
        'What is seek time?',
        'Why is disk FCFS inefficient?',
        'What algorithm is better?'
      ]
    },
    '043': {
      id: '043',
      name: 'C-SCAN',
      fullName: 'Circular Scan (C-SCAN)',
      category: 'DISK_SCHEDULING',
      description: 'Services requests while moving in one direction, then returns',
      keyMetrics: ['seek time', 'total head movement'],
      pros: ['More uniform wait times', 'Fairer than SCAN'],
      cons: ['Return time can be long'],
      commonQuestions: [
        'How does C-SCAN differ from SCAN?',
        'What is an elevator algorithm?',
        'Why is it fairer?'
      ]
    }
  };

  return info[mechanismId] || {
    id: mechanismId,
    name: `Mechanism ${mechanismId}`,
    category: detectMechanismCategory(),
    description: 'Algorithm visualization'
  };
}

/**
 * Get human-readable names for all mechanisms
 *
 * @returns {object} - Map of mechanism ID to name
 */
function getMechanismNames() {
  return {
    '001': 'FCFS CPU Scheduling',
    '002': 'Non-Preemptive SJF',
    '003': 'Non-Preemptive Priority High',
    '004': 'Non-Preemptive Priority Low',
    '005': 'Round Robin',
    '006': 'Preemptive SJF',
    '007': 'Preemptive Priority High',
    '008': 'Preemptive Priority Low',
    '011': 'First Fit Memory Allocation',
    '012': 'Best Fit Memory Allocation',
    '013': 'Worst Fit Memory Allocation',
    '021': 'FIFO Page Replacement',
    '022': 'Optimal Page Replacement',
    '023': 'LRU Page Replacement',
    '024': 'LFU Page Replacement',
    '025': 'MFU Page Replacement',
    '031': 'Contiguous File Allocation',
    '032': 'Linked File Allocation',
    '033': 'Indexed File Allocation',
    '041': 'FCFS Disk Scheduling',
    '042': 'SSTF Disk Scheduling',
    '043': 'C-SCAN Disk Scheduling',
    '044': 'LOOK Disk Scheduling',
    '045': 'C-LOOK Disk Scheduling'
  };
}

/**
 * Create enhanced context for API request
 * Includes mechanism info and detailed state
 *
 * @returns {object} - Context ready for API
 */
export function getEnhancedContext() {
  const basicContext = getChatbotContext();
  const mechanismInfo = getMechanismInfo(basicContext.mechanism);

  return {
    ...basicContext,
    mechanismInfo,
    dataAvailability: {
      hasInputData: basicContext.inputData !== null,
      hasOutputData: basicContext.outputData !== null,
      canProvideStepInfo: basicContext.currentStep !== null && basicContext.totalSteps !== null
    }
  };
}

/**
 * Log context for debugging
 * Useful during development and troubleshooting
 */
export function logContext() {
  const context = getEnhancedContext();
  console.log('[Chatbot Context]', {
    mechanism: context.mechanism,
    mechanismName: context.mechanismName,
    category: context.category,
    mode: context.mode,
    animationState: context.animationState,
    currentStep: context.currentStep,
    totalSteps: context.totalSteps,
    isPlaying: context.isPlaying,
    dataAvailability: context.dataAvailability
  });
  return context;
}

/**
 * Test if context is available and valid
 *
 * @returns {boolean} - True if we have enough context
 */
export function hasValidContext() {
  const context = getChatbotContext();
  return context.mechanism !== null && context.mechanism !== undefined;
}

/**
 * Get context summary for logging
 *
 * @returns {string} - Human-readable context summary
 */
export function getContextSummary() {
  const context = getChatbotContext();
  const info = getMechanismInfo(context.mechanism);

  let summary = `Viewing: ${info.name || 'Unknown'} (${context.mode})`;

  if (context.currentStep !== null && context.totalSteps !== null) {
    summary += ` - Step ${context.currentStep} of ${context.totalSteps}`;
  }

  if (context.isPlaying) {
    summary += ' [Playing]';
  } else {
    summary += ' [Paused]';
  }

  return summary;
}

export default {
  getChatbotContext,
  getMechanismInfo,
  getEnhancedContext,
  logContext,
  hasValidContext,
  getContextSummary,
  extractMechanismId,
  extractMechanismName,
  detectMechanismCategory,
  detectCurrentMode,
  extractAnimationState,
  extractCurrentStep,
  extractTotalSteps,
  extractIsPlaying,
  extractInputData,
  extractOutputData
};
