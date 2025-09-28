<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubShortcodes {

    private static $instance = null;

    /**
     * Singleton pattern: getInstance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor: register shortcodes
     */
    private function __construct() {
        add_shortcode('jialiub_bookmark_button', [ $this, 'bookmarkButton' ]);
        add_shortcode('jialiub_user_bookmarks_table', [ $this, 'userBookmarksTable' ]);
        add_shortcode('jialiub_top_bookmarks_table', [ $this, 'topBookmarksTable' ]);
    }

    /**
     * Shortcode handler for displaying bookmark button
     * Usage: [jialiub_bookmark_button]
     */
    public function bookmarkButton($atts) {
        global $post;
        return jialiub_bookmark_button_html($post);
    }

    /**
     * Shortcode handler for displaying user's bookmarks table
     * Usage: [jialiub_user_bookmarks_table]
     */
    public function userBookmarksTable($atts) {
        return jialiub_render_user_bookmarks_table();
    }

    /**
     * Shortcode handler for displaying top bookmarks table
     * Usage: [jialiub_top_bookmarks_table]
     */
    public function topBookmarksTable($atts) {
        return jialiub_render_top_bookmarks_table();
    }
}
