<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Enqueue necessary scripts and styles once
 */
function jialiufl_enqueue_assets() {
    wp_enqueue_style('jialiufl-fontawesome');
    wp_enqueue_script('jialiufl-fontawesome');
    wp_enqueue_style('jialiufl-styles');
    wp_enqueue_script('jialiufl-main');
}

/**
 * Generate the like and favorite buttons based on settings
 *
 * @param string $post_type
 * @param array $enabled_for_like
 * @param array $enabled_for_fav
 * @return string
 */
function jialiufl_get_buttons_html($post_type, $enabled_for_like, $enabled_for_fav) {
    ob_start();
    ?>
    <div class="jialiufl-favorite-and-like-button">
        <?php if (in_array($post_type, $enabled_for_like)) : ?>
            <span class="jiali-like-button">
                <i class="fa-regular fa-heart"></i>
                <span class="jiali-like-count">123156</span>
            </span>
        <?php endif; ?>
        <?php if (in_array($post_type, $enabled_for_fav)) : ?>
            <span class="jiali-favorite-button">
                <i class="fa-regular fa-bookmark"></i>
                <span class="jiali-favorite-count">11000</span>
            </span>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Append buttons to content if post type is enabled
 *
 * @param string $content
 * @return string
 */
function jialiufl_append_buttons_to_content($content) {
    if (!is_singular()) return $content;

    global $post;
    $post_type = get_post_type($post);

    $enabled_for_like = get_option('jialiufl_enabled_post_types_for_like', []);
    $enabled_for_fav  = get_option('jialiufl_enabled_post_types_for_fav', []);

    // Exit early if nothing is enabled for this post type
    if (!in_array($post_type, $enabled_for_like) && !in_array($post_type, $enabled_for_fav)) {
        return $content;
    }

    jialiufl_enqueue_assets();

    $buttons_html = jialiufl_get_buttons_html($post_type, $enabled_for_like, $enabled_for_fav);
    $position = get_option('jialiufl_button_position', 'after');

    return ($position === 'before') ? $buttons_html . $content : $content . $buttons_html;
}
add_filter('the_content', 'jialiufl_append_buttons_to_content');
