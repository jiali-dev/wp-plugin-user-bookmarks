<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Likes functions
// Get user likes table
function jialiufl_get_likes_db() {
    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'jialiufl_likes');
    return $table_name;
}

// Get likes
function jialiufl_get_likes() {
    global $wpdb;
    $table_name = jialiufl_get_likes_db();
    $results = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM $table_name")
    );
    return $results;
}

// Get user likes count
function jialiufl_get_user_likes_count($user_id) {
    $count = get_user_meta( $user_id, 'jialiufl_likes_count', true );
    if ( empty( $count ) ) {
        $count = 0;
    }
    return $count;
}

// Get user likes count
function jialiufl_get_post_likes_count($post_id) {
    $count = get_post_meta( $post_id, 'jialiufl_likes_count', true );
    if ( empty( $count ) ) {
        $count = 0;
    }
    return $count;
}

// Get user likes
function jialiufl_get_user_likes($user_id) {
    global $wpdb;
    $table_name = jialiufl_get_likes_db();
    $results = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM $table_name WHERE user_id = %d", $user_id)
    );
    return $results;
}

// Exist user post like
function jialiufl_user_post_like_exist($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiufl_get_likes_db();
    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND post_id = %d", $user_id, $post_id));
    return $exists > 0;
}

// Add like
function jialiufl_add_like($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiufl_get_likes_db();
    $insert = $wpdb->insert($table_name, array(
        'user_id' => absint($user_id),
        'post_id' => absint($post_id)
    ));
    return $insert;
}

// Remove like
function jialiufl_remove_like($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiufl_get_likes_db();
    $delete = $wpdb->delete($table_name, array(
        'user_id' => absint($user_id),
        'post_id' => absint($post_id)
    ));
    return $delete;
}

// Toggle like 
function jialiufl_toggle_like($user_id, $post_id) {

    $exists =jialiufl_user_post_like_exist($user_id, $post_id);

    if ($exists) {
        $result = jialiufl_remove_like($user_id, $post_id);
        if( !is_wp_error( $result ) ) {
            $likes_count = jialiufl_get_post_likes_count($post_id);
            $likes_count--;
            update_post_meta($post_id, 'jialiufl_likes_count', $likes_count);
            $user_likes_count = jialiufl_get_user_likes_count($user_id);
            $user_likes_count--;
            update_user_meta($user_id, 'jialiufl_likes_count', $user_likes_count);
        }
    } else {
        $result = jialiufl_add_like($user_id, $post_id);
        if( !is_wp_error( $result ) ) {
            $likes_count = jialiufl_get_post_likes_count($post_id);
            $likes_count++;
            update_post_meta($post_id, 'jialiufl_likes_count', $likes_count);
            $user_likes_count = jialiufl_get_user_likes_count($user_id);
            $user_likes_count++;
            update_user_meta($user_id, 'jialiufl_likes_count', $user_likes_count);
        }
    }
    return $result;
}

// Bookmark functions
// Get user bookmarks table
function jialiufl_get_bookmarks_db() {
    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'jialiufl_bookmarks');
    return $table_name;
}

// Get bookmarks
function jialiufl_get_bookmarks() {
    global $wpdb;
    $table_name = jialiufl_get_bookmarks_db();
    $results = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM $table_name")
    );
    return $results;
}

// Get user bookmarks count
function jialiufl_get_user_bookmarks_count($user_id) {
    $count = get_user_meta( $user_id, 'jialiufl_bookmarks_count', true );
    if ( empty( $count ) ) {
        $count = 0;
    }
    return $count;
}

// Get post bookmarks count
function jialiufl_get_post_bookmarks_count($post_id) {
    $count = get_post_meta( $post_id, 'jialiufl_bookmarks_count', true );
    if ( empty( $count ) ) {
        $count = 0;
    }
    return $count;
}

// Get user bookmarks
function jialiufl_get_user_bookmarks($user_id) {
    global $wpdb;
    $table_name = jialiufl_get_bookmarks_db();
    $results = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM $table_name WHERE user_id = %d", $user_id)
    );
    return $results;
}

// Exist user post bookmark     
function jialiufl_user_post_bookmark_exist($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiufl_get_bookmarks_db();
    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND post_id = %d", $user_id, $post_id));
    return $exists > 0;
}

// Add bookmark
function jialiufl_add_bookmark($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiufl_get_bookmarks_db();
    $insert = $wpdb->insert($table_name, array(
        'user_id' => absint($user_id),
        'post_id' => absint($post_id)
    ));
    return $insert;
}

// Remove bookmark
function jialiufl_remove_bookmark($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiufl_get_bookmarks_db();
    $delete = $wpdb->delete($table_name, array(
        'user_id' => absint($user_id),
        'post_id' => absint($post_id)
    ));
    return $delete;
}

// Toggle bookmark
function jialiufl_toggle_bookmark($user_id, $post_id) {

    $exists = jialiufl_user_post_bookmark_exist($user_id, $post_id);

    if ($exists) {
        $result = jialiufl_remove_bookmark($user_id, $post_id);
        if( !is_wp_error( $result ) ) {
            $bookmarks_count = jialiufl_get_post_bookmarks_count($post_id);
            $bookmarks_count--;
            update_post_meta($post_id, 'jialiufl_bookmarks_count', $bookmarks_count);
            $user_bookmarks_count = jialiufl_get_user_bookmarks_count($user_id);
            $user_bookmarks_count--;
            update_user_meta($user_id, 'jialiufl_bookmarks_count', $user_bookmarks_count);
        }
    } else {
        $result = jialiufl_add_bookmark($user_id, $post_id);
        if( !is_wp_error( $result ) ) {
            $bookmarks_count = jialiufl_get_post_bookmarks_count($post_id);
            $bookmarks_count++;
            update_post_meta($post_id, 'jialiufl_bookmarks_count', $bookmarks_count);
            $user_bookmarks_count = jialiufl_get_user_bookmarks_count($user_id);
            $user_bookmarks_count++;
            update_user_meta($user_id, 'jialiufl_bookmarks_count', $user_bookmarks_count);
        }
    }
    return $result;
}

// Create bookmarks and likes tables
// Create user bookmarks table
function jialiufl_bookmarks_table() {
    global $wpdb;
    $table_name = jialiufl_get_bookmarks_db();
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        post_id mediumint(9) NOT NULL,
        date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY user_id (user_id),
        KEY post_id (post_id),
        KEY date_added (date_added)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Create user likes table
function jialiufl_likes_table() {
    global $wpdb;
    $table_name = jialiufl_get_likes_db();
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        user_id mediumint(9) NOT NULL,
        post_id mediumint(9) NOT NULL,
        date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id),
        KEY user_id (user_id),
        KEY post_id (post_id),
        KEY date_added (date_added)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Create tables on plugin activation
function jialiufl_create_tables() {
    jialiufl_bookmarks_table();
    jialiufl_likes_table();
}
register_activation_hook(plugin_basename(dirname(__DIR__) . '/core.php'), 'jialiufl_create_tables');