<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Shortcode handler for displaying bookmark button
 *
 * Usage: [jialiub_bookmark_button]
 */
function jialiub_bookmark_button_shortcode($atts) {
    return jialiub_bookmark_button_html();
}
add_shortcode('jialiub_bookmark_button', 'jialiub_bookmark_button_shortcode');

/**
 * Shortcode handler for displaying user's bookmarks table
 *
 * Usage: [jialiub_user_bookmarks_table]
 */
function jialiub_user_bookmarks_table_shortcode($atts) { 
    return jialiub_render_user_bookmarks_table();
}
add_shortcode('jialiub_user_bookmarks_table', 'jialiub_user_bookmarks_table_shortcode');

/**
 * Shortcode handler for displaying all bookmarks table
 *
 * Usage: [jialiub_all_bookmarks_table]
 */
function jialiub_all_bookmarks_table_shortcode($atts) { 
    return jialiub_render_all_bookmarks_table();
}
add_shortcode('jialiub_all_bookmarks_table', 'jialiub_all_bookmarks_table_shortcode');