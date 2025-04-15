<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define Constant
require_once( plugin_dir_path(__FILE__) . 'define_constants.php' );

// Add DB functions
require_once( JIALIUFL_INC_PATH . '/database-functions.php');

// Register theme assets
require_once( JIALIUFL_INC_PATH . '/register_assets.php');

// Register front view
require_once( JIALIUFL_INC_PATH . '/front.php');

// Register settings page
require_once( JIALIUFL_INC_PATH . '/settings.php');

// Register shortcodes 
require_once( JIALIUFL_INC_PATH . '/shortcodes.php');

// Register ajax function
require_once( JIALIUFL_INC_PATH . '/ajax-functions.php');

?>