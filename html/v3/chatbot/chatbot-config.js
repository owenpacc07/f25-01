/**
 * Chatbot Configuration
 *
 * Central configuration file for all chatbot settings including:
 * - API endpoint paths
 * - Widget dimensions and positioning
 * - Animation speeds
 * - Message limits
 * - Timeouts
 */

export const CHATBOT_CONFIG = {
  // API Configuration
  API: {
    endpoint: '/p/f25-01/v3/api/chatbot-stream.php',
    timeout: 30000, // 30 seconds
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    }
  },

  // Widget Positioning (fixed, bottom-right)
  WIDGET: {
    position: {
      bottom: '20px',
      right: '20px'
    },
    zIndex: 10000,
    zIndexToggle: 10001
  },

  // Dimensions
  DIMENSIONS: {
    // Collapsed state (button only)
    button: {
      width: '60px',
      height: '60px',
      borderRadius: '50%'
    },
    // Expanded state (chat window)
    window: {
      width: '350px',
      maxHeight: '500px',
      minHeight: '300px',
      borderRadius: '12px'
    },
    // Mobile adjustments
    mobile: {
      window: {
        width: 'calc(100% - 40px)',
        bottom: '10px',
        right: '10px',
        left: '10px'
      },
      breakpoint: 768 // pixels
    }
  },

  // Colors
  COLORS: {
    primary: '#e94e19',      // Orange (matches navbar)
    background: '#ffffff',
    text: '#333333',
    border: '#e0e0e0',
    userMessage: '#e8f5e9',  // Light green
    assistantMessage: '#f5f5f5', // Light gray
    systemMessage: '#fff3cd'  // Light yellow
  },

  // Animation
  ANIMATION: {
    expandCollapse: 300,     // ms for expand/collapse transition
    messageSlide: 200,       // ms for message slide-in
    typingBlink: 600         // ms for typing indicator blink
  },

  // Message Handling
  MESSAGES: {
    maxHistory: 20,          // Keep last 20 messages in memory
    maxLength: 500,          // Max message length
    minLength: 1             // Min message length
  },

  // UI Text
  TEXT: {
    title: 'OS Visuals Assistant',
    placeholder: 'Ask me about this algorithm...',
    submitLabel: 'Send',
    minButtonLabel: '−',
    closeButtonLabel: '×',
    typingIndicator: '...',
    defaultWelcome: 'Hi! 👋 I\'m your OS Visuals assistant. Ask me anything about the algorithm you\'re viewing!',
    errorMessage: 'Sorry, I encountered an error. Please try again.',
    apiErrorMessage: 'I\'m temporarily unavailable. Please try again or check the info page.',
    outOfScopeMessage: 'That question is not in my scope of possibilities. Feel free to ask about OS algorithms and mechanisms!',
    loadingMessage: 'Thinking...',
    emptyMessage: 'No messages yet. Start by asking a question!'
  },

  // Session Storage Keys
  SESSION: {
    chatHistoryKey: 'chatbot_history_v3',
    sessionIdKey: 'chatbot_session_id',
    contextKey: 'chatbot_context'
  },

  // Features
  FEATURES: {
    showTypingIndicator: true,
    showWelcomeMessage: true,
    persistHistory: true, // Session-based only, no localStorage
    autoScrollMessages: true,
    enableKeyboardShortcuts: true // Enter to send, Escape to close
  },

  // Accessibility
  ACCESSIBILITY: {
    ariaLiveRegion: 'polite', // or 'assertive'
    ariaLabelToggle: 'Open AI Assistant',
    ariaLabelClose: 'Close chat',
    ariaLabelMinimize: 'Minimize chat',
    ariaLabelInput: 'Type your question',
    ariaLabelSend: 'Send message',
    roleMessages: 'log'
  }
};

// Export individual config sections for convenience
export const API_CONFIG = CHATBOT_CONFIG.API;
export const WIDGET_CONFIG = CHATBOT_CONFIG.WIDGET;
export const DIMENSIONS_CONFIG = CHATBOT_CONFIG.DIMENSIONS;
export const COLORS_CONFIG = CHATBOT_CONFIG.COLORS;
export const TEXT_CONFIG = CHATBOT_CONFIG.TEXT;
export const FEATURES_CONFIG = CHATBOT_CONFIG.FEATURES;
