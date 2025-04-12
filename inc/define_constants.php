<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define plugin directory URI for assets
define('JIALIUFL_PLUGIN_URI', plugin_dir_url(dirname(__DIR__) . '/core.php'));
define('JIALIUFL_ASSETS_URI', JIALIUFL_PLUGIN_URI . 'assets');
define('JIALIUFL_CSS_URI', JIALIUFL_ASSETS_URI . '/css');
define('JIALIUFL_JS_URI', JIALIUFL_ASSETS_URI . '/js');

// Define plugin directory path for file inclusion
define('JIALIUFL_PLUGIN_PATH', plugin_dir_path(dirname(__DIR__) . '/core.php'));
define('JIALIUFL_INC_PATH', JIALIUFL_PLUGIN_PATH . 'inc');

?>