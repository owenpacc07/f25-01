/**
 * Chatbot Context Extraction Module
 *
 * Extracts mechanism context, animation state, input/output data
 * Makes the chatbot aware of which algorithm is being viewed and its current state
 */

import { getDataContext, extractComparisonData } from './chatbot-data-extractor.js';

/**
 * Get comprehensive context from current page
 *
 * @returns {object} Context object with mechanism info and animation state
 */
export function getChatbotContext() {
  console.log('[ChatbotContext] Building basic context...');
  const mid = extractMechanismId();
  const context = {
    mechanism: mid,
    mechanismName: getMechanismName(mid),
    category: detectMechanismCategory(mid),
    mode: detectCurrentMode(),
    animationState: extractAnimationState(),
    currentStep: extractCurrentStep(),
    totalSteps: extractTotalSteps(),
    hasInputData: !!extractInputData(),
    hasOutputData: !!extractOutputData(),
    urlPath: window.location.pathname,
    timestamp: Date.now(),
    debugInfo: {
      windowMid: window.mid || null,
      windowCurrStep: window.currStep || null,
      windowPaused: window.paused || null,
      windowInput: !!window.input,
      windowOutput: !!window.output
    }
  };

  console.log('[ChatbotContext] Basic context built:', context);
  return context;
}

/**
 * Get enhanced context with mechanism metadata and actual data
 *
 * @returns {object} Full context with algorithm information and data context
 */
export function getEnhancedContext() {
  console.log('[ChatbotContext] Building enhanced context with data...');
  const basicContext = getChatbotContext();
  const mechanismInfo = getMechanismInfo(basicContext.mechanism);

  // Get actual input/output data from the page
  console.log('[ChatbotContext] Calling getDataContext to extract mechanism data...');
  const dataContext = getDataContext();
  console.log('[ChatbotContext] Data context retrieved. Length:', dataContext.length);
  console.log('[ChatbotContext] Data context content:', dataContext);

  const enhancedContext = {
    ...basicContext,
    mechanismInfo: mechanismInfo,
    dataAvailability: {
      inputDataAvailable: basicContext.hasInputData,
      outputDataAvailable: basicContext.hasOutputData,
      animationRunning: basicContext.animationState.isPlaying,
      stepByStepMode: basicContext.animationState.isStepping
    },
    contextQuality: determineContextQuality(basicContext, mechanismInfo),
    chatbotSuggestions: generateSuggestions(basicContext, mechanismInfo),
    // NEW: Add actual mechanism data for AI to use
    mechanismData: {
      dataContext: dataContext,
      hasActualData: dataContext.length > 0
    },
    // NEW: Formatting instructions for better AI response structure
    aiFormattingInstructions: {
      style: 'structured',
      guidelines: [
        'Use clear section headers (use # for main topics, ## for subtopics)',
        'Break responses into logical paragraphs - one idea per paragraph',
        'Use bullet points for lists or multiple items',
        'Use **bold** for important terms but sparingly',
        'Keep explanations concise but complete',
        'Include examples when explaining algorithms',
        'End with a summary or next steps if appropriate'
      ],
      formatting: {
        useSectionHeaders: true,
        useWhitespace: true,
        useBulletPoints: true,
        maxParagraphLength: '3-4 sentences'
      }
    }
  };

  console.log('[ChatbotContext] ✓ Enhanced context built. Has data:', enhancedContext.mechanismData.hasActualData);
  return enhancedContext;
}

/**
 * Extract mechanism ID from multiple sources
 *
 * @returns {string|null} Mechanism ID (e.g., "001", "011") or null
 */
export function extractMechanismId() {
  // Source 1: Global variable (set by mechanism PHP template)
  if (window.mid) {
    return String(window.mid).padStart(3, '0');
  }

  // Source 2: Parse from URL path (e.g., /core/m-001/)
  const urlMatch = window.location.pathname.match(/\/m-(\d{3})/);
  if (urlMatch) {
    return urlMatch[1];
  }

  // Source 3: Check data attribute on body
  const bodyAttr = document.body.getAttribute('data-mechanism-id');
  if (bodyAttr) {
    return String(bodyAttr).padStart(3, '0');
  }

  // Source 4: Check localStorage fallback
  const stored = localStorage.getItem('lastMechanismId');
  if (stored) {
    return String(stored).padStart(3, '0');
  }

  return null;
}

/**
 * Categorize mechanism by ID
 *
 * @param {string} mechanismId - Mechanism ID (e.g., "001")
 * @returns {string|null} Category: CPU, Memory, Page, File, Disk, or null
 */
export function detectMechanismCategory(mechanismId) {
  if (!mechanismId) return null;

  const id = parseInt(mechanismId);

  if (id >= 1 && id <= 8) return 'CPU';
  if (id >= 11 && id <= 19) return 'Memory';
  if (id >= 21 && id <= 29) return 'Page';
  if (id >= 31 && id <= 39) return 'File';
  if (id >= 41 && id <= 49) return 'Disk';

  return null;
}

/**
 * Detect current mode from URL
 *
 * @returns {string} Mode: core, core-a, core-c, core-e, core-s
 */
export function detectCurrentMode() {
  const pathParts = window.location.pathname.split('/');
  for (let part of pathParts) {
    if (part.match(/^core(?:-[a-z])?$/)) {
      return part;
    }
  }
  return 'core';
}

/**
 * Extract animation state
 *
 * @returns {object} { isPlaying, isPaused, isStepping }
 */
export function extractAnimationState() {
  const state = {
    isPlaying: false,
    isPaused: true,
    isStepping: false
  };

  // Check window.paused global
  if (window.paused === false) {
    state.isPlaying = true;
    state.isPaused = false;
  } else if (window.paused === true) {
    state.isPlaying = false;
    state.isPaused = true;
  }

  // Check for play/pause buttons in DOM
  const playButton = document.querySelector('[id*="play"], [class*="play"]');
  const pauseButton = document.querySelector('[id*="pause"], [class*="pause"]');

  if (playButton?.disabled === false && pauseButton?.disabled === true) {
    state.isPlaying = false;
    state.isPaused = true;
  } else if (playButton?.disabled === true && pauseButton?.disabled === false) {
    state.isPlaying = true;
    state.isPaused = false;
  }

  // Check data attribute
  const dataAttr = document.body.getAttribute('data-animation-state');
  if (dataAttr === 'playing') {
    state.isPlaying = true;
    state.isPaused = false;
  } else if (dataAttr === 'paused') {
    state.isPlaying = false;
    state.isPaused = true;
  }

  return state;
}

/**
 * Extract current step in algorithm
 *
 * @returns {number|null} Current step or null if unavailable
 */
export function extractCurrentStep() {
  // Source 1: Global variable
  if (window.currStep !== undefined && window.currStep !== null) {
    return parseInt(window.currStep);
  }

  // Source 2: CPU scheduling burst counter
  if (window.nextBurst !== undefined && window.nextBurst !== null) {
    return parseInt(window.nextBurst);
  }

  // Source 3: Look for step counter in DOM
  const stepElements = document.querySelectorAll('[class*="step"], [id*="step"]');
  for (let elem of stepElements) {
    const text = elem.textContent.match(/\d+/);
    if (text) return parseInt(text[0]);
  }

  // Source 4: Count highlighted rows in table
  const highlightedRows = document.querySelectorAll('tr.active, tr.highlight, tr.current');
  if (highlightedRows.length > 0) {
    // This is approximate - actual step is row index
    return highlightedRows[0].rowIndex || null;
  }

  return null;
}

/**
 * Extract total steps in algorithm
 *
 * @returns {number|null} Total steps or null if unavailable
 */
export function extractTotalSteps() {
  // Source 1: Length of output array
  if (window.output && Array.isArray(window.output)) {
    return window.output.length;
  }

  // Source 2: Count rows in animation table
  const tableRows = document.querySelectorAll('table tbody tr');
  if (tableRows.length > 0) {
    return tableRows.length;
  }

  // Source 3: Length of input array
  if (window.input && Array.isArray(window.input)) {
    return window.input.length;
  }

  // Source 4: Count items in visualization
  const animationItems = document.querySelectorAll('[class*="animation-item"], [class*="item"]');
  if (animationItems.length > 0) {
    return animationItems.length;
  }

  return null;
}

/**
 * Extract input data from page
 *
 * @returns {object|null} Algorithm input data or null
 */
export function extractInputData() {
  // Source 1: Global window.input
  if (window.input) {
    return window.input;
  }

  // Source 2: CPU scheduling data
  if (window.processData) {
    return window.processData;
  }

  // Source 3: Memory allocation data
  if (window.memoryData) {
    return window.memoryData;
  }

  // Source 4: Page replacement data
  if (window.pageData) {
    return window.pageData;
  }

  // Source 5: Disk scheduling data
  if (window.diskData) {
    return window.diskData;
  }

  return null;
}

/**
 * Extract output data from page
 *
 * @returns {object|null} Algorithm results or null
 */
export function extractOutputData() {
  // Source 1: Global window.output
  if (window.output) {
    return window.output;
  }

  // Source 2: Results variable
  if (window.results) {
    return window.results;
  }

  // Source 3: Animation data
  if (window.animationData) {
    return window.animationData;
  }

  // Source 4: Metrics
  if (window.metrics) {
    return window.metrics;
  }

  return null;
}

/**
 * Get detailed metadata about a mechanism
 *
 * @param {string} mechanismId - Mechanism ID (e.g., "001")
 * @returns {object|null} Mechanism info or null
 */
export function getMechanismInfo(mechanismId) {
  const mechanisms = {
    '001': {
      id: '001',
      name: 'FCFS',
      fullName: 'First Come First Served',
      category: 'CPU',
      description: 'Processes execute in the order they arrive in the ready queue. Non-preemptive scheduling algorithm.',
      keyMetrics: ['Wait Time', 'Turnaround Time', 'Response Time'],
      pros: ['Simple to understand and implement', 'Fair in arrival order', 'Minimal overhead'],
      cons: ['Convoy effect', 'Does not minimize average wait time', 'Unfair to short processes'],
      timeComplexity: 'O(n)',
      spaceComplexity: 'O(1)',
      bestCaseScenario: 'All processes have similar burst times',
      worstCaseScenario: 'Long process followed by many short processes',
      commonQuestions: [
        {
          question: 'What is the convoy effect?',
          answer: 'The convoy effect occurs when a long process blocks short processes behind it, causing them to wait unnecessarily.'
        },
        {
          question: 'Why is FCFS not optimal?',
          answer: 'Because it does not consider burst times. A short process must wait for a long process to complete.'
        }
      ],
      relatedConcepts: ['Context Switching', 'Scheduling', 'CPU Scheduling'],
      applications: ['Batch processing systems', 'Simple operating systems']
    },
    '002': {
      id: '002',
      name: 'SJF',
      fullName: 'Shortest Job First',
      category: 'CPU',
      description: 'Process with shortest burst time is scheduled first. Can be preemptive (SRTF) or non-preemptive.',
      keyMetrics: ['Wait Time', 'Turnaround Time', 'Burst Time'],
      pros: ['Minimizes average waiting time', 'Good for known burst times', 'Optimal for non-preemptive scheduling'],
      cons: ['Requires knowledge of future burst times', 'Can cause starvation of long processes', 'Difficult to predict burst times'],
      timeComplexity: 'O(n log n)',
      spaceComplexity: 'O(n)',
      bestCaseScenario: 'Burst times are known accurately',
      worstCaseScenario: 'Long process arrives with many short processes waiting',
      commonQuestions: [
        {
          question: 'How is burst time determined?',
          answer: 'Burst time is estimated based on historical data from previous process runs.'
        },
        {
          question: 'Can SJF cause starvation?',
          answer: 'Yes, long processes can be starved by a continuous stream of short processes.'
        }
      ],
      relatedConcepts: ['SRTF', 'Scheduling', 'Process Burst Time'],
      applications: ['Interactive systems', 'Time-sharing systems']
    },
    '005': {
      id: '005',
      name: 'RR',
      fullName: 'Round Robin',
      category: 'CPU',
      description: 'Each process gets a fixed time quantum. If not completed, it goes to the end of the queue.',
      keyMetrics: ['Wait Time', 'Turnaround Time', 'Time Quantum', 'Context Switches'],
      pros: ['Fair time distribution', 'Prevents starvation', 'Good for interactive systems'],
      cons: ['Context switching overhead', 'Quantum size selection is critical', 'Poor for batch processing'],
      timeComplexity: 'O(n)',
      spaceComplexity: 'O(n)',
      bestCaseScenario: 'All processes have similar burst times',
      worstCaseScenario: 'Large quantum with one long process, small quantum with many short processes',
      commonQuestions: [
        {
          question: 'What is the optimal time quantum?',
          answer: 'The optimal quantum is typically 80% of the average burst time, balancing fairness and overhead.'
        },
        {
          question: 'Why does context switching matter?',
          answer: 'Context switching has overhead (CPU time) for saving/restoring process state, affecting total execution time.'
        }
      ],
      relatedConcepts: ['Time Quantum', 'Context Switching', 'Fairness'],
      applications: ['Timesharing systems', 'Interactive systems', 'Modern operating systems']
    },
    '011': {
      id: '011',
      name: 'First Fit',
      fullName: 'First Fit Memory Allocation',
      category: 'Memory',
      description: 'Allocates memory in the first available block large enough for the process.',
      keyMetrics: ['External Fragmentation', 'Internal Fragmentation', 'Allocation Speed'],
      pros: ['Fast allocation (O(n))', 'Simple algorithm', 'Reasonably good fragmentation'],
      cons: ['May leave small unusable fragments', 'Not optimal memory usage'],
      timeComplexity: 'O(n)',
      spaceComplexity: 'O(1)',
      bestCaseScenario: 'Processes fit perfectly into available blocks',
      worstCaseScenario: 'Many small gaps created, wasting memory',
      commonQuestions: [
        {
          question: 'What is external fragmentation?',
          answer: 'External fragmentation is unused memory between allocated blocks that is too small to be used.'
        },
        {
          question: 'How does First Fit compare to Best Fit?',
          answer: 'First Fit is faster but leaves larger gaps. Best Fit is slower but creates smaller gaps.'
        }
      ],
      relatedConcepts: ['Memory Fragmentation', 'Memory Allocation', 'Compaction'],
      applications: ['Dynamic memory allocation', 'Operating system memory management']
    },
    '012': {
      id: '012',
      name: 'Best Fit',
      fullName: 'Best Fit Memory Allocation',
      category: 'Memory',
      description: 'Allocates memory in the smallest block that fits the process.',
      keyMetrics: ['External Fragmentation', 'Internal Fragmentation', 'Allocation Speed'],
      pros: ['Minimizes external fragmentation', 'Good memory utilization', 'Reduces wasted space'],
      cons: ['Slower than First Fit (O(n))', 'Still produces fragmentation', 'Must scan all blocks'],
      timeComplexity: 'O(n)',
      spaceComplexity: 'O(1)',
      bestCaseScenario: 'Optimal matching of process size to block size',
      worstCaseScenario: 'Many blocks of varying sizes creating complex fragmentation',
      commonQuestions: [
        {
          question: 'Why is Best Fit slower?',
          answer: 'Best Fit must search through all available blocks to find the smallest fitting one, while First Fit stops at the first match.'
        },
        {
          question: 'Does Best Fit prevent fragmentation completely?',
          answer: 'No, it reduces fragmentation but cannot prevent it entirely. Some wasted space is still created.'
        }
      ],
      relatedConcepts: ['Memory Fragmentation', 'First Fit', 'Worst Fit'],
      applications: ['Memory management systems', 'Embedded systems']
    },
    '013': {
      id: '013',
      name: 'Worst Fit',
      fullName: 'Worst Fit Memory Allocation',
      category: 'Memory',
      description: 'Allocates memory in the largest available block.',
      keyMetrics: ['External Fragmentation', 'Internal Fragmentation', 'Allocation Speed'],
      pros: ['Leaves larger blocks for future allocations', 'Can reduce fragmentation', 'Fair distribution'],
      cons: ['Slower than First Fit', 'May not be practical', 'Large fragments can be wasted'],
      timeComplexity: 'O(n)',
      spaceComplexity: 'O(1)',
      bestCaseScenario: 'Many medium-sized processes can fit in remaining space',
      worstCaseScenario: 'Large wasted blocks that cannot be used',
      commonQuestions: [
        {
          question: 'When is Worst Fit useful?',
          answer: 'Worst Fit is useful when you want to keep large contiguous blocks available for larger processes.'
        },
        {
          question: 'Why is Worst Fit rarely used?',
          answer: 'In practice, Worst Fit performs worse than First Fit or Best Fit and uses significant overhead searching.'
        }
      ],
      relatedConcepts: ['Memory Allocation', 'First Fit', 'Best Fit'],
      applications: ['Memory management research', 'Specialized allocation schemes']
    },
    '021': {
      id: '021',
      name: 'FIFO',
      fullName: 'FIFO (First In First Out) Page Replacement',
      category: 'Page',
      description: 'Replaces the page that has been in memory the longest.',
      keyMetrics: ['Page Faults', 'Page Hits', 'Hit Rate', 'Miss Rate'],
      pros: ['Simple to implement', 'Low overhead', 'Easy to understand'],
      cons: ['Poor performance', 'Susceptible to Belady\'s anomaly', 'Ignores page usage frequency'],
      timeComplexity: 'O(1)',
      spaceComplexity: 'O(n)',
      bestCaseScenario: 'Sequential page access pattern',
      worstCaseScenario: 'Frequently used pages enter early and get replaced',
      commonQuestions: [
        {
          question: 'What is Belady\'s anomaly?',
          answer: 'Belady\'s anomaly occurs when increasing the number of page frames increases page faults in FIFO.'
        },
        {
          question: 'Why is FIFO not optimal?',
          answer: 'FIFO does not consider page usage patterns, so it may replace frequently used pages.'
        }
      ],
      relatedConcepts: ['Page Replacement', 'Belady\'s Anomaly', 'Virtual Memory'],
      applications: ['Virtual memory systems', 'Cache replacement policies']
    },
    '023': {
      id: '023',
      name: 'LRU',
      fullName: 'LRU (Least Recently Used) Page Replacement',
      category: 'Page',
      description: 'Replaces the page that has not been used for the longest time.',
      keyMetrics: ['Page Faults', 'Page Hits', 'Hit Rate', 'Miss Rate'],
      pros: ['Good performance', 'Considers page usage patterns', 'Respects locality principle'],
      cons: ['Expensive to implement', 'Requires tracking page accesses', 'Overhead for frequent updates'],
      timeComplexity: 'O(log n)',
      spaceComplexity: 'O(n)',
      bestCaseScenario: 'Programs with good temporal locality',
      worstCaseScenario: 'All pages accessed equally, no locality',
      commonQuestions: [
        {
          question: 'Why is LRU better than FIFO?',
          answer: 'LRU considers usage patterns and keeps frequently accessed pages in memory, while FIFO ignores usage.'
        },
        {
          question: 'How is LRU typically implemented?',
          answer: 'With a timestamp on each page access, or a linked list to track page access order.'
        }
      ],
      relatedConcepts: ['Page Replacement', 'Locality of Reference', 'Virtual Memory'],
      applications: ['Modern operating systems', 'CPU cache replacement', 'Virtual memory systems']
    },
    '041': {
      id: '041',
      name: 'FCFS',
      fullName: 'FCFS (First Come First Served) Disk Scheduling',
      category: 'Disk',
      description: 'Disk requests are serviced in the order they arrive in the queue.',
      keyMetrics: ['Seek Time', 'Average Seek Time', 'Total Seek Time', 'Arm Movement'],
      pros: ['Simple to implement', 'Fair to all requests', 'No starvation'],
      cons: ['Causes excessive arm movement', 'Poor performance', 'High seek time'],
      timeComplexity: 'O(1)',
      spaceComplexity: 'O(n)',
      bestCaseScenario: 'Sequential disk requests',
      worstCaseScenario: 'Requests alternating between outer and inner cylinders',
      commonQuestions: [
        {
          question: 'Why is FCFS disk scheduling poor?',
          answer: 'FCFS causes the disk arm to move back and forth across the disk unnecessarily, wasting time in seek operations.'
        },
        {
          question: 'What causes excessive arm movement?',
          answer: 'Requests arriving from different cylinders force the arm to move long distances between requests.'
        }
      ],
      relatedConcepts: ['Disk Scheduling', 'Seek Time', 'Cylinder'],
      applications: ['Early disk systems', 'Simple storage controllers']
    },
    '043': {
      id: '043',
      name: 'C-SCAN',
      fullName: 'C-SCAN (Circular SCAN) Disk Scheduling',
      category: 'Disk',
      description: 'Disk arm moves in one direction until the end, then returns to the beginning and repeats.',
      keyMetrics: ['Seek Time', 'Average Seek Time', 'Uniform Wait Time'],
      pros: ['Uniform wait time', 'Better performance than SCAN', 'Prevents starvation'],
      cons: ['More complex than FCFS', 'Unequal performance at track boundaries'],
      timeComplexity: 'O(n log n)',
      spaceComplexity: 'O(n)',
      bestCaseScenario: 'Requests uniformly distributed across disk',
      worstCaseScenario: 'All requests at one end of disk',
      commonQuestions: [
        {
          question: 'How is C-SCAN different from SCAN?',
          answer: 'C-SCAN treats the disk as circular, returning to the beginning instead of reversing direction like SCAN.'
        },
        {
          question: 'What is the advantage of uniform wait time?',
          answer: 'Uniform wait time means all processes experience similar latency, providing predictable performance.'
        }
      ],
      relatedConcepts: ['SCAN Algorithm', 'Disk Scheduling', 'Seek Time'],
      applications: ['Modern disk controllers', 'Storage area networks']
    }
  };

  return mechanisms[mechanismId] || null;
}

/**
 * Get human-readable name for mechanism
 *
 * @param {string} mechanismId - Mechanism ID
 * @returns {string|null} Mechanism name or null
 */
export function getMechanismName(mechanismId) {
  const info = getMechanismInfo(mechanismId);
  return info ? info.fullName : null;
}

/**
 * Check if context is valid and sufficient
 *
 * @param {object} context - Context object from getChatbotContext()
 * @returns {boolean} True if context is valid
 */
export function hasValidContext(context = null) {
  if (!context) {
    context = getChatbotContext();
  }

  // Must have mechanism ID
  if (!context.mechanism) return false;

  // Category must be recognized
  if (!context.category) return false;

  // Context timestamp must be recent (within 30 seconds)
  const now = Date.now();
  if (now - context.timestamp > 30000) return false;

  return true;
}

/**
 * Get human-readable context summary for debugging
 *
 * @returns {string} Formatted context summary
 */
export function getContextSummary() {
  const context = getChatbotContext();
  const enhanced = getEnhancedContext();

  return `
=== Chatbot Context Summary ===
Mechanism: m-${context.mechanism} (${context.mechanismName})
Category: ${context.category}
Mode: ${context.mode}
Animation: ${context.animationState.isPlaying ? 'Playing' : 'Paused'} (Step ${context.currentStep}/${context.totalSteps})
Input Data: ${context.hasInputData ? '✓ Available' : '✗ None'}
Output Data: ${context.hasOutputData ? '✓ Available' : '✗ None'}
Context Quality: ${enhanced.contextQuality}
Valid: ${hasValidContext(context) ? '✓ Yes' : '✗ No'}
  `;
}

/**
 * Log detailed context for debugging (enable with ?debug=1)
 */
export function logContext() {
  const isDebugMode = new URLSearchParams(window.location.search).has('debug');
  if (!isDebugMode) return;

  const context = getChatbotContext();
  const enhanced = getEnhancedContext();

  console.group('[Chatbot Context]');
  console.log('Basic Context:', context);
  console.log('Enhanced Context:', enhanced);
  console.log('Summary:', getContextSummary());
  console.groupEnd();
}

/**
 * Determine overall quality of extracted context
 *
 * @private
 */
function determineContextQuality(context, mechanismInfo) {
  if (context.mechanism && mechanismInfo && context.animationState && context.currentStep) {
    return 'full';
  }
  if (context.mechanism && mechanismInfo) {
    return 'partial';
  }
  return 'minimal';
}

/**
 * Generate chatbot suggestions based on context
 *
 * @private
 */
function generateSuggestions(context, mechanismInfo) {
  const suggestions = [];

  if (!mechanismInfo) {
    suggestions.push('Ask me about any OS algorithm concept');
  } else {
    suggestions.push(`Ask me about ${context.mechanismName}`);
    suggestions.push(`I can explain ${context.mechanismName}'s pros and cons`);
    if (context.category) {
      suggestions.push(`Compare ${context.mechanismName} with other ${context.category} algorithms`);
    }
  }

  if (context.animationState.isPlaying) {
    suggestions.push('Ask me what\'s happening at this step');
  }

  return suggestions;
}
