/**
 * Chatbot Data Bridge
 *
 * Global script that exposes mechanism data to window scope
 * Works across all mechanism types and core variants
 * Runs on every mechanism page and makes data available to the chatbot
 */

(function() {
  'use strict';

  /**
   * Extract mechanism ID from URL path
   * e.g., /core/m-001/ → "001"
   */
  function extractMechanismIdFromURL() {
    const match = window.location.pathname.match(/\/m-(\d{3})/);
    return match ? match[1] : null;
  }

  /**
   * Determine mechanism type from ID
   */
  function determineMechanismType(mid) {
    if (!mid) return null;
    const id = parseInt(mid);
    if (id >= 1 && id <= 8) return 'cpu';
    if (id >= 11 && id <= 19) return 'memory';
    if (id >= 21 && id <= 29) return 'page';
    if (id >= 31 && id <= 39) return 'file';
    if (id >= 41 && id <= 49) return 'disk';
    return null;
  }

  /**
   * Check if data is available in current scope
   */
  function isDataAvailable() {
    try {
      // Try to access mechanism data variables from module scope
      // This works because this script runs in the same context as mechanism pages

      // Check if input exists and has content
      if (typeof input !== 'undefined' && input && input.length > 0) {
        return true;
      }

      // Fallback: check for output if input doesn't exist
      if (typeof output !== 'undefined' && output && output.length > 0) {
        return true;
      }

      // Another fallback: check for mechanism data in different variable names
      if (typeof processData !== 'undefined' && processData && processData.length > 0) {
        return true;
      }

      return false;
    } catch (e) {
      return false;
    }
  }

  /**
   * Safely get mechanism data from current scope
   */
  function getMechanismData() {
    const data = {
      input: null,
      output: null,
      extraOutput: null
    };

    try {
      // Try to get input
      if (typeof input !== 'undefined') {
        data.input = input;
      } else if (typeof processData !== 'undefined') {
        data.input = processData;
      } else if (typeof memoryData !== 'undefined') {
        data.input = memoryData;
      } else if (typeof pageData !== 'undefined') {
        data.input = pageData;
      } else if (typeof diskData !== 'undefined') {
        data.input = diskData;
      }

      // Try to get output
      if (typeof output !== 'undefined') {
        data.output = output;
      } else if (typeof results !== 'undefined') {
        data.output = results;
      }

      // Try to get extra output (animation data)
      if (typeof extraOutput !== 'undefined') {
        data.extraOutput = extraOutput;
      } else if (typeof animationData !== 'undefined') {
        data.extraOutput = animationData;
      }

      return data;
    } catch (e) {
      console.warn('[ChatbotDataBridge] Error getting mechanism data:', e);
      return data;
    }
  }

  /**
   * Expose data to window scope in standardized format
   */
  function exposeData() {
    console.log('[ChatbotDataBridge] Attempting to expose data...');

    if (!isDataAvailable()) {
      console.log('[ChatbotDataBridge] Data not available yet');
      return false;
    }

    console.log('[ChatbotDataBridge] Data is available, extracting...');

    try {
      const mechanismData = getMechanismData();
      const mid = extractMechanismIdFromURL();
      const type = determineMechanismType(mid);

      console.log('[ChatbotDataBridge] Mechanism ID:', mid);
      console.log('[ChatbotDataBridge] Mechanism Type:', type);
      console.log('[ChatbotDataBridge] Raw Input Data:', mechanismData.input);
      console.log('[ChatbotDataBridge] Raw Output Data:', mechanismData.output);

      // Create standardized global object
      window.__visosData = {
        input: mechanismData.input,
        output: mechanismData.output || [],
        extraOutput: mechanismData.extraOutput || [],
        mid: mid,
        type: type,
        timestamp: Date.now()
      };

      console.log('[ChatbotDataBridge] Exposed to window.__visosData:', window.__visosData);

      // Also expose individually for backward compatibility
      // and direct access by chatbot
      if (mechanismData.input) {
        window.input = mechanismData.input;
      }

      if (mechanismData.output) {
        window.output = mechanismData.output;
      }

      if (mechanismData.extraOutput) {
        window.extraOutput = mechanismData.extraOutput;
      }

      // Set mechanism ID on window
      if (mid) {
        window.mid = mid;
      }

      if (type) {
        window.mechanismType = type;
      }

      console.log('[ChatbotDataBridge] ✓ Data successfully exposed to window scope');
      return true;
    } catch (e) {
      console.warn('[ChatbotDataBridge] Error exposing data:', e);
      return false;
    }
  }

  /**
   * Main initialization with retry logic
   */
  function initialize() {
    console.log('[ChatbotDataBridge] Initializing data bridge...');

    // Try immediately in case data is already loaded
    if (exposeData()) {
      console.log('[ChatbotDataBridge] ✓ Data exposed on first attempt');
      return;
    }

    console.log('[ChatbotDataBridge] Data not ready, starting polling (up to 5 seconds)...');

    // Polling approach: check every 100ms for up to 5 seconds
    let attempts = 0;
    const maxAttempts = 50; // 50 * 100ms = 5 seconds
    const pollInterval = setInterval(() => {
      attempts++;
      if (exposeData()) {
        clearInterval(pollInterval);
        console.log(`[ChatbotDataBridge] ✓ Data exposed after ${attempts * 100}ms of polling`);
        return;
      }

      if (attempts % 10 === 0) {
        console.log(`[ChatbotDataBridge] Still waiting for data... (${attempts * 100}ms)`);
      }

      if (attempts >= maxAttempts) {
        clearInterval(pollInterval);
        console.warn('[ChatbotDataBridge] ⚠ Data not loaded after timeout (5 seconds)');
      }
    }, 100);

    // Also try when DOM is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function onDOMReady() {
        console.log('[ChatbotDataBridge] Trying to expose data on DOMContentLoaded...');
        exposeData();
        document.removeEventListener('DOMContentLoaded', onDOMReady);
      });
    }

    // Try again after a short delay for late-loading scripts
    setTimeout(() => {
      if (!window.__visosData || !window.__visosData.input) {
        console.log('[ChatbotDataBridge] Trying to expose data at 1000ms mark...');
        exposeData();
      }
    }, 1000);
  }

  // Initialize when this script loads
  console.log('[ChatbotDataBridge] Script loaded, starting initialization...');
  initialize();
})();
