<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Shortcode handler for displaying like and favorite buttons
 *
 * Usage: [jialiufl_buttons]
 */
function jialiufl_buttons_shortcode($atts) {
    

    jialiufl_enqueue_assets();

    return jialiufl_get_buttons_html();
}
add_shortcode('jialiufl_buttons', 'jialiufl_buttons_shortcode');
