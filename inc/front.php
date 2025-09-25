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
function jialiub_bookmark_button_html() {

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
            <div class="jialiub-bookmark-block" >
                <?php if( is_array($enabled_for_bookmark) && in_array($post->post_type, $enabled_for_bookmark)) : 
                    $bookmarks_exist = JialiubBookmarkFunctions::getInstance()->bookmarkExists(get_current_user_id(  ), $post->ID ) ?>
                    <span class="jialiub-bookmark-button <?php echo ( $bookmarks_exist ? 'jialiub-bookmark-button-active' : '' ) ?>" data-action="bookmark">
                        <i class="jialiub-icon <?php echo ( $bookmarks_exist ? 'fa-solid' : 'fa-regular' ) ?> fa-bookmark"></i>
                        <?php if( !empty(get_option('jialiub_show_label') ) ):  ?>
                            <span class="jialiub-bookmark-label">
                                <?php echo ( $bookmarks_exist ? JIALIUB_ACTION_LABEL : JIALIUB_SINGULAR_LABEL ); ?>
                            </span>
                        <?php endif ?>
                        <span class="jialiub-bookmark-count">
                            <?php 
                                $bookmarks_count = JialiubBookmarkFunctions::getInstance()->getPostBookmarksCount($post->ID);
                                echo ( $bookmarks_count > 0 ? "($bookmarks_count)" : '' ); 
                            ?>
                        </span>
                    </span>
                <?php endif; ?>
            </div>
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

    $buttons_html = jialiub_bookmark_button_html();

    $position = get_option('jialiub_button_position', 'after');

    if ($position === 'before') {
        return $buttons_html . $content;
    } else {
        return $content . $buttons_html;
    }
}
add_filter('the_content', 'jialiub_append_buttons_to_content');

/**
 * Render the user's bookmarks
 *
 * @return string
 */
function jialiub_render_user_bookmarks_table( ) {

    $user_id = get_current_user_id();
    $post_ids = JialiubBookmarkFunctions::getInstance()->getUserBookmarks($user_id);
    $bookmarks = new WP_Query([
        'post__in' => ( empty($post_ids) ? [0] : $post_ids ),
        'post_type' => 'any',
        'posts_per_page' => -1,
        'orderby' => 'post__in',
        'update_post_meta_cache' => false, 
        'update_post_term_cache' => false,
        'ignore_sticky_posts' => true 
    ]);

    if ( empty( $bookmarks ) ) {
        return '<p>' . esc_html__( 'No bookmarks found.', 'jiali-user-bookmarks' ) . '</p>';
    }

    $bookmarks = wp_list_pluck( $bookmarks->posts, 'ID' );

    ob_start();
    ?>
    <div class="table-responsive">
        <table class="table data-table tablesorter jialiub-bookmarks-table" role="grid">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Title', 'jiali-user-bookmarks' ); ?></th>
                    <th><?php esc_html_e( 'Type', 'jiali-user-bookmarks' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $bookmarks as $bookmark ) :
                    $post = get_post( $bookmark );
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo esc_url( get_permalink( $post ) ); ?>">
                                <?php echo esc_html( get_the_title( $post ) ); ?>
                            </a>
                        </td>
                        <td>
                            <?php echo esc_html( get_post_type_object( $post->post_type )->labels->singular_name ); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}
