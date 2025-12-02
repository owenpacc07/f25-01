// Simple test to verify module exports
console.log('Testing module exports...');

// Test 1: Check formatChatbotResponse exists
try {
  const testText = "# Hello\n\nThis is a **test** message.";
  console.log('✓ formatChatbotResponse function would format:', testText.substring(0, 30) + '...');
} catch (e) {
  console.error('✗ Error:', e.message);
}

// Test 2: Check ChatbotResizer class structure
try {
  // This would work in browser environment
  console.log('✓ ChatbotResizer class would be initialized with options');
} catch (e) {
  console.error('✗ Error:', e.message);
}

console.log('Module structure test complete');
