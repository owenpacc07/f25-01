# Chatbot Context Extraction Module

## Overview

The context extraction module (`chatbot-context.js`) is responsible for detecting and extracting all relevant information about the current page state so the chatbot can provide mechanism-specific, context-aware responses. This enables the AI to understand exactly which algorithm the user is viewing, what mode it's in, and what data is currently being displayed.

## Module Architecture

### Core Functions

#### `getChatbotContext()`
**Purpose:** Main function that extracts all available context from the current page.

**Returns:** Object with the following properties:
```javascript
{
  mechanism: string,        // e.g., "001", "011", "021"
  mechanismName: string,    // e.g., "FCFS", "First Fit", "FIFO"
  category: string,         // e.g., "CPU", "Memory", "Page", "File", "Disk"
  mode: string,             // e.g., "core", "core-a", "core-e", "core-s", "core-c"
  animationState: {
    isPlaying: boolean,     // Is animation currently playing?
    isPaused: boolean,      // Is animation paused?
    isStepping: boolean,    // Are we in step-by-step mode?
  },
  currentStep: number|null, // Current step in algorithm execution
  totalSteps: number|null,  // Total steps in algorithm
  hasInputData: boolean,    // Is input data available?
  hasOutputData: boolean,   // Is output/result data available?
  urlPath: string,          // Current page URL path
  timestamp: number,        // When context was extracted
  debugInfo: object         // Additional debug information
}
```

**Example Usage:**
```javascript
const context = getChatbotContext();
console.log(`User is viewing ${context.mechanismName} in ${context.category} scheduling`);
console.log(`Current step: ${context.currentStep} of ${context.totalSteps}`);
```

---

#### `extractMechanismId()`
**Purpose:** Extract the mechanism ID from the current page.

**Returns:** String mechanism ID (e.g., "001", "011", "021") or `null`

**Extraction Order:**
1. Reads `window.mid` global variable (set by PHP template)
2. Parses from URL using regex pattern: `/m-(\d{3})/`
3. Checks `data-mechanism-id` attribute on body element
4. Checks `localStorage` for fallback value

**Example Usage:**
```javascript
const mid = extractMechanismId(); // "001"
```

---

#### `detectMechanismCategory()`
**Purpose:** Categorize the mechanism based on its ID.

**Returns:** String category name: `"CPU"`, `"Memory"`, `"Page"`, `"File"`, `"Disk"`, or `null`

**Categorization Logic:**
- **001-008**: CPU Scheduling algorithms
- **011-013**: Memory Allocation algorithms
- **021-025**: Page Replacement algorithms
- **031-033**: File Allocation algorithms
- **041-045**: Disk Scheduling algorithms

**Example Usage:**
```javascript
const category = detectMechanismCategory(); // "Memory" for m-011
```

---

#### `detectCurrentMode()`
**Purpose:** Detect which variation of the algorithm is being viewed.

**Returns:** String mode: `"core"`, `"core-a"`, `"core-c"`, `"core-e"`, `"core-s"`, or `null`

**Extraction Method:**
- Parses URL path using regex: `/\/(core(?:-[a-z])?)\//`
- Different modes may represent different pedagogical approaches or visualizations

**Supported Modes:**
- `core` - Standard/core algorithm implementation
- `core-a` - Variant A (pedagogical variation)
- `core-c` - Variant C (pedagogical variation)
- `core-e` - Variant E (extended features)
- `core-s` - Variant S (simplified version)

**Example Usage:**
```javascript
const mode = detectCurrentMode(); // "core-a" if viewing /core-a/m-001/
```

---

#### `extractAnimationState()`
**Purpose:** Determine the current state of the algorithm visualization animation.

**Returns:** Object with boolean flags:
```javascript
{
  isPlaying: boolean,   // Animation is currently playing
  isPaused: boolean,    // Animation is paused
  isStepping: boolean   // In step-by-step mode
}
```

**Detection Methods:**
1. Checks `window.paused` global variable
2. Checks state of play/pause buttons in DOM
3. Checks `data-animation-state` attribute
4. Falls back to checking button disable states

**Example Usage:**
```javascript
const animState = extractAnimationState();
if (animState.isPlaying) {
  console.log('Animation is currently running');
}
```

---

#### `extractCurrentStep()`
**Purpose:** Get the current step/iteration of the algorithm execution.

**Returns:** Number or `null`

**Extraction Sources:**
1. `window.currStep` global variable
2. `window.nextBurst` for CPU scheduling algorithms
3. Text content of step counter elements
4. Index of highlighted element in animation table

**Example Usage:**
```javascript
const step = extractCurrentStep(); // 3 (processing step 3)
```

---

#### `extractTotalSteps()`
**Purpose:** Get the total number of steps in the algorithm execution.

**Returns:** Number or `null`

**Extraction Sources:**
1. Length of `window.output` array
2. Count of rows in animation table
3. Length of `window.input` array
4. Number of processes/pages/blocks in animation

**Example Usage:**
```javascript
const totalSteps = extractTotalSteps(); // 15 (algorithm has 15 total steps)
```

---

#### `extractInputData()`
**Purpose:** Extract the input data currently being processed by the algorithm.

**Returns:** Object with algorithm-specific input properties or `null`

**For CPU Scheduling Mechanisms:**
```javascript
{
  processes: [
    { id: "P1", arrivalTime: 0, burstTime: 8, priority: 0 },
    { id: "P2", arrivalTime: 1, burstTime: 4, priority: 0 },
    // ... more processes
  ],
  processCount: number
}
```

**For Memory Allocation Mechanisms:**
```javascript
{
  memoryBlocks: [
    { id: 1, size: 200 },
    { id: 2, size: 100 },
    // ... more blocks
  ],
  allocationSize: number,
  blockCount: number
}
```

**For Page Replacement Mechanisms:**
```javascript
{
  referenceString: [1, 2, 3, 2, 1, 5, 2, 1, 6, ...],
  pageCount: number,
  frameCount: number
}
```

**Extraction Sources:**
1. `window.input` global variable (primary)
2. `window.processData` for scheduling
3. `window.memoryData` for memory algorithms
4. `window.pageData` for page replacement
5. Parsed from table cells in page HTML

**Example Usage:**
```javascript
const inputData = extractInputData();
console.log(`${inputData.processes.length} processes to schedule`);
```

---

#### `extractOutputData()`
**Purpose:** Extract the calculated results/output from the algorithm execution.

**Returns:** Object with algorithm-specific output properties or `null`

**For CPU Scheduling:**
```javascript
{
  ganttChart: [
    { process: "P1", startTime: 0, endTime: 8 },
    { process: "P2", startTime: 8, endTime: 12 },
    // ... more entries
  ],
  metrics: {
    avgWaitingTime: 6.5,
    avgTurnaroundTime: 12.5,
    avgResponseTime: 4.25,
    contextSwitches: 2
  }
}
```

**For Memory Allocation:**
```javascript
{
  allocation: [
    { process: "P1", blockId: 1, offset: 0, size: 100 },
    { process: "P2", blockId: 2, offset: 0, size: 50 },
    // ... more allocations
  ],
  externalFragmentation: 45,
  internalFragmentation: 23,
  allocationSuccess: true
}
```

**For Page Replacement:**
```javascript
{
  timeline: [
    { time: 0, page: 1, hit: false, frames: [1] },
    { time: 1, page: 2, hit: false, frames: [1, 2] },
    // ... more entries
  ],
  metrics: {
    pageHits: 8,
    pageFaults: 12,
    hitRate: 0.4,
    faultRate: 0.6
  }
}
```

**Extraction Sources:**
1. `window.output` global variable (primary)
2. `window.results` for final output
3. `window.animationData` for time-based output
4. Parsed from result tables in DOM
5. Calculated from metrics displayed on page

**Example Usage:**
```javascript
const outputData = extractOutputData();
console.log(`Average wait time: ${outputData.metrics.avgWaitingTime}ms`);
```

---

#### `getMechanismInfo(mechanismId)`
**Purpose:** Get detailed metadata about a specific mechanism.

**Parameters:**
- `mechanismId` (string): The mechanism ID (e.g., "001", "011")

**Returns:** Object with mechanism details or `null` if not found:
```javascript
{
  id: string,                    // "001"
  name: string,                  // "FCFS"
  fullName: string,              // "First Come First Served"
  category: string,              // "CPU"
  description: string,           // Detailed description
  keyMetrics: string[],          // ["Wait Time", "Turnaround Time", ...]
  pros: string[],                // Advantages of algorithm
  cons: string[],                // Disadvantages of algorithm
  timeComplexity: string,        // "O(n)"
  spaceComplexity: string,       // "O(1)"
  bestCaseScenario: string,      // When algorithm performs best
  worstCaseScenario: string,     // When algorithm performs poorly
  commonQuestions: [             // FAQ for this algorithm
    {
      question: string,
      answer: string
    },
    // ... more questions
  ],
  relatedConcepts: string[],     // Related algorithms/concepts
  applications: string[]         // Real-world applications
}
```

**Populated Mechanisms (Current Coverage):**
- **m-001**: FCFS (First Come First Served) - CPU Scheduling
- **m-002**: SJF (Shortest Job First) - CPU Scheduling
- **m-005**: RR (Round Robin) - CPU Scheduling
- **m-011**: First Fit - Memory Allocation
- **m-012**: Best Fit - Memory Allocation
- **m-013**: Worst Fit - Memory Allocation
- **m-021**: FIFO (First In First Out) - Page Replacement
- **m-023**: LRU (Least Recently Used) - Page Replacement
- **m-041**: FCFS - Disk Scheduling
- **m-043**: C-SCAN - Disk Scheduling

**Example Usage:**
```javascript
const info = getMechanismInfo("001");
console.log(info.fullName); // "First Come First Served"
console.log(info.pros);     // ["Simple to implement", "Fair", ...]
```

---

#### `getEnhancedContext()`
**Purpose:** Get comprehensive context combining basic extraction with mechanism metadata.

**Returns:** Enhanced context object:
```javascript
{
  // All properties from getChatbotContext()
  ...getChatbotContext(),

  // Additional metadata from getMechanismInfo()
  mechanismInfo: {
    name: string,
    fullName: string,
    description: string,
    keyMetrics: string[],
    pros: string[],
    cons: string[],
    commonQuestions: object[]
  },

  // Computed availability flags
  dataAvailability: {
    inputDataAvailable: boolean,
    outputDataAvailable: boolean,
    animationRunning: boolean,
    stepByStepMode: boolean
  },

  // Context quality indicator
  contextQuality: "full" | "partial" | "minimal",

  // Suggestions for chatbot
  chatbotSuggestions: string[]
}
```

**Example Usage:**
```javascript
const enhanced = getEnhancedContext();

// Use mechanism info in chatbot prompt
const prompt = `User is learning about ${enhanced.mechanismInfo.fullName}.
They are at step ${enhanced.currentStep} of ${enhanced.totalSteps}.
Key concept: ${enhanced.mechanismInfo.keyMetrics[0]}`;

sendMessage(userQuestion, enhanced);
```

---

### Utility Functions

#### `hasValidContext()`
**Purpose:** Check if extracted context is sufficient for meaningful chatbot responses.

**Returns:** Boolean

**Validation Checks:**
- Mechanism ID is present and valid
- Category is recognized
- Context timestamp is recent (within 30 seconds)

**Example Usage:**
```javascript
if (hasValidContext()) {
  console.log('Ready to answer mechanism-specific questions');
} else {
  console.log('Context is incomplete, using generic responses');
}
```

---

#### `getContextSummary()`
**Purpose:** Get a human-readable summary of the current context for debugging.

**Returns:** String with formatted context information

**Example Output:**
```
=== Chatbot Context Summary ===
Mechanism: m-001 (FCFS)
Category: CPU Scheduling
Mode: core
Animation: Playing (Step 3/15)
Input Data: 4 processes loaded
Output Data: Gantt chart calculated
Context Quality: full
```

**Example Usage:**
```javascript
if (window.location.search.includes('debug')) {
  console.log(getContextSummary());
}
```

---

#### `logContext()`
**Purpose:** Output detailed context information to console for debugging purposes.

**Output:** Detailed logging of all extracted properties with indentation

**Example Usage:**
```javascript
// Enable debug logging with ?debug=1 URL parameter
import { logContext } from './chatbot-context.js';
logContext();
```

---

## Data Availability Patterns

### How Context is Extracted by Mechanism Type

#### CPU Scheduling (m-001 to m-008)
```
Input Source:  window.input or window.processData
              └─ Array of process objects with arrival time, burst time, priority

Output Source: window.output or animation results table
              └─ Gantt chart array with time slices
              └─ Metrics: wait time, turnaround time, response time, context switches

Animation:    Play/pause buttons in DOM
             └─ Current step via window.currStep or table highlighting
```

#### Memory Allocation (m-011 to m-013)
```
Input Source:  window.memoryData or memory block specification
              └─ Total memory, block sizes, allocation request size

Output Source: Allocation results table
              └─ Which blocks are allocated, where
              └─ Metrics: external fragmentation, internal fragmentation

Animation:    Step through allocation visualization
             └─ Shows available space, allocated space, fragmentation
```

#### Page Replacement (m-021 to m-025)
```
Input Source:  window.pageData or reference string input
              └─ Number of frames, reference string of page accesses

Output Source: Page table and statistics
              └─ Page hits vs misses per time step
              └─ Metrics: hit rate, miss rate, total faults

Animation:    Timeline visualization
             └─ Shows frame contents at each step
             └─ Highlights page hits and misses
```

#### File Allocation (m-031 to m-033)
```
Input Source:  File system structure
              └─ Files, directory structure, disk blocks

Output Source: Allocation map
              └─ Which blocks allocated to which files
              └─ Metrics: fragmentation, utilization
```

#### Disk Scheduling (m-041 to m-045)
```
Input Source:  window.diskData or request queue
              └─ Initial disk head position, queue of requests

Output Source: Head movement visualization and statistics
              └─ Total seek time, average seek time
              └─ Movement pattern over time
```

---

## Integration with Chatbot UI

### How ChatbotUI Uses Context

The `ChatbotUI` class automatically extracts and uses context when:

1. **User opens chat**: `getEnhancedContext()` is called to load mechanism info
2. **User sends message**: Context is passed to API with the message
3. **Chatbot responds**: Backend uses context to construct mechanism-specific system prompt

### Context Flow

```
User views mechanism page (m-001)
         ↓
ChatbotUI initializes
         ↓
getChatbotContext() extracts: id, category, mode, animation state
         ↓
getMechanismInfo() adds: algorithm description, pros/cons, FAQs
         ↓
User asks question "What is convoy effect?"
         ↓
sendMessage() passes enhanced context to /api/chatbot-stream.php
         ↓
Backend constructs 3-layer system prompt:
  1. Global instructions
  2. Category knowledge (CPU scheduling)
  3. Mechanism details (FCFS specifics)
         ↓
AI responds with context-aware answer about convoy effect
```

---

## Extending Context Extraction

### Adding Support for New Mechanisms

To add metadata for a new mechanism:

1. **Identify the mechanism ID and category** (e.g., m-007 = CPU)
2. **Add entry to mechanism metadata** in `getMechanismInfo()`:

```javascript
export function getMechanismInfo(mechanismId) {
  const mechanisms = {
    // ... existing mechanisms

    '007': {
      id: '007',
      name: 'Priority Scheduling',
      fullName: 'Priority Scheduling',
      category: 'CPU',
      description: 'CPU scheduling algorithm that assigns priority values to processes and executes highest priority first.',
      keyMetrics: ['Wait Time', 'Turnaround Time', 'Priority Inversion'],
      pros: [
        'Can prioritize critical processes',
        'Flexible - can implement different scheduling policies',
        'Suitable for real-time systems'
      ],
      cons: [
        'Can cause starvation of low-priority processes',
        'Priority inversion problem',
        'More complex than FCFS'
      ],
      timeComplexity: 'O(n)',
      spaceComplexity: 'O(n)',
      bestCaseScenario: 'All processes have similar priority',
      worstCaseScenario: 'High priority process blocks indefinitely',
      commonQuestions: [
        {
          question: 'What is priority inversion?',
          answer: 'When a lower-priority process holds a resource that a higher-priority process needs.'
        },
        {
          question: 'How do we prevent starvation?',
          answer: 'Aging technique - gradually increase priority of waiting processes.'
        }
      ],
      relatedConcepts: ['Context Switching', 'Preemption', 'Priority Queues'],
      applications: ['Real-time operating systems', 'Multimedia systems']
    }
  };

  return mechanisms[mechanismId] || null;
}
```

3. **Test context extraction** on the mechanism page:
   - Navigate to the mechanism: `/p/f25-01/v3/core/m-007/`
   - Open developer console
   - Call: `getChatbotContext()`
   - Verify all properties populate correctly

4. **Test with chatbot**:
   - Ask a question about the mechanism
   - Verify response reflects the metadata you added

### Adding New Context Sources

If you need to extract additional context:

1. **Identify data source** on the page (global variable, DOM element, etc.)
2. **Create extraction function** following the pattern:

```javascript
// Example: Extract algorithm complexity info
function extractComplexity() {
  // Try multiple sources
  if (window.algorithmComplexity) {
    return window.algorithmComplexity;
  }

  // Parse from page text
  const complexityText = document.querySelector('[data-complexity]')?.textContent;
  if (complexityText) {
    return complexityText;
  }

  // Fallback
  return null;
}
```

3. **Add to getChatbotContext()**:

```javascript
const context = {
  // ... existing properties
  complexity: extractComplexity()
};
```

4. **Document the new field** in this file's "Core Functions" section

---

## Debugging Context Extraction

### Enable Debug Mode

Add `?debug=1` to any mechanism URL to see detailed context logging:

```
https://domain.com/p/f25-01/v3/core/m-001?debug=1
```

This enables:
- Detailed console logging via `logContext()`
- Context summary displayed in console
- All extraction attempts logged

### Common Issues and Solutions

**Issue:** Context is `null`
- **Check:** Is the mechanism page fully loaded? Try opening dev console after page is ready.
- **Solution:** Use `?debug=1` to see which extraction methods succeeded.

**Issue:** `currentStep` or `totalSteps` are `null`
- **Check:** Are window globals being set? Look for `window.currStep` in console.
- **Solution:** Animation might not be initialized yet. Wait for page to fully load.

**Issue:** Input/output data are `null`
- **Check:** Has user loaded data? Some mechanisms require manual data entry.
- **Solution:** Try loading sample data from the mechanism's data file picker.

**Issue:** Chatbot not providing mechanism-specific responses
- **Check:** Is context being passed to API? Check network tab for POST to `/api/chatbot-stream.php`
- **Verify:** Enhanced context includes `mechanismInfo` with full details
- **Solution:** Mechanism metadata might not be populated for this ID. Add it to `getMechanismInfo()`.

### Inspecting Context in Browser Console

```javascript
// Get current context
import { getChatbotContext, getEnhancedContext, logContext } from './chatbot-context.js';

const context = getChatbotContext();
console.log(context);

const enhanced = getEnhancedContext();
console.log('Mechanism:', enhanced.mechanismInfo);
console.log('Data Available:', enhanced.dataAvailability);

// Detailed logging
logContext();

// Context summary
console.log(getContextSummary());
```

---

## Performance Considerations

### Context Extraction Speed

The context extraction is optimized for performance:
- **First extraction** (~5-10ms): Initial parameter parsing
- **Subsequent calls** (<1ms): Cached window variables
- **Full enhanced context** (~10-20ms): Includes metadata lookup

### Caching Strategy

The chatbot doesn't cache context internally. Instead:
1. Context is extracted fresh on each message
2. Page globals (`window.mid`, `window.input`, etc.) are maintained by mechanism JavaScript
3. This ensures context is always current with animation state

### When to Extract Context

The ChatbotUI extracts context:
- When chat window opens (get initial context)
- Before sending each user message (ensure current state)
- Not continuously (would impact performance)

---

## API Integration

### System Prompt Construction Using Context

The backend (`chatbot-stream.php`) receives enhanced context and uses it to build a 3-layer system prompt:

```
Layer 1: Global Context
"You are an AI tutor helping students understand OS algorithms..."

Layer 2: Category Context
"The student is learning about CPU Scheduling algorithms..."

Layer 3: Mechanism Context
"They are specifically studying FCFS (First Come First Served)...
Pros: Simple, fair, non-preemptive...
Cons: Convoy effect, doesn't minimize wait time..."
```

This 3-layer approach ensures the AI understands:
- The general educational context
- The category of algorithm being studied
- The specific algorithm's characteristics and common misconceptions

---

## Summary

The context extraction module is a crucial bridge between the UI and the chatbot backend. It automatically detects what algorithm the user is viewing, what state the visualization is in, and provides rich metadata to enable truly context-aware AI responses.

By continuously extracting and passing this context, the chatbot can:
- ✓ Explain concepts related to the current algorithm
- ✓ Reference specific metrics visible on the current page
- ✓ Suggest follow-up learning based on algorithm properties
- ✓ Correct misconceptions specific to each algorithm
- ✓ Provide relevant examples using the user's current input data

The modular design allows for easy extension as new mechanisms are added to the platform.
