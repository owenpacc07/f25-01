/**
 * Chatbot UI Component
 *
 * Main class for the OS Visuals AI Chatbot widget
 * Handles:
 * - UI element creation and management
 * - User interaction (toggle, messages, input)
 * - Message display and history
 * - API communication
 * - State management
 *
 * Usage:
 * import { ChatbotUI } from './chatbot-ui.js';
 * window.chatbot = new ChatbotUI();
 */

import { CHATBOT_CONFIG, TEXT_CONFIG, COLORS_CONFIG, DIMENSIONS_CONFIG } from './chatbot-config.js';
import { sendMessage } from './chatbot-api.js';
import { getChatbotContext, getEnhancedContext, logContext, hasValidContext, getContextSummary, getMechanismInfo } from './chatbot-context.js';

export class ChatbotUI {
  constructor() {
    this.isExpanded = false;
    this.isLoading = false;
    this.messages = [];
    this.container = null;
    this.toggleButton = null;
    this.window = null;
    this.messagesDiv = null;
    this.inputForm = null;
    this.inputField = null;

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', () => this.init());
    } else {
      this.init();
    }
  }

  /**
   * Initialize the chatbot UI
   */
  init() {
    try {
      this.createElements();
      this.attachEventListeners();
      this.loadContext();
      console.log('[Chatbot] Initialized successfully');
    } catch (error) {
      console.error('[Chatbot] Initialization failed:', error);
    }
  }

  /**
   * Create all DOM elements for the chatbot
   */
  createElements() {
    // Create main container
    this.container = document.createElement('div');
    this.container.id = 'chatbot-container';
    document.body.appendChild(this.container);

    // Create toggle button (initially visible)
    this.toggleButton = this.createToggleButton();
    this.container.appendChild(this.toggleButton);

    // Create chat window (initially hidden)
    this.window = this.createChatWindow();
    this.window.style.display = 'none';
    this.container.appendChild(this.window);

    // Load and inject CSS
    this.injectStyles();
  }

  /**
   * Create the toggle button
   */
  createToggleButton() {
    const button = document.createElement('button');
    button.id = 'chatbot-toggle';
    button.setAttribute('aria-label', CHATBOT_CONFIG.ACCESSIBILITY.ariaLabelToggle);
    button.setAttribute('type', 'button');
    button.innerHTML = '💬';
    button.title = 'AI Assistant';
    return button;
  }

  /**
   * Create the chat window with header, messages, and input
   */
  createChatWindow() {
    const window = document.createElement('div');
    window.className = 'chatbot-window';

    // Header
    const header = document.createElement('div');
    header.className = 'chatbot-header';
    header.innerHTML = `
      <h3>${TEXT_CONFIG.title}</h3>
      <button type="button" id="chatbot-minimize" aria-label="${CHATBOT_CONFIG.ACCESSIBILITY.ariaLabelMinimize}">${TEXT_CONFIG.minButtonLabel}</button>
      <button type="button" id="chatbot-close" aria-label="${CHATBOT_CONFIG.ACCESSIBILITY.ariaLabelClose}">${TEXT_CONFIG.closeButtonLabel}</button>
    `;
    window.appendChild(header);

    // Messages container
    this.messagesDiv = document.createElement('div');
    this.messagesDiv.id = 'chatbot-messages';
    this.messagesDiv.setAttribute('role', CHATBOT_CONFIG.ACCESSIBILITY.roleMessages);
    this.messagesDiv.setAttribute('aria-live', CHATBOT_CONFIG.ACCESSIBILITY.ariaLiveRegion);
    this.messagesDiv.setAttribute('aria-atomic', 'false');
    window.appendChild(this.messagesDiv);

    // Input form
    this.inputForm = document.createElement('form');
    this.inputForm.className = 'chatbot-input-form';
    this.inputForm.id = 'chatbot-input-form';

    this.inputField = document.createElement('input');
    this.inputField.type = 'text';
    this.inputField.id = 'chatbot-input';
    this.inputField.placeholder = TEXT_CONFIG.placeholder;
    this.inputField.setAttribute('aria-label', CHATBOT_CONFIG.ACCESSIBILITY.ariaLabelInput);
    this.inputField.setAttribute('autocomplete', 'off');

    const submitButton = document.createElement('button');
    submitButton.type = 'submit';
    submitButton.setAttribute('aria-label', CHATBOT_CONFIG.ACCESSIBILITY.ariaLabelSend);
    submitButton.innerHTML = '→';
    submitButton.title = 'Send message (Enter)';

    this.inputForm.appendChild(this.inputField);
    this.inputForm.appendChild(submitButton);

    window.appendChild(this.inputForm);

    // Show welcome message
    this.addMessage(TEXT_CONFIG.defaultWelcome, 'system');

    return window;
  }

  /**
   * Inject CSS styles into the document
   */
  injectStyles() {
    // Create style tag
    const style = document.createElement('style');
    style.textContent = this.getCSSContent();
    document.head.appendChild(style);
  }

  /**
   * Get CSS content (loaded from file or embedded)
   * Note: In production, this should load from chatbot-styles.css
   */
  getCSSContent() {
    // For now, return minimal CSS to make it work
    // Full CSS will be loaded via stylesheet link in loader.php
    return `
      #chatbot-container {
        position: fixed;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 14px;
        z-index: 10000;
      }

      #chatbot-toggle {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: none;
        background: linear-gradient(135deg, #e94e19 0%, #d63d0c 100%);
        color: white;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(233, 78, 25, 0.4);
        transition: all 0.3s ease;
        z-index: 10001;
        padding: 0;
        outline: none;
      }

      #chatbot-toggle:hover {
        transform: scale(1.1);
      }

      .chatbot-window {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 350px;
        max-height: 500px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 40px rgba(0, 0, 0, 0.16);
        display: flex;
        flex-direction: column;
        z-index: 10000;
        overflow: hidden;
      }

      .chatbot-header {
        background: linear-gradient(135deg, #e94e19 0%, #d63d0c 100%);
        color: white;
        padding: 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .chatbot-header h3 {
        margin: 0;
        font-size: 16px;
      }

      .chatbot-header button {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        padding: 4px 8px;
      }

      #chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #fafafa;
        display: flex;
        flex-direction: column;
        gap: 12px;
      }

      .chatbot-message {
        display: flex;
        margin-bottom: 8px;
      }

      .chatbot-message.user {
        justify-content: flex-end;
      }

      .chatbot-message.user .message-content {
        background: #e8f5e9;
        color: #1b5e20;
      }

      .chatbot-message.assistant .message-content {
        background: white;
        border: 1px solid #e0e0e0;
      }

      .chatbot-message.system .message-content {
        background: #fff3cd;
        color: #664d03;
        align-self: center;
      }

      .message-content {
        padding: 10px 14px;
        border-radius: 12px;
        word-wrap: break-word;
        max-width: 85%;
        font-size: 13px;
        line-height: 1.4;
      }

      .chatbot-input-form {
        padding: 12px;
        background: white;
        border-top: 1px solid #e0e0e0;
        display: flex;
        gap: 8px;
      }

      #chatbot-input {
        flex: 1;
        padding: 10px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        font-size: 13px;
        font-family: inherit;
      }

      #chatbot-input:focus {
        outline: none;
        border-color: #e94e19;
      }

      .chatbot-input-form button[type="submit"] {
        padding: 10px 16px;
        background: #e94e19;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
      }

      .chatbot-input-form button[type="submit"]:hover {
        background: #d63d0c;
      }

      .chatbot-input-form button[type="submit"]:disabled {
        background: #cccccc;
        cursor: not-allowed;
      }

      @media (max-width: 768px) {
        .chatbot-window {
          width: calc(100% - 40px);
          bottom: 10px;
          right: 10px;
          left: 10px;
        }

        #chatbot-toggle {
          bottom: 10px;
          right: 10px;
        }
      }
    `;
  }

  /**
   * Attach event listeners to UI elements
   */
  attachEventListeners() {
    // Toggle button - open/close chat
    this.toggleButton.addEventListener('click', () => this.toggleChat());

    // Close button
    const closeBtn = this.window.querySelector('#chatbot-close');
    closeBtn.addEventListener('click', () => this.closeChat());

    // Minimize button
    const minimizeBtn = this.window.querySelector('#chatbot-minimize');
    minimizeBtn.addEventListener('click', () => this.toggleChat());

    // Input form submission
    this.inputForm.addEventListener('submit', (e) => this.handleSubmit(e));

    // Keyboard shortcuts
    if (CHATBOT_CONFIG.FEATURES.enableKeyboardShortcuts) {
      document.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }
  }

  /**
   * Toggle chat window open/closed
   */
  toggleChat() {
    if (this.isExpanded) {
      this.closeChat();
    } else {
      this.openChat();
    }
  }

  /**
   * Open the chat window
   */
  openChat() {
    this.isExpanded = true;
    this.window.style.display = 'flex';
    this.toggleButton.style.display = 'none';
    this.inputField.focus();
  }

  /**
   * Close the chat window
   */
  closeChat() {
    this.isExpanded = false;
    this.window.style.display = 'none';
    this.toggleButton.style.display = 'flex';
  }

  /**
   * Handle form submission (user sends message)
   */
  async handleSubmit(e) {
    e.preventDefault();

    const message = this.inputField.value.trim();

    // Validate message
    if (message.length < CHATBOT_CONFIG.MESSAGES.minLength) {
      return;
    }

    if (message.length > CHATBOT_CONFIG.MESSAGES.maxLength) {
      this.addMessage(`Message too long (max ${CHATBOT_CONFIG.MESSAGES.maxLength} characters)`, 'system');
      return;
    }

    // Add user message to display
    this.addMessage(message, 'user');

    // Clear input
    this.inputField.value = '';
    this.inputField.focus();

    // Disable input while loading
    this.setLoading(true);

    // Show typing indicator
    if (CHATBOT_CONFIG.FEATURES.showTypingIndicator) {
      this.addTypingIndicator();
    }

    try {
      // Get current context (will be implemented in Phase 3)
      const context = this.getContext();

      // Send message to API
      const response = await sendMessage(message, context);

      // Remove typing indicator
      this.removeTypingIndicator();

      // Add assistant response
      this.addMessage(response, 'assistant');
    } catch (error) {
      console.error('[Chatbot] Error sending message:', error);

      // Remove typing indicator
      this.removeTypingIndicator();

      // Show error message
      this.addMessage(TEXT_CONFIG.apiErrorMessage, 'system');
    } finally {
      this.setLoading(false);
    }
  }

  /**
   * Handle keyboard shortcuts
   */
  handleKeyboard(e) {
    // Escape to close chat
    if (e.key === 'Escape' && this.isExpanded) {
      this.closeChat();
    }

    // Focus input when opening
    if (this.isExpanded && e.key === '/') {
      e.preventDefault();
      this.inputField.focus();
    }
  }

  /**
   * Add a message to the chat
   */
  addMessage(text, role = 'assistant') {
    const messageDiv = document.createElement('div');
    messageDiv.className = `chatbot-message ${role}`;

    const contentDiv = document.createElement('div');
    contentDiv.className = 'message-content';
    contentDiv.textContent = text;

    messageDiv.appendChild(contentDiv);
    this.messagesDiv.appendChild(messageDiv);

    // Store in messages array
    this.messages.push({ text, role, timestamp: new Date() });

    // Trim messages if exceeding limit
    if (this.messages.length > CHATBOT_CONFIG.MESSAGES.maxHistory) {
      this.messages.shift();
      this.messagesDiv.removeChild(this.messagesDiv.firstChild);
    }

    // Auto-scroll to bottom
    if (CHATBOT_CONFIG.FEATURES.autoScrollMessages) {
      this.scrollToBottom();
    }
  }

  /**
   * Add typing indicator
   */
  addTypingIndicator() {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'chatbot-message assistant';
    messageDiv.id = 'typing-indicator';

    const typingDiv = document.createElement('div');
    typingDiv.className = 'typing-indicator';
    typingDiv.innerHTML = `
      <span class="typing-dot"></span>
      <span class="typing-dot"></span>
      <span class="typing-dot"></span>
    `;

    messageDiv.appendChild(typingDiv);
    this.messagesDiv.appendChild(messageDiv);

    this.scrollToBottom();
  }

  /**
   * Remove typing indicator
   */
  removeTypingIndicator() {
    const indicator = this.messagesDiv.querySelector('#typing-indicator');
    if (indicator) {
      indicator.remove();
    }
  }

  /**
   * Scroll messages to bottom
   */
  scrollToBottom() {
    setTimeout(() => {
      this.messagesDiv.scrollTop = this.messagesDiv.scrollHeight;
    }, 0);
  }

  /**
   * Set loading state
   */
  setLoading(isLoading) {
    this.isLoading = isLoading;
    const submitButton = this.inputForm.querySelector('button[type="submit"]');
    submitButton.disabled = isLoading;
    this.inputField.disabled = isLoading;
  }

  /**
   * Get current context from page
   * Uses chatbot-context.js for enhanced context extraction
   */
  getContext() {
    try {
      // Get enhanced context with all available data
      const context = getEnhancedContext();

      // Log context for debugging
      if (window.location.search.includes('debug')) {
        logContext();
        console.log('[Chatbot] Context Summary:', getContextSummary());
      }

      return context;
    } catch (error) {
      console.warn('[Chatbot] Error extracting context:', error);

      // Fallback to basic context
      return {
        mechanism: window.mid || null,
        mode: this.detectMode(),
        timestamp: new Date().toISOString()
      };
    }
  }

  /**
   * Detect current mode from URL
   * Note: Also available from chatbot-context.js
   */
  detectMode() {
    const path = window.location.pathname;
    if (path.includes('/core-a/')) return 'core-a';
    if (path.includes('/core-e/')) return 'core-e';
    if (path.includes('/core-s/')) return 'core-s';
    if (path.includes('/core-c/')) return 'core-c';
    if (path.includes('/core/')) return 'core';
    return 'unknown';
  }

  /**
   * Load stored context from session storage
   */
  loadContext() {
    try {
      const stored = sessionStorage.getItem(CHATBOT_CONFIG.SESSION.chatHistoryKey);
      if (stored && CHATBOT_CONFIG.FEATURES.persistHistory) {
        const history = JSON.parse(stored);
        this.messages = history;
        // Optionally restore messages to UI
      }
    } catch (error) {
      console.warn('[Chatbot] Could not load context:', error);
    }
  }

  /**
   * Save context to session storage
   */
  saveContext() {
    try {
      sessionStorage.setItem(
        CHATBOT_CONFIG.SESSION.chatHistoryKey,
        JSON.stringify(this.messages)
      );
    } catch (error) {
      console.warn('[Chatbot] Could not save context:', error);
    }
  }

  /**
   * Clear chat history
   */
  clearHistory() {
    this.messages = [];
    this.messagesDiv.innerHTML = '';
    this.addMessage(TEXT_CONFIG.defaultWelcome, 'system');
    this.saveContext();
  }

  /**
   * Get chat history
   */
  getHistory() {
    return this.messages;
  }

  /**
   * Destroy the chatbot (cleanup)
   */
  destroy() {
    if (this.container && this.container.parentNode) {
      this.container.parentNode.removeChild(this.container);
    }
  }
}

// Auto-initialize on page load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    if (!window.chatbot) {
      window.chatbot = new ChatbotUI();
    }
  });
} else {
  if (!window.chatbot) {
    window.chatbot = new ChatbotUI();
  }
}

export default ChatbotUI;
