<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define Constant
require_once( plugin_dir_path(__FILE__) . 'define_constants.php' );

// Add bookmark functions
require_once( JIALIUB_INC_PATH . '/bookmark-functions.php');

// Register theme assets
require_once( JIALIUB_INC_PATH . '/register_assets.php');

// Register front view
require_once( JIALIUB_INC_PATH . '/front.php');

// Register settings page
require_once( JIALIUB_INC_PATH . '/settings.php');

// Register shortcodes 
require_once( JIALIUB_INC_PATH . '/shortcodes.php');

// Register ajax function
require_once( JIALIUB_INC_PATH . '/ajax-functions.php');

// Register custom table
// require_once( JIALIUB_INC_PATH . '/classes/Jialiub_Posts_List_Table.php');

?>