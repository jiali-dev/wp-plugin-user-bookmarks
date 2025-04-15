<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Get hot posts
function jialiufl_favorite_and_like_toggle_ajax( ) {
    
    try {

        // Check nonce
        if ( empty($_POST['nonce']) || !isset($_POST['nonce']) || !wp_verify_nonce( $_POST['nonce'], 'jialiufl-nonce' ) )
            throw new Exception( __( 'Security error!', 'jiali-user-favorites-and-likes' ) , 403 );

        // Check user login
        if ( !is_user_logged_in(  ) )
            throw new Exception( __( 'You must be logged in to perform this action!', 'jiali-user-favorites-and-likes' ) , 403 );

        // User ID
        $user_id = get_current_user_id(  );

        // Check post ID
        if ( empty($_POST['post_id']) || !isset($_POST['post_id']) )
            throw new Exception( __( 'Post ID is not set!', 'jiali-user-favorites-and-likes' ) , 403 );

        // Get post ID
        $post_id = intval( $_POST['post_id'] );

        // Check user action
        if ( empty($_POST['action']) || !isset($_POST['action']) )
            throw new Exception( __( 'Action is not set!', 'jiali-user-favorites-and-likes' ) , 403 );

        // Get user action
        $user_action = sanitize_text_field( $_POST['user_action'] );

        // Get post type
        $post_type = get_post_type( $post_id );

        // Check if post type is enabled for like
        $enabled_for_user_action = get_option('jialiufl_enabled_post_types_for_'.$user_action, []);

        if ( !in_array($post_type, $enabled_for_user_action) )
            throw new Exception( __( 'This post type is not enabled for this action!', 'jiali-user-favorites-and-likes' ) , 403 );
        
        // Check if user has already liked or favorited the post
        $user_action_exist = "jialiufl_user_post_{$user_action}_exist"( $user_id, $post_id );

        // Toggle user action
        $result = "jialiufl_toggle_{$user_action}"($user_id, $post_id);
        
        if( !is_wp_error( $result )) {
            $data['user_action_exist'] = $user_action_exist;
            $data['user_action_count'] = "jialiufl_get_post_{$user_action}s_count"($post_id);
        } else {
            throw new Exception( __( 'An unknown error is occured, Try again!', 'jiali-user-favorites-and-likes' ) , 403 );
        }
        wp_send_json($data);
    } catch( Exception $ex ) {
        wp_send_json([
            'message' => $ex->getMessage()
        ], $ex->getCode() ? $ex->getCode() : 403);
    }   
}
add_action('wp_ajax_jialiufl_favorite_and_like_toggle_ajax', 'jialiufl_favorite_and_like_toggle_ajax');
add_action('wp_ajax_nopriv_jialiufl_favorite_and_like_toggle_ajax', 'jialiufl_favorite_and_like_toggle_ajax');
