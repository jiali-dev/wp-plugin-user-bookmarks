<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Enqueue necessary scripts and styles once
 */
function jialiub_enqueue_assets() {
    wp_enqueue_style('jialiub-fontawesome');
    wp_enqueue_script('jialiub-fontawesome');
    wp_enqueue_style('jialiub-notiflix');
    wp_enqueue_script('jialiub-notiflix');
    wp_enqueue_script('jialiub-notiflix-custom');
    wp_enqueue_style('jialiub-styles');
    wp_enqueue_script('jialiub-script');
}

/**
 * Generate the bookmark buttons based on settings
 *
 * @param string $post_type
 * @param array $enabled_for_bookmark
 * @return string
 */
function jialiub_get_bookmark_button_html() {
    global $post;

    if (!isset($post)) return '';

    $post_type = get_post_type($post);

    $enabled_for_bookmark  = get_option('jialiub_bookmark_enabled_post_types', []);

    // Exit early if not enabled for this post type
    if ( is_array($enabled_for_bookmark) && !in_array($post_type, $enabled_for_bookmark)) {
        return '';
    }

    ob_start();
    ?>
    <div class="jialiub-bookmark" data-post-id="<?php echo esc_attr($post->ID); ?>">
        <?php if( is_array($enabled_for_bookmark) && in_array($post->post_type, $enabled_for_bookmark)) : 
            $bookmarks_exist = jialiub_bookmark_exist(get_current_user_id(  ), $post->ID ) ?>
            <span class="jialiub-bookmark-button <?php echo ( $bookmarks_exist ? 'jialiub-bookmark-button-active' : '' ) ?>" data-action="bookmark">
                <i class="jialiub-icon <?php echo ( $bookmarks_exist ? 'fa-solid' : 'fa-regular' ) ?> fa-bookmark"></i>
                <span class="jialiub-bookmark-count"><?php $bookmarks_count = jialiub_get_post_bookmarks_count($post->ID); echo ( $bookmarks_count > 0 ? $bookmarks_count : '' ); ?></span>
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
function jialiub_append_buttons_to_content($content) {
    
    if (!is_singular()) return $content;

    global $post;

    // Prevent double rendering if shortcode is used
    if (has_shortcode($post->post_content, 'jialiub_buttons')) {
        return $content;
    }

    jialiub_enqueue_assets();

    $buttons_html = jialiub_get_bookmark_button_html();

    $position = get_option('jialiub_button_position', 'after');

    if ($position === 'before') {
        return $buttons_html . $content;
    } else {
        return $content . $buttons_html;
    }
}
add_filter('the_content', 'jialiub_append_buttons_to_content');
