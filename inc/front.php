<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Enqueue necessary scripts and styles once
 */
function jialiufl_enqueue_assets() {
    wp_enqueue_style('jialiufl-fontawesome');
    wp_enqueue_script('jialiufl-fontawesome');
    wp_enqueue_style('jialiufl-notiflix');
    wp_enqueue_script('jialiufl-notiflix');
    wp_enqueue_script('jialiufl-notiflix-custom');
    wp_enqueue_style('jialiufl-styles');
    wp_enqueue_script('jialiufl-script');
}

/**
 * Generate the like and favorite buttons based on settings
 *
 * @param string $post_type
 * @param array $enabled_for_like
 * @param array $enabled_for_fav
 * @return string
 */
function jialiufl_get_buttons_html() {
    global $post;

    if (!isset($post)) return '';

    $post_type = get_post_type($post);

    $enabled_for_like = get_option('jialiufl_enabled_post_types_for_like', []);
    $enabled_for_fav  = get_option('jialiufl_enabled_post_types_for_favorite', []);

    // Exit early if not enabled for this post type
    if (!in_array($post_type, $enabled_for_like) && !in_array($post_type, $enabled_for_fav)) {
        return '';
    }

    ob_start();
    ?>
    <div class="jialiufl-favorite-and-like" data-post-id="<?php echo esc_attr($post->ID); ?>">
        <?php if (in_array($post->post_type, $enabled_for_like)) : 
            $like_exist = jialiufl_user_post_like_exist(get_current_user_id(  ), $post->ID ) ?>
            <span class="jialiufl-favorite-and-like-button <?php echo ( $like_exist ? 'jialiufl-favorite-and-like-button-active' : '' ) ?>" data-action="like">
                <i class="jialiufl-icon <?php echo $like_exist ? 'fa-solid' : 'fa-regular' ?> fa-heart"></i>
                <span class="jialiufl-like-count"><?php $likes_count = jialiufl_get_post_likes_count($post->ID); echo ( $likes_count > 0 ? $likes_count : '' ); ?></span>
            </span>
        <?php endif; ?>
        <?php if (in_array($post->post_type, $enabled_for_fav)) : 
            $favorites_exist = jialiufl_user_post_favorite_exist(get_current_user_id(  ), $post->ID ) ?>
            <span class="jialiufl-favorite-and-like-button <?php echo ( $favorites_exist ? 'jialiufl-favorite-and-like-button-active' : '' ) ?>" data-action="favorite">
                <i class="jialiufl-icon <?php echo ( $favorites_exist ? 'fa-solid' : 'fa-regular' ) ?> fa-bookmark"></i>
                <span class="jialiufl-favorite-count"><?php $favorites_count = jialiufl_get_post_favorites_count($post->ID); echo ( $favorites_count > 0 ? $favorites_count : '' ); ?></span>
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

    // Prevent double rendering if shortcode is used
    if (has_shortcode($post->post_content, 'jialiufl_buttons')) {
        return $content;
    }

    jialiufl_enqueue_assets();

    $buttons_html = jialiufl_get_buttons_html();

    $position = get_option('jialiufl_button_position', 'after');

    if ($position === 'before') {
        return $buttons_html . $content;
    } else {
        return $content . $buttons_html;
    }
}
add_filter('the_content', 'jialiufl_append_buttons_to_content');
