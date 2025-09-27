<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Get hot posts
function jialiub_bookmark_toggle_ajax( ) {
    
    try {

        // Check nonce
        if ( empty($_POST['nonce']) || !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'jialiub-nonce' ) )
            throw new Exception( __( 'Security error!', 'jiali-user-bookmarks' ) , 403 );

        // Check user login
        if ( !is_user_logged_in(  ) )
            throw new Exception( __( 'You must be logged in to perform this action!', 'jiali-user-bookmarks' ) , 403 );

        // User ID
        $user_id = get_current_user_id(  );

        // Check post ID
        if ( empty($_POST['post_id']) || !isset($_POST['post_id']) )
            throw new Exception( __( 'Post ID is not set!', 'jiali-user-bookmarks' ) , 403 );

        // Get post ID
        $post_id = intval( $_POST['post_id'] );

        // Get post type
        $post_type = get_post_type( $post_id );

        // Check if post type is enabled for bookmark
        $bookmark_enabled_post_types = get_option('jialiub_bookmark_enabled_post_types', []);

        if (  is_array($bookmark_enabled_post_types) && !in_array($post_type, $bookmark_enabled_post_types) )
            throw new Exception( __( 'This post type is not enabled for this action!', 'jiali-user-bookmarks' ) , 403 );
        
        // Toggle user bookmark
        $result = JialiubBookmarkFunctions::getInstance()->toggleBookmark($user_id, $post_id);
        
        if( !is_wp_error( $result ) ) {
            $data['bookmark_exist'] = JialiubBookmarkFunctions::getInstance()->bookmarkExists( $user_id, $post_id );
            $data['bookmarks_count'] = JialiubBookmarkFunctions::getInstance()->getPostBookmarksCount($post_id);

            if( !empty(get_option('jialiub_show_label') ) ) {
                $data['bookmarks_label'] = ( $data['bookmark_exist'] ? JIALIUB_ACTION_LABEL : JIALIUB_SINGULAR_LABEL );
            } else {
                $data['bookmarks_label'] = '';
            }

        } else {
            throw new Exception( __( 'An unknown error is occured, Try again!', 'jiali-user-bookmarks' ) , 403 );
        }
        wp_send_json($data);
    } catch( Exception $ex ) {
        wp_send_json([
            'message' => $ex->getMessage()
        ], $ex->getCode() ? $ex->getCode() : 403);
    }   
}
add_action('wp_ajax_jialiub_bookmark_toggle_ajax', 'jialiub_bookmark_toggle_ajax');
add_action('wp_ajax_nopriv_jialiub_bookmark_toggle_ajax', 'jialiub_bookmark_toggle_ajax');

// Get User Bookmarks
function jialiub_get_user_bookmarks_ajax() {
    try {

        // Check nonce
        if ( empty($_POST['nonce']) || !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'jialiub-nonce' ) )
            throw new Exception( __( 'Security error!', 'jiali-user-bookmarks' ) , 403 );

        $paged = isset($_POST['start']) ? floor(intval($_POST['start']) / intval($_POST['length'])) + 1 : 1;
        $per_page = intval($_POST['length']) ?: 10;

        $user_id = get_current_user_id();
        $bookmarked_ids = JialiubBookmarkFunctions::getInstance()->getUserBookmarks($user_id);

        if(empty($bookmarked_ids)) {
            wp_send_json([
                'draw' => intval($_POST['draw']),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        $query = new WP_Query([
            'post__in'               => $bookmarked_ids,
            'post_type'              => 'any',
            'posts_per_page'         => $per_page,
            'paged'                  => $paged,
            'orderby'                => 'post__in',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'ignore_sticky_posts'    => true,
        ]);

        $data = [];
        foreach($query->posts as $post) {
            $data[] = [
                '<a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a>',
                get_the_author_meta('display_name', $post->post_author),
            ];
        }

        wp_send_json([
            'draw' => intval($_POST['draw']),
            'recordsTotal' => count($bookmarked_ids),
            'recordsFiltered' => count($bookmarked_ids),
            'data' => $data
        ]);
        
    } catch( Exception $ex ) {
        error_log('Jialiub AJAX error: ' . $e->getMessage());
        wp_send_json([
            'draw' => intval($_POST['draw'] ?? 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ], $ex->getCode() ? $ex->getCode() : 403);
    }  
}
add_action('wp_ajax_jialiub_get_user_bookmarks_ajax', 'jialiub_get_user_bookmarks_ajax');
add_action('wp_ajax_nopriv_jialiub_get_user_bookmarks_ajax', 'jialiub_get_user_bookmarks_ajax');

// Get All Bookmarks
function jialiub_get_all_bookmarks_ajax() {
    try {

        // Check nonce
        if ( empty($_POST['nonce']) || !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'jialiub-nonce' ) )
            throw new Exception( __( 'Security error!', 'jiali-user-bookmarks' ) , 403 );

        $paged = isset($_POST['start']) ? floor(intval($_POST['start']) / intval($_POST['length'])) + 1 : 1;
        $per_page = intval($_POST['length']) ?: 10;

        $user_id = get_current_user_id();
        $bookmarked_ids = JialiubBookmarkFunctions::getInstance()->getAllBookmarks();

        if(empty($bookmarked_ids)) {
            wp_send_json([
                'draw' => intval($_POST['draw']),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }

        $query = new WP_Query([
            'post__in'               => $bookmarked_ids,
            'post_type'              => 'any',
            'posts_per_page'         => $per_page,
            'paged'                  => $paged,
            'orderby'                => 'post__in',
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
            'ignore_sticky_posts'    => true,
        ]);

        $data = [];
        foreach($query->posts as $post) {
            $data[] = [
                '<a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a>',
                get_the_author_meta('display_name', $post->post_author),
                JialiubBookmarkFunctions::getInstance()->getPostBookmarksCount($post->ID),
            ];
        }

        wp_send_json([
            'draw' => intval($_POST['draw']),
            'recordsTotal' => count($bookmarked_ids),
            'recordsFiltered' => count($bookmarked_ids),
            'data' => $data
        ]);
        
    } catch( Exception $ex ) {
        error_log('Jialiub AJAX error: ' . $e->getMessage());
        wp_send_json([
            'draw' => intval($_POST['draw'] ?? 1),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => [],
        ], $ex->getCode() ? $ex->getCode() : 403);
    }  
}
add_action('wp_ajax_jialiub_get_all_bookmarks_ajax', 'jialiub_get_all_bookmarks_ajax');
add_action('wp_ajax_nopriv_jialiub_get_all_bookmarks_ajax', 'jialiub_get_all_bookmarks_ajax');