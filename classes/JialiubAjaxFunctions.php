<?php
if (!defined('ABSPATH')) exit;

class JialiubAjaxFunctions {
    
    private static $instance = null;

    private function __construct() {
        // Toggle bookmark
        add_action('wp_ajax_jialiub_bookmark_toggle_ajax', [$this, 'toggleBookmark']);
        add_action('wp_ajax_nopriv_jialiub_bookmark_toggle_ajax', [$this, 'toggleBookmark']);

        // Get user bookmarks
        add_action('wp_ajax_jialiub_get_user_bookmarks_ajax', [$this, 'getUserBookmarks']);
        add_action('wp_ajax_nopriv_jialiub_get_user_bookmarks_ajax', [$this, 'getUserBookmarks']);

        // Get top bookmarks
        add_action('wp_ajax_jialiub_get_top_bookmarks_ajax', [$this, 'getTopBookmarks']);
        add_action('wp_ajax_nopriv_jialiub_get_top_bookmarks_ajax', [$this, 'getTopBookmarks']);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function verifyNonce() {
        $nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : '';
        if (empty($nonce) || !wp_verify_nonce($nonce, 'jialiub-nonce')) {
            throw new Exception(__('Security error!', 'jiali-user-bookmarks'), 403);
        }
    }

    public function toggleBookmark() {
        try {
            $this->verifyNonce();

            if (!is_user_logged_in()) {
                throw new Exception(__('You must be logged in to perform this action!', 'jiali-user-bookmarks'), 403);
            }

            $user_id = get_current_user_id();

            if (empty($_POST['post_id'])) {
                throw new Exception(__('Post ID is not set!', 'jiali-user-bookmarks'), 403);
            }

            $post_id   = intval($_POST['post_id']);
            $post_type = get_post_type($post_id);

            $enabled_types = get_option('jialiub_bookmark_enabled_post_types', []);
            if (is_array($enabled_types) && !in_array($post_type, $enabled_types)) {
                throw new Exception(__('This post type is not enabled for this action!', 'jiali-user-bookmarks'), 403);
            }

            $result = JialiubBookmarkFunctions::getInstance()->toggleBookmark($user_id, $post_id);

            if (!is_wp_error($result)) {
                $data['bookmark_exist']  = JialiubBookmarkFunctions::getInstance()->bookmarkExists($user_id, $post_id);
                $data['bookmarks_count'] = JialiubBookmarkFunctions::getInstance()->getPostBookmarksCount($post_id);

                if (!empty(get_option('jialiub_show_label'))) {
                    $data['bookmarks_label'] = $data['bookmark_exist'] ? JIALIUB_ACTION_LABEL : JIALIUB_SINGULAR_LABEL;
                } else {
                    $data['bookmarks_label'] = '';
                }

                wp_send_json($data);
            } else {
                throw new Exception(__('An unknown error occurred, try again!', 'jiali-user-bookmarks'), 403);
            }
        } catch (Exception $ex) {
            wp_send_json(['message' => $ex->getMessage()], $ex->getCode() ?: 403);
        }
    }

    public function getUserBookmarks() {
        try {
            $this->verifyNonce();

            $length = isset($_POST['length']) ? intval($_POST['length']) : 10;
            $paged  = isset($_POST['start']) ? floor(intval($_POST['start']) / $length) + 1 : 1;
            $draw   = isset($_POST['draw']) ? intval($_POST['draw']) : 1;

            $user_id        = get_current_user_id();
            $bookmarked_ids = JialiubBookmarkFunctions::getInstance()->getUserBookmarks($user_id);

            if (empty($bookmarked_ids)) {
                wp_send_json([
                    'draw'            => $draw,
                    'recordsTotal'    => 0,
                    'recordsFiltered' => 0,
                    'data'            => [],
                ]);
            }

            $query = new WP_Query([
                'post__in'               => $bookmarked_ids,
                'post_type'              => 'any',
                'posts_per_page'         => $length,
                'paged'                  => $paged,
                'orderby'                => 'post__in',
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'ignore_sticky_posts'    => true,
            ]);

            $data = [];
            foreach ($query->posts as $post) {
                $data[] = [
                    '<a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a>',
                    get_the_author_meta('display_name', $post->post_author),
                    JialiubViews::getInstance()->bookmarkButtonHtml($post, true),
                ];
            }

            wp_send_json([
                'draw'            => $draw,
                'recordsTotal'    => count($bookmarked_ids),
                'recordsFiltered' => count($bookmarked_ids),
                'data'            => $data,
            ]);
        } catch (Exception $ex) {
            wp_send_json([
                'draw'            => intval($_POST['draw'] ?? 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
            ], $ex->getCode() ?: 403);
        }
    }

    public function getTopBookmarks() {
        try {
            $this->verifyNonce();

            $draw            = isset($_POST['draw']) ? intval($_POST['draw']) : 1;
            $bookmarked_ids  = JialiubBookmarkFunctions::getInstance()->getTopBookmarks();

            if (empty($bookmarked_ids)) {
                wp_send_json([
                    'draw'            => $draw,
                    'recordsTotal'    => 0,
                    'recordsFiltered' => 0,
                    'data'            => [],
                ]);
            }

            $query = new WP_Query([
                'post__in'               => $bookmarked_ids,
                'post_type'              => 'any',
                'posts_per_page'         => -1,
                'orderby'                => 'post__in',
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
                'ignore_sticky_posts'    => true,
            ]);

            $data = [];
            foreach ($query->posts as $post) {
                $data[] = [
                    '<a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a>',
                    get_the_author_meta('display_name', $post->post_author),
                    JialiubBookmarkFunctions::getInstance()->getPostBookmarksCount($post->ID),
                ];
            }

            wp_send_json([
                'draw'            => $draw,
                'recordsTotal'    => count($bookmarked_ids),
                'recordsFiltered' => count($bookmarked_ids),
                'data'            => $data,
            ]);
        } catch (Exception $ex) {
            wp_send_json([
                'draw'            => intval($_POST['draw'] ?? 1),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
            ], $ex->getCode() ?: 403);
        }
    }
}
