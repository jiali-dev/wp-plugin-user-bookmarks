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

        if ( !in_array($post_type, $bookmark_enabled_post_types) )
            throw new Exception( __( 'This post type is not enabled for this action!', 'jiali-user-bookmarks' ) , 403 );
        
        // Toggle user bookmark
        $result = jialiub_toggle_bookmark($user_id, $post_id);
        
        if( !is_wp_error( $result ) ) {
            $data['bookmark_exist'] = jialiub_bookmark_exist( $user_id, $post_id ); // Check if user has already bookmarked or bookmarked the post
            $data['bookmarks_count'] = jialiub_get_post_bookmarks_count($post_id);
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
