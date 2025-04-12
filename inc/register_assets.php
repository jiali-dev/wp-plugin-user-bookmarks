<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Add theme assets --> Register for globaly and enqueue for specific page
function jialiufl_register_assets() {
    // Register styles
    wp_register_style('jialiufl-styles', JIALIUFL_CSS_URI . '/styles.css' , array(), '1.0.0', 'all');

    // Register scripts
    wp_register_script('jialiufl-main', JIALIUFL_JS_URI . '/main.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'jialiufl_register_assets');

?>