<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Generate the bookmark buttons based on settings
 *
 * @param string $post_type
 * @param array $enabled_for_bookmark
 * @return string
 */
function jialiub_bookmark_button_html() {

    wp_enqueue_style('jialiub-fontawesome');
    wp_enqueue_script('jialiub-fontawesome');
    wp_enqueue_style('jialiub-notiflix');
    wp_enqueue_script('jialiub-notiflix');
    wp_enqueue_script('jialiub-notiflix-custom');
    wp_enqueue_style('jialiub-styles');
    wp_enqueue_script('jialiub-script');

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

    wp_enqueue_style('jialiub-datatable');
    wp_enqueue_script('jialiub-datatable');
    wp_enqueue_script('jialiub-datatable-custom');
    wp_enqueue_style('jialiub-styles');

    ob_start();
    ?>

    <h2><?php sprintf( esc_html__('User %s', 'jiali-user-bookmarks'), JIALIUB_PLURAL_LABEL ) ?></h2>
    <div class="table-responsive">
        <table class="jialiub-bookmarks-table table table-striped table-row-bordered display" id="jialiub-bookmarks-table" role="grid">
            <thead>
                <tr>
                    <th><?php esc_html_e('Title', 'jiali-user-bookmarks'); ?></th>
                    <th><?php esc_html_e('Author', 'jiali-user-bookmarks'); ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th><?php esc_html_e('Title', 'jiali-user-bookmarks'); ?></th>
                    <th><?php esc_html_e('Author', 'jiali-user-bookmarks'); ?></th>
                </tr>
            </tfoot>
        </table>

    </div>
    <?php
    return ob_get_clean();
}
