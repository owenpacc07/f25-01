<?php
/**
 * Chatbot Loader
 *
 * Integrates the chatbot onto pages by loading CSS and JavaScript modules
 * Include this file in navbar.php or directly in mechanism pages
 *
 * Usage:
 *   <?php include __DIR__ . '/chatbot/chatbot-loader.php'; ?>
 */

// Get version path and SITE_ROOT with fallbacks
// First try values from parent scope (set by system.php via PathConfig)
// Then try PathConfig if not set
// Finally fallback to safe defaults
if (empty($SITE_ROOT) || empty($version_path)) {
    require_once(__DIR__ . '/../../config/PathConfig.php');
    $pathConfig = PathConfig::getInstance();
    $SITE_ROOT = $SITE_ROOT ?? $pathConfig->getSiteRoot();
    $version_path = $version_path ?? $pathConfig->getVersionPath();
}
// Last resort hardcoded fallback (should never reach here)
$version_path = $version_path ?? "/v7";
$SITE_ROOT = $SITE_ROOT ?? "/p/f25-01";

// Check if we're in an admin panel or other excluded area
$current_path = $_SERVER['REQUEST_URI'] ?? '';
$excluded_paths = [
    'admin',
    'teacher',
    'professor',
    '/api/',
    'login',
    'auth'
];

$should_load = true;
foreach ($excluded_paths as $exclude) {
    if (stripos($current_path, $exclude) !== false) {
        $should_load = false;
        break;
    }
}

if (!$should_load) {
    return; // Don't load chatbot on excluded pages
}

// Determine if debug mode is enabled
$debug_mode = isset($_GET['debug']) ? true : false;

if ($debug_mode) {
    echo "<!-- Chatbot Loader: Starting initialization -->\n";
}
?>

<!-- OS Visuals Chatbot Stylesheet -->
<link rel="stylesheet" href="<?php echo $SITE_ROOT; ?><?php echo $version_path; ?>/chatbot/chatbot-styles.css">

<!-- OS Visuals Chatbot JavaScript Modules -->
<script type="module">
  // Import and initialize chatbot modules
  import { chatbot } from '<?php echo $SITE_ROOT; ?><?php echo $version_path; ?>/chatbot/chatbot-ui.js';

  <?php if ($debug_mode): ?>
  console.log('[Chatbot Loader] Chatbot modules loaded successfully');
  console.log('[Chatbot Loader] Chatbot instance:', chatbot);
  console.log('[Chatbot Loader] Debug mode enabled');
  <?php endif; ?>

  // Make chatbot available globally for debugging
  if (typeof window !== 'undefined') {
    window.chatbot = chatbot;
    <?php if ($debug_mode): ?>
    console.log('[Chatbot Loader] Chatbot instance available at window.chatbot');
    <?php endif; ?>
  }
</script>

<?php if ($debug_mode): ?>
<!-- Debug Information -->
<script>
  console.log('[Chatbot Loader] Configuration:');
  console.log('  Version Path:', '<?php echo $version_path; ?>');
  console.log('  Site Root:', '<?php echo $SITE_ROOT; ?>');
  console.log('  Current Page:', window.location.pathname);
  console.log('  Mechanism ID (window.mid):', window.mid || 'None');
  console.log('  Current Mode:', window.location.pathname.match(/\/(core(?:-[a-z])?)\//)?.[1] || 'Unknown');
</script>
<?php endif; ?>
