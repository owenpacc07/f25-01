/**
 * Chatbot UI Component
 *
 * Main chatbot user interface class
 * Manages DOM elements, state, and user interactions
 */

import { CHATBOT_CONFIG, API_CONFIG, COLORS_CONFIG, TEXT_CONFIG, FEATURES_CONFIG } from './chatbot-config.js';
import { sendMessage, getErrorMessage, logAPIError, APIError } from './chatbot-api.js';
import { getEnhancedContext, logContext } from './chatbot-context.js';
import { formatChatbotResponse, initializeMarked } from './chatbot-response-formatter.js';
import { ChatbotResizer } from './chatbot-resizer.js';

/**
 * ChatbotUI - Main chatbot class
 *
 * Singleton pattern - only one instance should exist
 */
export class ChatbotUI {
  constructor() {
    // State
    this.isExpanded = false;
    this.isLoading = false;
    this.messages = [];
    this.context = {};

    // DOM Elements
    this.container = null;
    this.toggleButton = null;
    this.chatWindow = null;
    this.messagesContainer = null;
    this.inputForm = null;
    this.inputField = null;
    this.sendButton = null;
    this.minimizeButton = null;
    this.liveRegion = null;

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => this.init());
    } else {
      this.init();
    }
  }

  /**
   * Initialize the chatbot
   */
  init() {
    // Create DOM elements
    this.createElements();

    // Inject styles
    this.injectStyles();

    // Load chat history from session
    this.loadHistory();

    // Attach event listeners
    this.attachEventListeners();

    // Initialize resize functionality
    const resizer = new ChatbotResizer(this.chatWindow, {
      minWidth: 350,
      minHeight: 400
    });
    this.log('✓ Resizer initialized');

    // Initialize marked.js for markdown formatting
    initializeMarked();
    this.log('✓ Markdown formatter initialized');

    // Log initialization if debug mode
    this.log('✓ Chatbot initialized');
  }

  /**
   * Create all required DOM elements
   */
  createElements() {
    // Container
    this.container = document.createElement('div');
    this.container.id = 'chatbot-container';

    // Toggle Button
    this.toggleButton = document.createElement('button');
    this.toggleButton.id = 'chatbot-toggle';
    this.toggleButton.setAttribute('aria-label', CHATBOT_CONFIG.A11Y.toggleButtonLabel);
    this.toggleButton.innerHTML = '💬';
    this.toggleButton.style.display = 'flex';

    // Chat Window
    this.chatWindow = document.createElement('div');
    this.chatWindow.className = 'chatbot-window';
    this.chatWindow.style.display = 'none';

    // Header
    const header = document.createElement('div');
    header.className = 'chatbot-header';
    header.innerHTML = `<h3>${TEXT_CONFIG.title}</h3>`;

    // Close Button
    const closeButton = document.createElement('button');
    closeButton.setAttribute('aria-label', CHATBOT_CONFIG.A11Y.closeButtonLabel);
    closeButton.innerHTML = '✕';
    closeButton.style.marginLeft = 'auto';

    // Minimize Button
    this.minimizeButton = document.createElement('button');
    this.minimizeButton.setAttribute('aria-label', CHATBOT_CONFIG.A11Y.minimizeButtonLabel);
    this.minimizeButton.innerHTML = '−';

    header.appendChild(this.minimizeButton);
    header.appendChild(closeButton);

    // Messages Container
    this.messagesContainer = document.createElement('div');
    this.messagesContainer.id = 'chatbot-messages';
    this.messagesContainer.setAttribute('role', 'log');
    this.messagesContainer.setAttribute('aria-live', 'polite');
    this.messagesContainer.setAttribute('aria-label', CHATBOT_CONFIG.A11Y.messagesLiveRegion);

    // Input Form
    this.inputForm = document.createElement('form');
    this.inputForm.id = 'chatbot-input-form';

    this.inputField = document.createElement('input');
    this.inputField.id = 'chatbot-input';
    this.inputField.type = 'text';
    this.inputField.placeholder = TEXT_CONFIG.placeholder;
    this.inputField.setAttribute('aria-label', CHATBOT_CONFIG.A11Y.inputLabel);
    this.inputField.setAttribute('autocomplete', 'off');

    this.sendButton = document.createElement('button');
    this.sendButton.type = 'submit';
    this.sendButton.textContent = TEXT_CONFIG.sendButton;
    this.sendButton.setAttribute('aria-label', CHATBOT_CONFIG.A11Y.sendButtonLabel);

    this.inputForm.appendChild(this.inputField);
    this.inputForm.appendChild(this.sendButton);

    // Assemble chat window
    this.chatWindow.appendChild(header);
    this.chatWindow.appendChild(this.messagesContainer);
    this.chatWindow.appendChild(this.inputForm);

    // Assemble container
    this.container.appendChild(this.toggleButton);
    this.container.appendChild(this.chatWindow);

    // Add to page
    document.body.appendChild(this.container);

    // Close button event
    closeButton.addEventListener('click', (e) => {
      e.preventDefault();
      this.closeChat();
    });
  }

  /**
   * Inject CSS styles into page
   */
  injectStyles() {
    // Styles are loaded via chatbot-loader.php, but we can add inline fixes if needed
    // For now, relying on external stylesheet
  }

  /**
   * Attach event listeners
   */
  attachEventListeners() {
    // Toggle button
    this.toggleButton.addEventListener('click', (e) => {
      e.preventDefault();
      this.toggleChat();
    });

    // Minimize button
    this.minimizeButton.addEventListener('click', (e) => {
      e.preventDefault();
      this.toggleChat();
    });

    // Input form submit
    this.inputForm.addEventListener('submit', (e) => {
      e.preventDefault();
      this.handleSubmit();
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
      if (!FEATURES_CONFIG.keyboardShortcuts) return;

      // Escape to close
      if (e.key === 'Escape' && this.isExpanded) {
        e.preventDefault();
        this.closeChat();
      }
    });

    // Enter to send (within input field)
    this.inputField.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        this.handleSubmit();
      }
    });
  }

  /**
   * Toggle chat open/closed
   */
  toggleChat() {
    if (this.isExpanded) {
      this.closeChat();
    } else {
      this.openChat();
    }
  }

  /**
   * Open chat window
   */
  openChat() {
    this.isExpanded = true;
    this.chatWindow.style.display = 'flex';
    this.toggleButton.style.display = 'none';

    // Show welcome message if first time
    if (this.messages.length === 0 && FEATURES_CONFIG.showWelcomeMessage) {
      this.addMessage(TEXT_CONFIG.welcome, 'system');
    }

    // Focus input field
    setTimeout(() => this.inputField.focus(), 100);

    // Extract context
    this.context = this.getContext();
  }

  /**
   * Close chat window
   */
  closeChat() {
    this.isExpanded = false;
    this.chatWindow.style.display = 'none';
    this.toggleButton.style.display = 'flex';

    // Clear loading state
    this.setLoading(false);
  }

  /**
   * Handle message submission
   */
  async handleSubmit() {
    const message = this.inputField.value.trim();

    // Validate message
    if (message.length === 0) {
      this.inputField.focus();
      return;
    }

    if (message.length > CHATBOT_CONFIG.MESSAGE.maxLength) {
      this.addMessage(
        `Message too long. Maximum ${CHATBOT_CONFIG.MESSAGE.maxLength} characters.`,
        'system'
      );
      return;
    }

    // Add user message to UI
    this.addMessage(message, 'user');
    this.inputField.value = '';
    this.setLoading(true);

    try {
      // Get updated context
      this.context = this.getContext();
      console.log('[ChatbotUI] Context retrieved for message:', this.context);
      console.log('[ChatbotUI] Has mechanismData:', !!this.context.mechanismData);
      if (this.context.mechanismData) {
        console.log('[ChatbotUI] mechanismData.hasActualData:', this.context.mechanismData.hasActualData);
        console.log('[ChatbotUI] mechanismData.dataContext length:', this.context.mechanismData.dataContext?.length || 0);
      }

      // Show typing indicator
      if (FEATURES_CONFIG.showTypingIndicator) {
        this.addTypingIndicator();
      }

      // Send to API
      console.log('[ChatbotUI] Sending message to API with context...');
      const response = await sendMessage(message, this.context);
      console.log('[ChatbotUI] ✓ Response received from API');

      // Remove typing indicator
      if (FEATURES_CONFIG.showTypingIndicator) {
        this.removeTypingIndicator();
      }

      // Add assistant response
      this.addMessage(response, 'assistant');
    } catch (error) {
      logAPIError(error);

      // Remove typing indicator
      if (FEATURES_CONFIG.showTypingIndicator) {
        this.removeTypingIndicator();
      }

      // Show error message
      const errorMessage = getErrorMessage(error);
      this.addMessage(errorMessage, 'system');
    } finally {
      this.setLoading(false);
      this.inputField.focus();
    }
  }

  /**
   * Add message to chat
   *
   * @param {string} text - Message text
   * @param {string} role - Message role: 'user', 'assistant', 'system'
   */
  addMessage(text, role = 'assistant') {
    // Create message object
    const message = {
      text: text,
      role: role,
      timestamp: new Date()
    };

    // Add to history
    this.messages.push(message);

    // Limit history size
    if (this.messages.length > CHATBOT_CONFIG.MESSAGE.maxHistory) {
      this.messages.shift();
    }

    // Save to session storage
    this.saveHistory();

    // Create DOM element
    const messageEl = document.createElement('div');
    messageEl.className = `chatbot-message ${role}`;

    const contentEl = document.createElement('div');
    contentEl.className = 'chatbot-message-content';

    // Use formatter for assistant messages, plain text for others
    if (role === 'assistant') {
      contentEl.innerHTML = formatChatbotResponse(text, role);
    } else {
      contentEl.textContent = text;
    }

    messageEl.appendChild(contentEl);
    this.messagesContainer.appendChild(messageEl);

    // Auto-scroll to bottom
    if (FEATURES_CONFIG.autoScroll) {
      this.scrollToBottom();
    }
  }

  /**
   * Add typing indicator
   */
  addTypingIndicator() {
    const messageEl = document.createElement('div');
    messageEl.className = 'chatbot-message assistant';
    messageEl.id = 'chatbot-typing-indicator';

    const typingEl = document.createElement('div');
    typingEl.className = 'chatbot-typing';
    typingEl.innerHTML = '<span></span><span></span><span></span>';

    messageEl.appendChild(typingEl);
    this.messagesContainer.appendChild(messageEl);

    this.scrollToBottom();
  }

  /**
   * Remove typing indicator
   */
  removeTypingIndicator() {
    const indicator = document.getElementById('chatbot-typing-indicator');
    if (indicator) {
      indicator.remove();
    }
  }

  /**
   * Scroll messages to bottom
   */
  scrollToBottom() {
    setTimeout(() => {
      this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }, 0);
  }

  /**
   * Set loading state
   *
   * @param {boolean} loading - Is loading?
   */
  setLoading(loading) {
    this.isLoading = loading;
    this.inputField.disabled = loading;
    this.sendButton.disabled = loading;
  }

  /**
   * Get current page context
   *
   * Enhanced context extraction with mechanism metadata and animation state
   *
   * @returns {object} - Enhanced context object with mechanism info, metadata, and state
   */
  getContext() {
    // Use enhanced context extraction from chatbot-context module
    const enhancedContext = getEnhancedContext();

    // Log context if debug mode is enabled
    const isDebugMode = new URLSearchParams(window.location.search).has('debug');
    if (isDebugMode) {
      logContext(enhancedContext);
    }

    return enhancedContext;
  }

  /**
   * Detect current mode from URL
   *
   * @returns {string} - Mode: core, core-a, core-c, core-e, core-s
   */
  detectMode() {
    const pathParts = window.location.pathname.split('/');
    for (let part of pathParts) {
      if (part.match(/^core(-[a-z])?$/)) {
        return part;
      }
    }
    return 'core';
  }

  /**
   * Save chat history to session storage
   */
  saveHistory() {
    if (!FEATURES_CONFIG.persistHistory) return;

    try {
      sessionStorage.setItem(
        CHATBOT_CONFIG.STORAGE.history,
        JSON.stringify(this.messages)
      );
    } catch (error) {
      console.warn('[Chatbot] Failed to save history:', error.message);
    }
  }

  /**
   * Load chat history from session storage
   */
  loadHistory() {
    if (!FEATURES_CONFIG.persistHistory) return;

    try {
      const saved = sessionStorage.getItem(CHATBOT_CONFIG.STORAGE.history);
      if (saved) {
        this.messages = JSON.parse(saved);

        // Restore messages to DOM (without re-saving to sessionStorage to avoid duplicates)
        for (let msg of this.messages) {
          // Create DOM element directly without calling addMessage() to avoid double-saving
          const messageEl = document.createElement('div');
          messageEl.className = `chatbot-message ${msg.role}`;

          const contentEl = document.createElement('div');
          contentEl.className = 'chatbot-message-content';

          // Use formatter for assistant messages, plain text for others
          if (msg.role === 'assistant') {
            contentEl.innerHTML = formatChatbotResponse(msg.text, msg.role);
          } else {
            contentEl.textContent = msg.text;
          }

          messageEl.appendChild(contentEl);
          this.messagesContainer.appendChild(messageEl);
        }

        // Auto-scroll to bottom if history was restored
        if (FEATURES_CONFIG.autoScroll && this.messages.length > 0) {
          this.scrollToBottom();
        }
      }
    } catch (error) {
      console.warn('[Chatbot] Failed to load history:', error.message);
      // Clear corrupted history
      this.messages = [];
    }
  }

  /**
   * Clear chat history
   */
  clearHistory() {
    this.messages = [];
    this.messagesContainer.innerHTML = '';
    this.saveHistory();
  }

  /**
   * Get message history
   *
   * @returns {array} - Array of message objects
   */
  getHistory() {
    return [...this.messages];
  }

  /**
   * Log message (debug mode)
   *
   * @param {string} message - Message to log
   */
  log(message) {
    const isDebugMode = new URLSearchParams(window.location.search).has('debug');
    if (isDebugMode) {
      console.log('[Chatbot]', message);
    }
  }

  /**
   * Destroy chatbot instance
   */
  destroy() {
    if (this.container) {
      this.container.remove();
    }
    this.messages = [];
  }
}

// Create and export singleton instance
export const chatbot = new ChatbotUI();
