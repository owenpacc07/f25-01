<?php
/**
 * Chatbot Loader Include
 *
 * This file should be included in navbar.php to load the chatbot on all pages.
 * It handles:
 * - Loading CSS stylesheets
 * - Loading JavaScript modules
 * - Initializing the chatbot UI
 *
 * Usage in navbar.php:
 * <?php include './chatbot/chatbot-loader.php'; ?>
 */

// Get the site root for proper path references
$siteRoot = isset($SITE_ROOT) ? $SITE_ROOT : '/p/f25-01';
$versionPath = isset($version_path) ? $version_path : '/v3';
$chatbotPath = $siteRoot . $versionPath . '/chatbot';

// Don't load chatbot on admin panel (optional check)
$currentPath = $_SERVER['REQUEST_URI'];
$isAdminPanel = strpos($currentPath, '/adminPanel/') !== false;

if ($isAdminPanel) {
  // Chatbot not needed on admin panel - return early
  return;
}

// Only load chatbot if we're on a relevant page
$isMechanismPage = preg_match('/\/(core|core-a|core-e|core-s|core-c)\/(m-\d+|index)/', $currentPath);
$isHomepage = preg_match('/(index\.php)?$/', $currentPath);

if (!$isMechanismPage && !$isHomepage) {
  // Not a mechanism or main page - chatbot not needed
  return;
}
?>

<!-- Chatbot Stylesheet -->
<link rel="stylesheet" href="<?php echo $chatbotPath; ?>/chatbot-styles.css">

<!-- Chatbot JavaScript Modules -->
<script type="module">
  // Import and initialize chatbot with context awareness
  import { ChatbotUI } from '<?php echo $chatbotPath; ?>/chatbot-ui.js';
  import { logContext, getContextSummary } from '<?php echo $chatbotPath; ?>/chatbot-context.js';

  // Auto-initialize chatbot (ChatbotUI constructor handles this)
  // window.chatbot will be available globally after initialization

  // Log initialization
  console.log('[Chatbot Loader] Chatbot modules loaded');

  // Log context information for debugging (only in debug mode)
  if (window.location.search.includes('debug')) {
    console.log('[Chatbot] Page Context:', getContextSummary());
    logContext();
  }
</script>

<?php
/*
 * Alternative: If you want to use separate <script> tags for each module:
 *
 * <script type="module" src="<?php echo $chatbotPath; ?>/chatbot-config.js"></script>
 * <script type="module" src="<?php echo $chatbotPath; ?>/chatbot-api.js"></script>
 * <script type="module" src="<?php echo $chatbotPath; ?>/chatbot-ui.js"></script>
 */
?>
