/**
 * Chatbot API Client
 *
 * Handles all communication with the backend chatbot API endpoint
 * Includes error handling, retries, and request validation
 */

import { API_CONFIG } from './chatbot-config.js';

/**
 * Custom error class for API errors
 */
export class APIError extends Error {
  constructor(message, code = 'UNKNOWN_ERROR', details = null) {
    super(message);
    this.name = 'APIError';
    this.code = code;
    this.details = details;
  }
}

/**
 * Send a message to the chatbot API
 *
 * @param {string} message - User's message
 * @param {object} context - Algorithm context (mechanism, mode, data, etc.)
 * @returns {Promise<string>} - Chatbot response text
 * @throws {APIError} - On any error
 */
export async function sendMessage(message, context = {}) {
  // Validate input
  if (!message || typeof message !== 'string') {
    throw new APIError('Invalid message', 'INVALID_MESSAGE');
  }

  const trimmedMessage = message.trim();
  if (trimmedMessage.length === 0) {
    throw new APIError('Message cannot be empty', 'EMPTY_MESSAGE');
  }

  // Prepare request payload
  const payload = {
    message: trimmedMessage,
    mechanism: context.mechanism || null,
    mode: context.mode || 'core',
    context: context
  };

  try {
    const response = await fetch(API_CONFIG.endpoint, {
      method: API_CONFIG.method,
      headers: API_CONFIG.headers,
      body: JSON.stringify(payload),
      timeout: API_CONFIG.timeout
    });

    // Handle HTTP errors
    if (!response.ok) {
      const errorData = await response.json().catch(() => ({}));
      throw new APIError(
        errorData.message || `HTTP ${response.status}`,
        errorData.error_code || `HTTP_${response.status}`,
        errorData
      );
    }

    // Parse response
    const data = await response.json();

    // Validate response format
    if (!data || data.status !== 'success' || !data.response) {
      throw new APIError(
        'Invalid response format from API',
        'INVALID_RESPONSE_FORMAT',
        data
      );
    }

    return data.response;
  } catch (error) {
    // Handle different error types
    if (error instanceof APIError) {
      throw error;
    }

    // Network errors
    if (error instanceof TypeError) {
      throw new APIError(
        'Network error - failed to reach API',
        'NETWORK_ERROR',
        error.message
      );
    }

    // Timeout errors
    if (error.name === 'AbortError') {
      throw new APIError(
        'Request timeout - API took too long to respond',
        'TIMEOUT',
        error.message
      );
    }

    // Unknown errors
    throw new APIError(
      error.message || 'Unknown error occurred',
      'UNKNOWN_ERROR',
      error
    );
  }
}

/**
 * Send message with automatic retry logic
 *
 * @param {string} message - User's message
 * @param {object} context - Algorithm context
 * @param {number} maxRetries - Maximum number of retries (default: 2)
 * @returns {Promise<string>} - Chatbot response text
 * @throws {APIError} - On final failure
 */
export async function sendMessageWithRetry(message, context = {}, maxRetries = 2) {
  let lastError;

  for (let attempt = 0; attempt <= maxRetries; attempt++) {
    try {
      return await sendMessage(message, context);
    } catch (error) {
      lastError = error;

      // Don't retry non-recoverable errors
      if (
        error.code === 'INVALID_MESSAGE' ||
        error.code === 'EMPTY_MESSAGE' ||
        error.code === 'INVALID_RESPONSE_FORMAT'
      ) {
        throw error;
      }

      // Don't retry on final attempt
      if (attempt === maxRetries) {
        break;
      }

      // Exponential backoff: 1s, 2s, 4s
      const delayMs = Math.pow(2, attempt) * 1000;
      await new Promise(resolve => setTimeout(resolve, delayMs));
    }
  }

  throw lastError || new APIError(
    'Failed after multiple retries',
    'MAX_RETRIES_EXCEEDED'
  );
}

/**
 * Test API connectivity
 *
 * Sends a simple OPTIONS request to check if API is reachable
 *
 * @returns {Promise<boolean>} - True if API is reachable, false otherwise
 */
export async function testAPIConnectivity() {
  try {
    const response = await fetch(API_CONFIG.endpoint, {
      method: 'OPTIONS',
      timeout: 5000
    });
    return response.ok;
  } catch (error) {
    console.warn('[Chatbot] API connectivity test failed:', error.message);
    return false;
  }
}

/**
 * Get a user-friendly error message
 *
 * Converts error codes to helpful messages for users
 *
 * @param {APIError} error - The error to convert
 * @returns {string} - User-friendly error message
 */
export function getErrorMessage(error) {
  if (!(error instanceof APIError)) {
    return 'An unexpected error occurred. Please try again.';
  }

  const messages = {
    'NETWORK_ERROR': 'Connection error. Please check your internet and try again.',
    'TIMEOUT': 'The response took too long. Please try again.',
    'API_KEY_NOT_CONFIGURED': 'The chatbot is not properly configured. Please contact support.',
    'HYDRA_UNREACHABLE': 'The AI service is temporarily unavailable. Please try again later.',
    'HYDRA_ERROR': 'The AI service encountered an error. Please try again.',
    'INVALID_MESSAGE': 'Please enter a valid message.',
    'EMPTY_MESSAGE': 'Please enter a message.',
    'INVALID_RESPONSE_FORMAT': 'The response format was invalid. Please try again.',
    'MAX_RETRIES_EXCEEDED': 'Failed after multiple attempts. Please try again later.',
    'UNKNOWN_ERROR': 'An unknown error occurred. Please try again.'
  };

  return messages[error.code] || error.message || messages['UNKNOWN_ERROR'];
}

/**
 * Log API error for debugging
 *
 * @param {APIError} error - The error to log
 */
export function logAPIError(error) {
  const isDebugMode = new URLSearchParams(window.location.search).has('debug');
  if (!isDebugMode) return;

  console.error('[Chatbot API Error]', {
    code: error.code,
    message: error.message,
    details: error.details,
    timestamp: new Date().toISOString()
  });
}
