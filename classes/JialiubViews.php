<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class JialiubViews {

    private static $instance = null;

    public static function getInstance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Hook into content
        add_filter( 'the_content', [ $this, 'appendButtonsToContent' ] );
    }

    /**
     * Enqueue required assets
     */
    private function enqueueAssets( $with_datatables = false ) {
        wp_enqueue_style('jialiub-fontawesome');
        wp_enqueue_script('jialiub-fontawesome');
        wp_enqueue_style('jialiub-notiflix');
        wp_enqueue_script('jialiub-notiflix');
        wp_enqueue_script('jialiub-notiflix-custom');
        wp_enqueue_style('jialiub-styles');
        wp_enqueue_script('jialiub-script');

        if ( $with_datatables ) {
            wp_enqueue_style('jialiub-datatable');
            wp_enqueue_script('jialiub-datatable');
            wp_enqueue_script('jialiub-datatable-custom');
        }
    }

    /**
     * Bookmark button HTML
     */
    public function bookmarkButtonHtml( $post, $just_icon = false ) {
        $this->enqueueAssets();

        if ( ! isset( $post ) ) return '';

        $post_type = get_post_type( $post );
        $enabled_for_bookmark = get_option( 'jialiub_bookmark_enabled_post_types', [] );

        if ( is_array( $enabled_for_bookmark ) && ! in_array( $post_type, $enabled_for_bookmark ) ) {
            return '';
        }

        ob_start(); ?>
        <div class="jialiub-bookmark" data-post-id="<?php echo esc_attr( $post->ID ); ?>">
            <div class="jialiub-bookmark-block">
                <?php if ( in_array( $post->post_type, $enabled_for_bookmark ) ) : 
                    $bookmarks_exist = JialiubBookmarkFunctions::getInstance()->bookmarkExists( get_current_user_id(), $post->ID ); ?>
                    
                    <span class="jialiub-bookmark-button <?php echo ( $bookmarks_exist ? 'jialiub-bookmark-button-active' : '' ); ?>" data-action="bookmark">
                        <i class="jialiub-icon <?php echo ( $bookmarks_exist ? 'fa-solid' : 'fa-regular' ); ?> fa-bookmark"></i>
                        <?php if ( ! $just_icon ): ?>
                            <?php if ( ! empty( get_option('jialiub_show_label') ) ): ?>
                                <span class="jialiub-bookmark-label">
                                    <?php echo esc_html( $bookmarks_exist ? JIALIUB_ACTION_LABEL : JIALIUB_SINGULAR_LABEL ); ?>
                                </span>
                            <?php endif; ?>
                            <span class="jialiub-bookmark-count">
                                <?php 
                                    $bookmarks_count = JialiubBookmarkFunctions::getInstance()->getPostBookmarksCount( $post->ID );
                                    echo esc_html( $bookmarks_count > 0 ? "($bookmarks_count)" : '' ); 
                                ?>
                            </span>
                        <?php endif; ?>
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Append buttons to content
     */
    public function appendButtonsToContent( $content ) {
        if ( ! is_singular() ) return $content;

        global $post;

        if ( has_shortcode( $post->post_content, 'jialiub_buttons' ) ) {
            return $content;
        }

        $buttons_html = $this->bookmarkButtonHtml( $post );
        $position = get_option( 'jialiub_button_position', 'after' );

        return ( $position === 'before' ) 
            ? $buttons_html . $content 
            : $content . $buttons_html;
    }

    /**
     * User bookmarks table
     */
    public function renderUserBookmarksTable() {
        $this->enqueueAssets( true );

        ob_start(); ?>
        <div class="table-responsive">
            <table class="jialiub-bookmarks-table jialiub-user-bookmarks-table table table-striped table-row-bordered display" role="grid">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Title', 'jiali-user-bookmarks'); ?></th>
                        <th><?php esc_html_e('Author', 'jiali-user-bookmarks'); ?></th>
                        <th><?php esc_html_e('Action', 'jiali-user-bookmarks'); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><?php esc_html_e('Title', 'jiali-user-bookmarks'); ?></th>
                        <th><?php esc_html_e('Author', 'jiali-user-bookmarks'); ?></th>
                        <th><?php esc_html_e('Action', 'jiali-user-bookmarks'); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Top bookmarks table (admin only)
     */
    public function renderTopBookmarksTable() {
        if ( ! current_user_can( 'manage_options' ) ) return '';

        $this->enqueueAssets( true );

        ob_start(); ?>
        <div class="table-responsive">
            <table class="jialiub-bookmarks-table jialiub-top-bookmarks-table table table-striped table-row-bordered display" role="grid">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Title', 'jiali-user-bookmarks'); ?></th>
                        <th><?php esc_html_e('Author', 'jiali-user-bookmarks'); ?></th>
                        <th><?php esc_html_e('Count', 'jiali-user-bookmarks'); ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><?php esc_html_e('Title', 'jiali-user-bookmarks'); ?></th>
                        <th><?php esc_html_e('Author', 'jiali-user-bookmarks'); ?></th>
                        <th><?php esc_html_e('Count', 'jiali-user-bookmarks'); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }

}