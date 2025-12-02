/**
 * Chatbot Response Formatter
 *
 * Converts raw AI responses to beautifully formatted HTML
 * using marked.js for markdown parsing
 */

// Import marked library from CDN
let marked;

/**
 * Initialize marked library
 * Load marked.js from CDN for markdown parsing
 */
export async function initializeMarked() {
  if (marked) return; // Already loaded

  try {
    // Load marked from CDN
    const response = await fetch('https://cdn.jsdelivr.net/npm/marked/lib/marked.esm.js');
    const markedModule = await response.text();

    // Alternative: Use direct import
    // Note: For production, consider using import statement if bundling
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/marked@11.1.1/+esm';
    script.type = 'module';

    // Actually, let's use the simpler approach with marked global
    const markedScript = document.createElement('script');
    markedScript.src = 'https://cdn.jsdelivr.net/npm/marked@11.1.1';
    markedScript.onload = () => {
      console.log('[Formatter] marked.js library loaded');
    };
    document.head.appendChild(markedScript);
  } catch (error) {
    console.warn('[Formatter] Failed to load marked.js, using plain text fallback:', error);
  }
}

/**
 * Format a chatbot response
 *
 * @param {string} text - Raw response text (may contain markdown)
 * @param {string} role - Message role ('user', 'assistant', 'system')
 * @returns {string} Formatted HTML
 */
export function formatChatbotResponse(text, role = 'assistant') {
  // For user and system messages, use simple text formatting
  if (role === 'user' || role === 'system') {
    return formatPlainText(text);
  }

  // For assistant messages, parse markdown
  return formatWithMarkdown(text);
}

/**
 * Format plain text with basic structure
 * Used for user and system messages
 *
 * @param {string} text - Plain text to format
 * @returns {string} Formatted HTML
 */
function formatPlainText(text) {
  // Escape HTML
  text = escapeHTML(text);

  // Convert line breaks to <br>
  text = text.replace(/\n/g, '<br>');

  // Wrap in paragraph
  return `<p class="chatbot-text">${text}</p>`;
}

/**
 * Format text using markdown parsing
 * Converts markdown syntax to HTML
 *
 * @param {string} text - Text with markdown formatting
 * @returns {string} Formatted HTML
 */
function formatWithMarkdown(text) {
  // Check if marked is available
  if (typeof marked === 'undefined') {
    console.warn('[Formatter] marked.js not loaded, using plain text format');
    return formatPlainText(text);
  }

  try {
    // Parse markdown to HTML
    let html = marked.parse(text);

    // Remove auto-generated <p> tags from marked for better structure
    // Only for single paragraphs - keep multiple paragraphs
    const hasMultipleParagraphs = (html.match(/<p>/g) || []).length > 1;
    if (!hasMultipleParagraphs && html.startsWith('<p>') && html.endsWith('</p>')) {
      html = html.slice(3, -4); // Remove outer <p> tags
    }

    // Add CSS classes for styling
    html = addFormattingClasses(html);

    // Wrap in container
    return `<div class="chatbot-formatted-response">${html}</div>`;
  } catch (error) {
    console.error('[Formatter] Error parsing markdown:', error);
    return formatPlainText(text);
  }
}

/**
 * Add CSS classes to formatted elements
 * Enhances visual hierarchy and styling
 *
 * @param {string} html - HTML from markdown parser
 * @returns {string} HTML with added CSS classes
 */
function addFormattingClasses(html) {
  // Add classes to headers
  html = html.replace(/<h1>/g, '<h1 class="chatbot-heading-1">');
  html = html.replace(/<h2>/g, '<h2 class="chatbot-heading-2">');
  html = html.replace(/<h3>/g, '<h3 class="chatbot-heading-3">');
  html = html.replace(/<h4>/g, '<h4 class="chatbot-heading-4">');
  html = html.replace(/<h5>/g, '<h5 class="chatbot-heading-5">');
  html = html.replace(/<h6>/g, '<h6 class="chatbot-heading-6">');

  // Add classes to paragraphs
  html = html.replace(/<p>/g, '<p class="chatbot-paragraph">');

  // Add classes to lists
  html = html.replace(/<ul>/g, '<ul class="chatbot-list">');
  html = html.replace(/<ol>/g, '<ol class="chatbot-list-ordered">');
  html = html.replace(/<li>/g, '<li class="chatbot-list-item">');

  // Add classes to code blocks
  html = html.replace(/<pre>/g, '<pre class="chatbot-code-block">');
  html = html.replace(/<code>/g, '<code class="chatbot-code">');

  // Add classes to blockquotes
  html = html.replace(/<blockquote>/g, '<blockquote class="chatbot-blockquote">');

  // Add classes to tables
  html = html.replace(/<table>/g, '<table class="chatbot-table">');

  return html;
}

/**
 * Escape HTML special characters
 * Prevents XSS attacks
 *
 * @param {string} text - Text to escape
 * @returns {string} Escaped text
 */
function escapeHTML(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, (char) => map[char]);
}

/**
 * Sanitize HTML to prevent XSS
 * Removes potentially dangerous tags and attributes
 *
 * @param {string} html - HTML to sanitize
 * @returns {string} Sanitized HTML
 */
function sanitizeHTML(html) {
  const allowedTags = [
    'p', 'div', 'span', 'br',
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
    'ul', 'ol', 'li',
    'strong', 'em', 'b', 'i', 'u', 's',
    'code', 'pre',
    'blockquote',
    'a', 'img',
    'table', 'thead', 'tbody', 'tr', 'th', 'td',
    'hr',
    'section', 'article'
  ];

  const allowedAttributes = {
    'a': ['href', 'title', 'target', 'rel'],
    'img': ['src', 'alt', 'title', 'width', 'height'],
    '*': ['class', 'id', 'style']
  };

  // Create a temporary container
  const temp = document.createElement('div');
  temp.innerHTML = html;

  // Recursively clean elements
  function cleanElement(el) {
    // Remove script tags and dangerous content
    const scripts = el.querySelectorAll('script, style, iframe, object, embed');
    scripts.forEach(s => s.remove());

    // Process all elements
    el.querySelectorAll('*').forEach(element => {
      const tagName = element.tagName.toLowerCase();

      // Remove element if not allowed
      if (!allowedTags.includes(tagName)) {
        // Move children up
        while (element.firstChild) {
          element.parentNode.insertBefore(element.firstChild, element);
        }
        element.remove();
      } else {
        // Remove disallowed attributes
        const allowedAttrs = allowedAttributes[tagName] || allowedAttributes['*'] || [];
        Array.from(element.attributes).forEach(attr => {
          if (!allowedAttrs.includes(attr.name)) {
            element.removeAttribute(attr.name);
          }
        });

        // Sanitize href and src to prevent javascript:
        if (element.href && element.href.startsWith('javascript:')) {
          element.href = '#';
        }
        if (element.src && element.src.startsWith('javascript:')) {
          element.src = '';
        }
      }
    });
  }

  cleanElement(temp);
  return temp.innerHTML;
}

/**
 * Initialize formatter on page load
 */
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initializeMarked);
} else {
  initializeMarked();
}

// Export functions
export { sanitizeHTML, escapeHTML };
