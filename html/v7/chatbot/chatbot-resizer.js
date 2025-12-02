/**
 * Chatbot Resizer
 *
 * Makes the chatbot widget resizable via corner drag
 * Allows users to adjust width and height dynamically
 */

/**
 * ChatbotResizer Class
 *
 * Handles resizing of chatbot widget from top-left corner
 * Uses mouse drag interaction similar to window resizing
 */
export class ChatbotResizer {
  /**
   * Constructor
   *
   * @param {HTMLElement} widgetElement - The chatbot widget element
   * @param {Object} options - Configuration options
   */
  constructor(widgetElement, options = {}) {
    // Element references
    this.widget = widgetElement;
    this.messagesContainer = widgetElement.querySelector('#chatbot-messages');

    // Configuration
    this.minWidth = options.minWidth || 350;
    this.minHeight = options.minHeight || 400;
    this.maxWidth = options.maxWidth || window.innerWidth * 0.9;
    this.maxHeight = options.maxHeight || window.innerHeight * 0.9;

    // Resize state
    this.isResizing = false;
    this.startX = 0;
    this.startY = 0;
    this.startWidth = 0;
    this.startHeight = 0;

    // Initialize
    this.init();
  }

  /**
   * Initialize resizer
   */
  init() {
    // Create and attach resize handle
    this.createResizeHandle();

    // Attach event listeners
    this.attachEventListeners();

    // Set initial dimensions
    this.setInitialDimensions();

    this.log('✓ Resizer initialized');
  }

  /**
   * Create resize handle element
   */
  createResizeHandle() {
    // Check if handle already exists
    if (this.widget.querySelector('.chatbot-resize-handle')) {
      this.handle = this.widget.querySelector('.chatbot-resize-handle');
      return;
    }

    // Create handle element
    this.handle = document.createElement('div');
    this.handle.className = 'chatbot-resize-handle';
    this.handle.title = 'Drag to resize';
    this.handle.setAttribute('aria-label', 'Resize chatbot widget');

    // Insert at the beginning of the widget
    this.widget.insertBefore(this.handle, this.widget.firstChild);
  }

  /**
   * Attach event listeners
   */
  attachEventListeners() {
    // Mouse down on handle to start resize
    this.handle.addEventListener('mousedown', (e) => this.startResize(e));

    // Mouse move to resize
    document.addEventListener('mousemove', (e) => this.resize(e), false);

    // Mouse up to stop resize
    document.addEventListener('mouseup', () => this.stopResize(), false);

    // Handle window resize
    window.addEventListener('resize', () => this.constrainSize(), false);

    this.log('✓ Event listeners attached');
  }

  /**
   * Start resize operation
   *
   * @param {MouseEvent} e - Mouse event
   */
  startResize(e) {
    // Prevent text selection during drag
    e.preventDefault();
    e.stopPropagation();

    this.isResizing = true;

    // Record starting position
    this.startX = e.clientX;
    this.startY = e.clientY;

    // Record starting dimensions
    this.startWidth = this.widget.offsetWidth;
    this.startHeight = this.widget.offsetHeight;

    // Add active class for visual feedback
    this.widget.classList.add('resizing');
    this.handle.classList.add('active');

    this.log('Resize started');
  }

  /**
   * Handle resize operation
   *
   * @param {MouseEvent} e - Mouse event
   */
  resize(e) {
    if (!this.isResizing) return;

    // Calculate drag distance
    const deltaX = e.clientX - this.startX;
    const deltaY = e.clientY - this.startY;

    // Calculate new dimensions
    let newWidth = this.startWidth - deltaX;
    let newHeight = this.startHeight - deltaY;

    // Apply constraints
    newWidth = Math.max(this.minWidth, Math.min(newWidth, this.maxWidth));
    newHeight = Math.max(this.minHeight, Math.min(newHeight, this.maxHeight));

    // Apply new dimensions
    this.widget.style.width = newWidth + 'px';
    this.widget.style.height = newHeight + 'px';

    // Trigger scroll to bottom if messages exist
    if (this.messagesContainer) {
      this.messagesContainer.scrollTop = this.messagesContainer.scrollHeight;
    }
  }

  /**
   * Stop resize operation
   */
  stopResize() {
    if (!this.isResizing) return;

    this.isResizing = false;

    // Remove active class
    this.widget.classList.remove('resizing');
    this.handle.classList.remove('active');

    this.log('Resize stopped');
  }

  /**
   * Set initial widget dimensions
   */
  setInitialDimensions() {
    // Check if dimensions are already set
    if (this.widget.style.width && this.widget.style.height) {
      return;
    }

    // Use reasonable defaults
    const defaultWidth = 400;
    const defaultHeight = 500;

    this.widget.style.width = defaultWidth + 'px';
    this.widget.style.height = defaultHeight + 'px';

    this.log(`Initial dimensions set: ${defaultWidth}x${defaultHeight}`);
  }

  /**
   * Constrain widget size to viewport
   * Called when window is resized
   */
  constrainSize() {
    if (!this.widget) return;

    const currentWidth = this.widget.offsetWidth;
    const currentHeight = this.widget.offsetHeight;

    let needsAdjustment = false;
    let newWidth = currentWidth;
    let newHeight = currentHeight;

    // Check if larger than current max
    if (currentWidth > this.maxWidth) {
      newWidth = this.maxWidth;
      needsAdjustment = true;
    }

    if (currentHeight > this.maxHeight) {
      newHeight = this.maxHeight;
      needsAdjustment = true;
    }

    if (needsAdjustment) {
      this.widget.style.width = newWidth + 'px';
      this.widget.style.height = newHeight + 'px';
      this.log(`Size constrained to viewport: ${newWidth}x${newHeight}`);
    }
  }

  /**
   * Update resize constraints
   *
   * @param {Object} options - New constraint values
   */
  updateConstraints(options) {
    if (options.minWidth) this.minWidth = options.minWidth;
    if (options.minHeight) this.minHeight = options.minHeight;
    if (options.maxWidth) this.maxWidth = options.maxWidth;
    if (options.maxHeight) this.maxHeight = options.maxHeight;

    this.constrainSize();
    this.log('Constraints updated');
  }

  /**
   * Get current widget dimensions
   *
   * @returns {Object} Width and height
   */
  getDimensions() {
    return {
      width: this.widget.offsetWidth,
      height: this.widget.offsetHeight
    };
  }

  /**
   * Set widget dimensions
   *
   * @param {number} width - New width in pixels
   * @param {number} height - New height in pixels
   */
  setDimensions(width, height) {
    this.widget.style.width = width + 'px';
    this.widget.style.height = height + 'px';
    this.log(`Dimensions set: ${width}x${height}`);
  }

  /**
   * Log message with prefix
   *
   * @param {string} message - Message to log
   */
  log(message) {
    console.log(`[ChatbotResizer] ${message}`);
  }
}

/**
 * Initialize resizer on page load
 *
 * @returns {ChatbotResizer|null} Resizer instance or null if widget not found
 */
export function initializeChatbotResizer(options = {}) {
  if (document.readyState === 'loading') {
    return new Promise((resolve) => {
      document.addEventListener('DOMContentLoaded', () => {
        const widget = document.querySelector('#chatbot-container .chatbot-window');
        if (widget) {
          const resizer = new ChatbotResizer(widget, options);
          resolve(resizer);
        } else {
          console.warn('[ChatbotResizer] Widget not found');
          resolve(null);
        }
      });
    });
  } else {
    const widget = document.querySelector('#chatbot-container .chatbot-window');
    if (widget) {
      return new ChatbotResizer(widget, options);
    } else {
      console.warn('[ChatbotResizer] Widget not found');
      return null;
    }
  }
}
