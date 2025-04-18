<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define plugin directory URI for assets
define('JIALIUB_PLUGIN_URI', plugin_dir_url(dirname(__DIR__) . '/core.php'));
define('JIALIUB_ASSETS_URI', JIALIUB_PLUGIN_URI . 'assets');
define('JIALIUB_CSS_URI', JIALIUB_ASSETS_URI . '/css');
define('JIALIUB_JS_URI', JIALIUB_ASSETS_URI . '/js');

// Define plugin directory path for file inclusion
define('JIALIUB_PLUGIN_PATH', plugin_dir_path(dirname(__DIR__) . '/core.php'));
define('JIALIUB_INC_PATH', JIALIUB_PLUGIN_PATH . 'inc');

?>