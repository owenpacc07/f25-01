/**
 * Chatbot Configuration
 *
 * Centralized configuration for all chatbot settings
 * Change values here to customize chatbot behavior across the entire application
 */

export const CHATBOT_CONFIG = {
  // API Configuration
  API: {
    endpoint: '/p/f25-01/v3b/api/chatbot-stream.php',
    method: 'POST',
    timeout: 30000, // 30 seconds
    headers: {
      'Content-Type': 'application/json'
    }
  },

  // Widget Positioning (Fixed in bottom-right corner)
  POSITION: {
    bottom: '20px',
    right: '20px',
    zIndex: 10000 // Above content but respects navbar
  },

  // Widget Dimensions
  DIMENSIONS: {
    button: {
      width: '60px',
      height: '60px',
      borderRadius: '50%' // Circular
    },
    window: {
      width: '350px',
      maxHeight: '500px',
      borderRadius: '12px'
    },
    mobile: {
      breakpoint: '768px',
      window: {
        width: 'calc(100% - 40px)',
        maxHeight: '70vh'
      }
    }
  },

  // Colors (Match OS Visuals theme)
  COLORS: {
    primary: '#e94e19', // Orange (navbar color)
    background: '#ffffff', // White
    text: '#333333', // Dark gray
    border: '#e0e0e0', // Light gray
    userMessage: '#e8f5e9', // Light green
    assistantMessage: '#f5f5f5', // Light gray
    systemMessage: '#fff3cd', // Light yellow
    error: '#f44336' // Red
  },

  // Animation Settings
  ANIMATION: {
    expandCollapse: 300, // milliseconds
    messageSlide: 200, // milliseconds
    typingIndicator: 600 // milliseconds
  },

  // Message Settings
  MESSAGE: {
    maxLength: 500,
    minLength: 1,
    maxHistory: 20, // Keep last 20 messages in memory
    scrollBehavior: 'smooth'
  },

  // UI Text (Customizable)
  TEXT: {
    title: 'OS Visuals Assistant',
    placeholder: 'Ask me about this algorithm...',
    sendButton: '→',
    closeButton: '✕',
    minimizeButton: '−',
    welcome: 'Hi! 👋 I\'m here to help you understand OS algorithms. Ask me anything about the current mechanism!',
    typing: 'Typing...',
    error: 'Sorry, I encountered an error. Please try again.',
    timeout: 'The response is taking too long. Please check your connection.',
    apiError: 'I\'m temporarily unavailable. Please try again later.'
  },

  // Session Storage Keys
  STORAGE: {
    history: 'chatbot_history_v3',
    sessionId: 'chatbot_session_id',
    context: 'chatbot_context'
  },

  // Feature Flags
  FEATURES: {
    showTypingIndicator: true,
    showWelcomeMessage: true,
    persistHistory: true, // Session-based
    autoScroll: true,
    keyboardShortcuts: true, // Enter to send, Escape to close
    debugMode: false // Set via ?debug=1 in URL
  },

  // Accessibility
  A11Y: {
    toggleButtonLabel: 'Open AI Assistant',
    closeButtonLabel: 'Close chat',
    minimizeButtonLabel: 'Minimize chat',
    messagesLiveRegion: 'Chat messages',
    inputLabel: 'Type your question',
    sendButtonLabel: 'Send message'
  }
};

// Export individual sections for easier access
export const API_CONFIG = CHATBOT_CONFIG.API;
export const POSITION_CONFIG = CHATBOT_CONFIG.POSITION;
export const COLORS_CONFIG = CHATBOT_CONFIG.COLORS;
export const TEXT_CONFIG = CHATBOT_CONFIG.TEXT;
export const FEATURES_CONFIG = CHATBOT_CONFIG.FEATURES;
