<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Shortcode handler for displaying bookmark button
 *
 * Usage: [jialiub_bookmark_button]
 */
function jialiub_bookmark_button_shortcode($atts) {
    
    jialiub_enqueue_assets();

    return jialiub_get_bookmark_button_html();
}
add_shortcode('jialiub_bookmark_button', 'jialiub_bookmark_button_shortcode');
