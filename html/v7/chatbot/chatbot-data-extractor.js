/**
 * Chatbot Data Extractor
 *
 * Extracts actual input/output data from mechanism pages
 * Formats data into human-readable context for the chatbot
 */

/**
 * Extract mechanism data from the current page
 * Tries multiple sources in priority order
 *
 * @returns {object} - Structured data with input, output, and formatted context
 */
export function extractMechanismData() {
  const data = {
    hasData: false,
    rawInput: null,
    rawOutput: null,
    formattedInput: '',
    formattedOutput: '',
    currentStep: null,
    isAnimating: false
  };

  console.log('[ChatbotDataExtractor] Starting data extraction...');

  try {
    // Priority 1: Try global data bridge (NEW - recommended)
    let input = null;
    let output = null;
    let mid = null;

    console.log('[ChatbotDataExtractor] Priority 1: Checking window.__visosData...');
    if (window.__visosData) {
      console.log('[ChatbotDataExtractor] ✓ Found window.__visosData:', window.__visosData);
      input = window.__visosData.input;
      output = window.__visosData.output;
      mid = window.__visosData.mid;
    } else {
      console.log('[ChatbotDataExtractor] ✗ window.__visosData not found');
    }

    // Priority 2: Try direct window globals (LEGACY)
    console.log('[ChatbotDataExtractor] Priority 2: Checking direct window globals...');
    if (!input && window.input) {
      console.log('[ChatbotDataExtractor] ✓ Found window.input:', window.input);
      input = window.input;
    }

    if (!output && window.output) {
      console.log('[ChatbotDataExtractor] ✓ Found window.output:', window.output);
      output = window.output;
    }

    if (!mid && window.mid) {
      console.log('[ChatbotDataExtractor] ✓ Found window.mid:', window.mid);
      mid = window.mid;
    }

    // Get mechanism ID if not yet obtained
    if (!mid) {
      console.log('[ChatbotDataExtractor] Extracting mechanism ID from URL...');
      const urlMatch = window.location.pathname.match(/\/m-(\d{3})/);
      if (urlMatch) {
        mid = urlMatch[1];
        console.log('[ChatbotDataExtractor] ✓ Extracted mid from URL:', mid);
      }
    }

    console.log('[ChatbotDataExtractor] Final mechanism ID:', mid);

    // Check if we have valid input data
    console.log('[ChatbotDataExtractor] Checking input data...');
    if (input && (Array.isArray(input) || typeof input === 'object') && getDataLength(input) > 0) {
      console.log('[ChatbotDataExtractor] ✓ Input data found, length:', getDataLength(input));
      data.rawInput = input;
      data.formattedInput = formatInputData(input, mid);
      console.log('[ChatbotDataExtractor] Formatted Input:', data.formattedInput);
      data.hasData = true;
    } else {
      console.log('[ChatbotDataExtractor] ✗ No valid input data found');
    }

    // Check if we have valid output data
    console.log('[ChatbotDataExtractor] Checking output data...');
    if (output && (Array.isArray(output) || typeof output === 'object') && getDataLength(output) > 0) {
      console.log('[ChatbotDataExtractor] ✓ Output data found, length:', getDataLength(output));
      data.rawOutput = output;
      data.formattedOutput = formatOutputData(output, mid);
      console.log('[ChatbotDataExtractor] Formatted Output:', data.formattedOutput);
      data.hasData = true;
    } else {
      console.log('[ChatbotDataExtractor] ✗ No valid output data found');
    }

    // Get current animation step if available
    if (window.currStep !== undefined) {
      data.currentStep = window.currStep;
      console.log('[ChatbotDataExtractor] Current step:', data.currentStep);
    }

    // Get animation state
    if (window.paused !== undefined) {
      data.isAnimating = !window.paused;
      console.log('[ChatbotDataExtractor] Animation state (isAnimating):', data.isAnimating);
    }

    console.log('[ChatbotDataExtractor] ✓ Extraction complete. Has data:', data.hasData);

  } catch (error) {
    console.warn('[Chatbot DataExtractor] Error extracting data:', error);
  }

  return data;
}

/**
 * Get length of data object (handles arrays and objects)
 * @private
 */
function getDataLength(data) {
  if (!data) return 0;
  if (Array.isArray(data)) return data.length;
  if (typeof data === 'object' && data.length !== undefined) return data.length;
  if (typeof data === 'object') return Object.keys(data).length;
  return 0;
}

/**
 * Format input data based on mechanism type
 *
 * @param {array} input - Input data array
 * @param {string} mechanismId - Mechanism ID (001-049)
 * @returns {string} - Formatted human-readable input
 */
function formatInputData(input, mechanismId) {
  if (!input || input.length === 0) return '';

  const mid = String(mechanismId).padStart(3, '0');
  const type = getMechanismType(mid);

  try {
    switch (type) {
      case 'cpu':
        return formatCPUInput(input);
      case 'memory':
        return formatMemoryInput(input);
      case 'page':
        return formatPageInput(input);
      case 'disk':
        return formatDiskInput(input);
      case 'file':
        return formatFileInput(input);
      default:
        return formatGenericInput(input);
    }
  } catch (error) {
    console.warn('[Chatbot DataExtractor] Error formatting input:', error);
    return formatGenericInput(input);
  }
}

/**
 * Format output data based on mechanism type
 *
 * @param {array} output - Output data array
 * @param {string} mechanismId - Mechanism ID
 * @returns {string} - Formatted human-readable output
 */
function formatOutputData(output, mechanismId) {
  if (!output || output.length === 0) return '';

  const mid = String(mechanismId).padStart(3, '0');
  const type = getMechanismType(mid);

  try {
    switch (type) {
      case 'cpu':
        return formatCPUOutput(output);
      case 'memory':
        return formatMemoryOutput(output);
      case 'page':
        return formatPageOutput(output);
      case 'disk':
        return formatDiskOutput(output);
      case 'file':
        return formatFileOutput(output);
      default:
        return formatGenericOutput(output);
    }
  } catch (error) {
    console.warn('[Chatbot DataExtractor] Error formatting output:', error);
    return formatGenericOutput(output);
  }
}

/**
 * Determine mechanism type from ID
 */
function getMechanismType(mechanismId) {
  const id = parseInt(mechanismId);
  if (id >= 1 && id <= 8) return 'cpu';
  if (id >= 11 && id <= 19) return 'memory';
  if (id >= 21 && id <= 29) return 'page';
  if (id >= 31 && id <= 39) return 'file';
  if (id >= 41 && id <= 49) return 'disk';
  return 'unknown';
}

/**
 * CPU Scheduling Input Formatting
 * Handles both object format: { pid, arrival, burst, priority }
 * AND array format: [arrival_time, burst_time, priority, ...]
 */
function formatCPUInput(input) {
  let formatted = 'Input Processes:\n';

  if (!Array.isArray(input)) {
    return 'Unable to format CPU input data';
  }

  input.forEach((proc, idx) => {
    const processId = idx + 1;

    // Handle object format (preferred): { pid, arrival, burst, priority }
    if (typeof proc === 'object' && proc !== null) {
      const arrivalTime = proc.arrival !== undefined ? proc.arrival : (proc[0] || '?');
      const burstTime = proc.burst !== undefined ? proc.burst : (proc[1] || '?');
      const priority = proc.priority !== undefined ? proc.priority : (proc[2] || '?');

      formatted += `Process ${processId}: Arrival Time=${arrivalTime}, Burst Time=${burstTime}, Priority=${priority}\n`;
    } else {
      // Handle array format (legacy)
      const arrivalTime = proc[0] !== undefined ? proc[0] : '?';
      const burstTime = proc[1] !== undefined ? proc[1] : '?';
      const priority = proc[2] !== undefined ? proc[2] : '?';

      formatted += `Process ${processId}: Arrival Time=${arrivalTime}, Burst Time=${burstTime}, Priority=${priority}\n`;
    }
  });

  return formatted.trim();
}

/**
 * CPU Scheduling Output Formatting
 */
function formatCPUOutput(output) {
  let formatted = 'Execution Results:\n';

  if (Array.isArray(output) && output.length > 0) {
    // If output contains calculated values
    if (output[0].waitingTime !== undefined || output[0].turnaroundTime !== undefined) {
      output.forEach((result, idx) => {
        const processId = idx + 1;
        const wait = result.waitingTime !== undefined ? result.waitingTime : '?';
        const turnaround = result.turnaroundTime !== undefined ? result.turnaroundTime : '?';
        formatted += `Process ${processId}: Waiting Time=${wait}, Turnaround Time=${turnaround}\n`;
      });
    }
  }

  return formatted.trim() || 'Execution data available';
}

/**
 * Memory Allocation Input Formatting
 * Handles object format: { memSlots: [{start, end}, ...], processes: [{id, size}, ...] }
 * AND array format with slot count as first element
 */
function formatMemoryInput(input) {
  let formatted = 'Memory Configuration:\n';

  // Handle object format (preferred): { memSlots: [...], processes: [...] }
  if (typeof input === 'object' && !Array.isArray(input)) {
    let formatted_slots = '';
    let formatted_procs = '';

    if (input.memSlots && Array.isArray(input.memSlots)) {
      formatted_slots = 'Memory Slots:\n';
      input.memSlots.forEach((slot, idx) => {
        if (typeof slot === 'object' && slot.start !== undefined && slot.end !== undefined) {
          const size = slot.end - slot.start;
          formatted_slots += `Slot ${idx + 1}: Start=${slot.start}, End=${slot.end}, Size=${size} KB\n`;
        }
      });
    }

    if (input.processes && Array.isArray(input.processes)) {
      formatted_procs = 'Processes:\n';
      input.processes.forEach((proc, idx) => {
        if (typeof proc === 'object' && proc.id !== undefined && proc.size !== undefined) {
          formatted_procs += `Process ${proc.id}: Size=${proc.size} KB\n`;
        }
      });
    }

    if (formatted_slots) formatted += formatted_slots + '\n';
    if (formatted_procs) formatted += formatted_procs;
    return formatted.trim();
  }

  // Handle array format (legacy)
  if (Array.isArray(input)) {
    let slotCount = 0;
    let formatted_slots = '';
    let formatted_procs = '';

    input.forEach((item, idx) => {
      if (typeof item === 'number') {
        if (idx === 0) {
          slotCount = item;
        } else if (idx <= slotCount) {
          formatted_slots += `Slot ${idx}: ${item}\n`;
        } else {
          formatted_procs += `Process: ${item}\n`;
        }
      }
    });

    if (formatted_slots) formatted += 'Memory Slots:\n' + formatted_slots + '\n';
    if (formatted_procs) formatted += 'Processes:\n' + formatted_procs;
    return formatted.trim();
  }

  return 'Unable to format Memory input data';
}

/**
 * Memory Allocation Output Formatting
 */
function formatMemoryOutput(output) {
  let formatted = 'Allocation Results:\n';

  output.forEach((alloc, idx) => {
    const start = alloc[0] !== undefined ? alloc[0] : '?';
    const end = alloc[1] !== undefined ? alloc[1] : '?';
    const processId = alloc[2] !== undefined ? alloc[2] : '?';
    formatted += `Allocation ${idx + 1}: Address ${start}-${end}, Process ${processId}\n`;
  });

  return formatted.trim();
}

/**
 * Page Replacement Input Formatting
 * Handles array format: [0, 1, 2, 3, ...]
 * OR comma-separated string
 */
function formatPageInput(input) {
  let formatted = 'Page Reference String:\n';

  if (Array.isArray(input)) {
    if (input.length > 0) {
      // Extract page numbers, filtering out objects
      const pageNumbers = input
        .filter(item => typeof item === 'number')
        .join(', ');

      if (pageNumbers) {
        formatted += pageNumbers;
      } else {
        // If array contains non-numbers, just stringify first element
        formatted += input[0].toString();
      }
    }
  } else if (typeof input === 'string') {
    formatted += input;
  }

  return formatted.trim() || 'Page reference data available';
}

/**
 * Page Replacement Output Formatting
 */
function formatPageOutput(output) {
  let formatted = 'Page Fault Analysis:\n';

  if (Array.isArray(output)) {
    output.forEach((item, idx) => {
      if (typeof item === 'number') {
        formatted += `Step ${idx + 1}: Page ${item}\n`;
      }
    });
  }

  return formatted.trim() || 'Page replacement data available';
}

/**
 * Disk Scheduling Input Formatting
 */
function formatDiskInput(input) {
  let formatted = 'Disk Requests:\n';

  input.forEach((req, idx) => {
    formatted += `Request ${idx + 1}: Cylinder ${req}\n`;
  });

  return formatted.trim();
}

/**
 * Disk Scheduling Output Formatting
 */
function formatDiskOutput(output) {
  let formatted = 'Head Movement:\n';

  output.forEach((movement, idx) => {
    formatted += `Move ${idx + 1}: ${movement}\n`;
  });

  return formatted.trim();
}

/**
 * File Allocation Input Formatting
 */
function formatFileInput(input) {
  let formatted = 'File Allocation Input:\n';
  formatted += input.toString();
  return formatted;
}

/**
 * File Allocation Output Formatting
 */
function formatFileOutput(output) {
  let formatted = 'File Allocation Output:\n';
  formatted += output.toString();
  return formatted;
}

/**
 * Generic Input Formatting (fallback)
 */
function formatGenericInput(input) {
  let formatted = 'Input Data:\n';

  input.forEach((item, idx) => {
    if (Array.isArray(item)) {
      formatted += `Item ${idx + 1}: ${item.join(', ')}\n`;
    } else {
      formatted += `Item ${idx + 1}: ${item}\n`;
    }
  });

  return formatted.trim();
}

/**
 * Generic Output Formatting (fallback)
 */
function formatGenericOutput(output) {
  let formatted = 'Output Data:\n';

  output.forEach((item, idx) => {
    if (Array.isArray(item)) {
      formatted += `Result ${idx + 1}: ${item.join(', ')}\n`;
    } else if (typeof item === 'object') {
      formatted += `Result ${idx + 1}: ${JSON.stringify(item)}\n`;
    } else {
      formatted += `Result ${idx + 1}: ${item}\n`;
    }
  });

  return formatted.trim();
}

/**
 * Extract comparison page data
 * Looks for tables with algorithm results
 *
 * @returns {string} - Formatted comparison data
 */
export function extractComparisonData() {
  let comparisonData = '';

  try {
    // Look for comparison results table
    const resultsTables = document.querySelectorAll('table');

    if (resultsTables.length > 0) {
      // Usually the last table is the comparison results
      const table = resultsTables[resultsTables.length - 1];
      comparisonData = formatTableAsText(table);
    }
  } catch (error) {
    console.warn('[Chatbot DataExtractor] Error extracting comparison data:', error);
  }

  return comparisonData;
}

/**
 * Convert HTML table to readable text format
 *
 * @param {HTMLTableElement} table - HTML table element
 * @returns {string} - Formatted table as text
 */
function formatTableAsText(table) {
  let formatted = 'Comparison Results:\n';

  try {
    // Get headers
    const headers = [];
    const headerCells = table.querySelectorAll('thead th, thead td');
    headerCells.forEach(cell => {
      headers.push(cell.textContent.trim());
    });

    // Get body rows
    const bodyRows = table.querySelectorAll('tbody tr');
    bodyRows.forEach((row, rowIdx) => {
      const cells = row.querySelectorAll('td, th');
      const rowData = [];
      cells.forEach(cell => {
        rowData.push(cell.textContent.trim());
      });

      if (rowData.length > 0) {
        if (headers.length === rowData.length) {
          // Create key-value pairs
          formatted += `\nRow ${rowIdx + 1}:\n`;
          headers.forEach((header, idx) => {
            formatted += `  ${header}: ${rowData[idx]}\n`;
          });
        } else {
          formatted += rowData.join(' | ') + '\n';
        }
      }
    });
  } catch (error) {
    console.warn('[Chatbot DataExtractor] Error formatting table:', error);
  }

  return formatted.trim();
}

/**
 * Get all available data context as a single string
 * This is what gets passed to the system prompt
 *
 * @returns {string} - Complete data context
 */
export function getDataContext() {
  console.log('[ChatbotDataExtractor] Building data context string...');
  const mechanismData = extractMechanismData();
  let context = '';

  if (mechanismData.hasData) {
    console.log('[ChatbotDataExtractor] ✓ Mechanism data available, building context...');
    context += 'DATA CONTEXT:\n';
    context += '=============\n';

    if (mechanismData.formattedInput) {
      context += mechanismData.formattedInput + '\n\n';
    }

    if (mechanismData.formattedOutput) {
      context += mechanismData.formattedOutput + '\n\n';
    }

    if (mechanismData.currentStep !== null) {
      context += `Current Step: ${mechanismData.currentStep}\n`;
    }

    if (mechanismData.isAnimating) {
      context += 'Animation State: Currently playing\n';
    }

    console.log('[ChatbotDataExtractor] ✓ Data context built:', context);
  } else {
    console.log('[ChatbotDataExtractor] ✗ No mechanism data available, returning empty context');
  }

  return context;
}
