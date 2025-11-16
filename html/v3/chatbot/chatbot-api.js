/**
 * Chatbot API Client
 *
 * Handles all communication with the chatbot backend API
 * - Sends messages to /api/chatbot-stream.php
 * - Handles responses and errors
 * - Manages timeouts and retries
 *
 * Usage:
 * import { sendMessage } from './chatbot-api.js';
 * const response = await sendMessage("Hello", context);
 */

import { CHATBOT_CONFIG, TEXT_CONFIG } from './chatbot-config.js';

/**
 * Send a message to the chatbot API
 *
 * @param {string} message - The user's message
 * @param {object} context - Context data (mechanism, mode, etc.)
 * @returns {Promise<string>} - The chatbot's response
 * @throws {Error} - If request fails
 */
export async function sendMessage(message, context = {}) {
  // Validate inputs
  if (!message || typeof message !== 'string') {
    throw new Error('Message must be a non-empty string');
  }

  // Prepare request payload
  const payload = {
    message: message.trim(),
    mechanism: context.mechanism || null,
    mode: context.mode || 'core',
    context: {
      currentStep: context.currentStep || null,
      isPlaying: context.isPlaying || false,
      inputData: context.inputData || null,
      outputData: context.outputData || null
    }
  };

  try {
    console.log('[Chatbot API] Sending message:', {
      message: payload.message.substring(0, 50) + '...',
      mechanism: payload.mechanism
    });

    // Make the request
    const response = await fetch(CHATBOT_CONFIG.API.endpoint, {
      method: CHATBOT_CONFIG.API.method,
      headers: CHATBOT_CONFIG.API.headers,
      body: JSON.stringify(payload),
      signal: AbortSignal.timeout(CHATBOT_CONFIG.API.timeout)
    });

    // Check if response is OK
    if (!response.ok) {
      const errorData = await response.json().catch(() => ({
        status: 'error',
        message: `HTTP ${response.status}`
      }));

      console.error('[Chatbot API] Error response:', errorData);

      // Handle specific error codes
      if (response.status === 500 || response.status === 503) {
        throw new APIError(
          TEXT_CONFIG.apiErrorMessage,
          'SERVICE_UNAVAILABLE',
          response.status
        );
      }

      throw new APIError(
        errorData.message || TEXT_CONFIG.apiErrorMessage,
        errorData.error_code || 'API_ERROR',
        response.status
      );
    }

    // Parse response
    const responseData = await response.json();

    // Check response status
    if (responseData.status !== 'success') {
      console.error('[Chatbot API] Non-success response:', responseData);

      throw new APIError(
        responseData.message || TEXT_CONFIG.apiErrorMessage,
        responseData.error_code || 'UNKNOWN_ERROR'
      );
    }

    // Extract and return the message
    if (!responseData.response || typeof responseData.response !== 'string') {
      throw new APIError(
        'Invalid response format from server',
        'INVALID_FORMAT'
      );
    }

    console.log('[Chatbot API] Response received successfully');

    return responseData.response;
  } catch (error) {
    // Handle different error types
    if (error instanceof APIError) {
      throw error;
    }

    if (error instanceof TypeError) {
      // Network error
      console.error('[Chatbot API] Network error:', error);
      throw new APIError(
        TEXT_CONFIG.apiErrorMessage,
        'NETWORK_ERROR'
      );
    }

    if (error.name === 'AbortError') {
      // Timeout
      console.error('[Chatbot API] Request timeout');
      throw new APIError(
        'Request timed out. The server is taking too long to respond.',
        'TIMEOUT'
      );
    }

    // Unknown error
    console.error('[Chatbot API] Unknown error:', error);
    throw new APIError(
      TEXT_CONFIG.errorMessage,
      'UNKNOWN_ERROR',
      error.message
    );
  }
}

/**
 * Custom error class for API errors
 */
export class APIError extends Error {
  constructor(message, code = 'UNKNOWN_ERROR', status = null) {
    super(message);
    this.name = 'APIError';
    this.code = code;
    this.status = status;
  }

  /**
   * Get user-friendly error message
   */
  getUserMessage() {
    switch (this.code) {
      case 'SERVICE_UNAVAILABLE':
        return TEXT_CONFIG.apiErrorMessage;
      case 'TIMEOUT':
        return 'The server is taking too long to respond. Please try again.';
      case 'NETWORK_ERROR':
        return 'Network error. Please check your connection and try again.';
      case 'API_KEY_NOT_CONFIGURED':
        return 'The chatbot is not properly configured. Please contact support.';
      case 'INVALID_FORMAT':
        return 'Received invalid response from server.';
      default:
        return this.message || TEXT_CONFIG.errorMessage;
    }
  }

  /**
   * Determine if error is recoverable
   */
  isRecoverable() {
    return ![
      'API_KEY_NOT_CONFIGURED',
      'INVALID_FORMAT',
      'SERVICE_UNAVAILABLE'
    ].includes(this.code);
  }
}

/**
 * Send message with retry logic (for future enhancement)
 *
 * @param {string} message - The user's message
 * @param {object} context - Context data
 * @param {number} maxRetries - Maximum number of retries
 * @returns {Promise<string>} - The chatbot's response
 */
export async function sendMessageWithRetry(message, context = {}, maxRetries = 2) {
  let lastError;

  for (let attempt = 1; attempt <= maxRetries + 1; attempt++) {
    try {
      return await sendMessage(message, context);
    } catch (error) {
      lastError = error;

      // Don't retry non-recoverable errors
      if (error instanceof APIError && !error.isRecoverable()) {
        throw error;
      }

      // Don't retry on last attempt
      if (attempt === maxRetries + 1) {
        throw error;
      }

      // Wait before retrying (exponential backoff)
      const delayMs = Math.pow(2, attempt - 1) * 1000;
      console.log(`[Chatbot API] Retry attempt ${attempt}/${maxRetries} after ${delayMs}ms`);

      await new Promise(resolve => setTimeout(resolve, delayMs));
    }
  }

  throw lastError;
}

/**
 * Test API connectivity
 *
 * @returns {Promise<boolean>} - True if API is reachable
 */
export async function testAPIConnectivity() {
  try {
    const response = await fetch(CHATBOT_CONFIG.API.endpoint, {
      method: 'OPTIONS',
      signal: AbortSignal.timeout(5000)
    });

    return response.ok;
  } catch (error) {
    console.warn('[Chatbot API] Connectivity test failed:', error);
    return false;
  }
}

/**
 * Get API endpoint URL
 */
export function getAPIEndpoint() {
  return CHATBOT_CONFIG.API.endpoint;
}

/**
 * Get API timeout
 */
export function getAPITimeout() {
  return CHATBOT_CONFIG.API.timeout;
}

export default { sendMessage, sendMessageWithRetry, testAPIConnectivity };
