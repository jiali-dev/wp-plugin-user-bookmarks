<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Bookmark functions
// Get user bookmarks table
function jialiub_get_bookmarks_db() {
    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'jialiub_bookmarks');
    return $table_name;
}

// Get bookmarks
function jialiub_get_bookmarks() {
    global $wpdb;
    $table_name = jialiub_get_bookmarks_db();
    $results = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM $table_name")
    );
    return $results;
}

// Get user bookmarks count
function jialiub_get_user_bookmarks_count($user_id) {
    $count = get_user_meta( $user_id, 'jialiub_bookmarks_count', true );
    if ( empty( $count ) ) {
        $count = 0;
    }
    return $count;
}

// Get post bookmarks count
function jialiub_get_post_bookmarks_count($post_id) {
    $count = get_post_meta( $post_id, 'jialiub_bookmarks_count', true );
    if ( empty( $count ) ) {
        $count = 0;
    }
    return $count;
}

// Get user bookmarks
function jialiub_get_user_bookmarks($user_id) {
    global $wpdb;
    $table_name = jialiub_get_bookmarks_db();
    $results = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM $table_name WHERE user_id = %d", $user_id)
    );
    return $results;
}

// Exist user post bookmark     
function jialiub_bookmark_exist($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiub_get_bookmarks_db();
    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND post_id = %d", $user_id, $post_id));
    return $exists > 0;
}

// Add bookmark
function jialiub_add_bookmark($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiub_get_bookmarks_db();
    $insert = $wpdb->insert($table_name, array(
        'user_id' => absint($user_id),
        'post_id' => absint($post_id)
    ));
    return $insert;
}

// Remove bookmark
function jialiub_remove_bookmark($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiub_get_bookmarks_db();
    $delete = $wpdb->delete($table_name, array(
        'user_id' => absint($user_id),
        'post_id' => absint($post_id)
    ));
    return $delete;
}

// Toggle bookmark
function jialiub_toggle_bookmark($user_id, $post_id) {

    $exists = jialiub_bookmark_exist($user_id, $post_id);

    if ($exists) {
        $result = jialiub_remove_bookmark($user_id, $post_id);
        if( !is_wp_error( $result ) ) {
            $bookmarks_count = jialiub_get_post_bookmarks_count($post_id);
            $bookmarks_count--;
            update_post_meta($post_id, 'jialiub_bookmarks_count', $bookmarks_count);
            $user_bookmarks_count = jialiub_get_user_bookmarks_count($user_id);
            $user_bookmarks_count--;
            update_user_meta($user_id, 'jialiub_bookmarks_count', $user_bookmarks_count);
        }
    } else {
        $result = jialiub_add_bookmark($user_id, $post_id);
        if( !is_wp_error( $result ) ) {
            $bookmarks_count = jialiub_get_post_bookmarks_count($post_id);
            $bookmarks_count++;
            update_post_meta($post_id, 'jialiub_bookmarks_count', $bookmarks_count);
            $user_bookmarks_count = jialiub_get_user_bookmarks_count($user_id);
            $user_bookmarks_count++;
            update_user_meta($user_id, 'jialiub_bookmarks_count', $user_bookmarks_count);
        }
    }
    return $result;
}

// Create user bookmarks table
function jialiub_bookmarks_table() {
    global $wpdb;
    $table_name = jialiub_get_bookmarks_db();
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
function jialiub_create_bookmark_table() {
    jialiub_bookmarks_table();
}
register_activation_hook(plugin_basename(dirname(__DIR__) . '/core.php'), 'jialiub_create_bookmark_table');