<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define plugin directory URI for assets
define('JUFL_PLUGIN_URI', plugin_dir_url(dirname(__DIR__) . '/core.php'));
define('JUFL_ASSETS_URI', JUFL_PLUGIN_URI . 'assets');
define('JUFL_CSS_URI', JUFL_ASSETS_URI . '/css');
define('JUFL_JS_URI', JUFL_ASSETS_URI . '/js');

// Define plugin directory path for file inclusion
define('JUFL_PLUGIN_PATH', plugin_dir_path(dirname(__DIR__) . '/core.php'));
define('JUFL_INC_PATH', JUFL_PLUGIN_PATH . 'inc');

?>