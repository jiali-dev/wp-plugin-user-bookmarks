<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Define Constant
require_once( plugin_dir_path(__FILE__) . 'define_constants.php' );


// Register theme assets
require_once( JUFL_INC_PATH . '/register_assets.php');

// Register front view
require_once( JUFL_INC_PATH . '/front.php');

// Register settings page
require_once( JUFL_INC_PATH . '/settings.php');

?>