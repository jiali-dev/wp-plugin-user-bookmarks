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

// Favorite functions
// Get user favorites table
function jialiufl_get_favorites_db() {
    global $wpdb;
    $table_name = esc_sql($wpdb->prefix . 'jialiufl_favorites');
    return $table_name;
}

// Get favorites
function jialiufl_get_favorites() {
    global $wpdb;
    $table_name = jialiufl_get_favorites_db();
    $results = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM $table_name")
    );
    return $results;
}

// Get user favorites count
function jialiufl_get_user_favorites_count($user_id) {
    $count = get_user_meta( $user_id, 'jialiufl_favorites_count', true );
    if ( empty( $count ) ) {
        $count = 0;
    }
    return $count;
}

// Get post favorites count
function jialiufl_get_post_favorites_count($post_id) {
    $count = get_post_meta( $user_id, 'jialiufl_favorites_count', true );
    if ( empty( $count ) ) {
        $count = 0;
    }
    return $count;
}

// Get user favorites
function jialiufl_get_user_favorites($user_id) {
    global $wpdb;
    $table_name = jialiufl_get_favorites_db();
    $results = $wpdb->get_col(
        $wpdb->prepare("SELECT post_id FROM $table_name WHERE user_id = %d", $user_id)
    );
    return $results;
}

// Exist user post favorite     
function jialiufl_user_post_favorite_exist($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiufl_get_favorites_db();
    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = %d AND post_id = %d", $user_id, $post_id));
    return $exists > 0;
}

// Add favorite
function jialiufl_add_favorite($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiufl_get_favorites_db();
    $insert = $wpdb->insert($table_name, array(
        'user_id' => absint($user_id),
        'post_id' => absint($post_id)
    ));
    return $insert;
}

// Remove favorite
function jialiufl_remove_favorite($user_id, $post_id) {
    global $wpdb;
    $table_name = jialiufl_get_favorites_db();
    $delete = $wpdb->delete($table_name, array(
        'user_id' => absint($user_id),
        'post_id' => absint($post_id)
    ));
    return $delete;
}

// Toggle favorite
function jialiufl_toggle_favorite($user_id, $post_id) {

    $exists = jialiufl_user_post_favorite_exist($user_id, $post_id);

    if ($exists) {
        $result = jialiufl_remove_favorite($user_id, $post_id);
        if( !is_wp_error( $result ) ) {
            $favorites_count = jialiufl_get_post_favorites_count($post_id);
            $favorites_count--;
            update_post_meta($post_id, 'jialiufl_favorites_count', $favorites_count);
            $user_favorites_count = jialiufl_get_user_favorites_count($user_id);
            $user_favorites_count--;
            update_user_meta($user_id, 'jialiufl_favorites_count', $user_favorites_count);
        }
    } else {
        $result = jialiufl_add_favorite($user_id, $post_id);
        if( !is_wp_error( $result ) ) {
            $favorites_count = jialiufl_get_post_favorites_count($post_id);
            $favorites_count++;
            update_post_meta($post_id, 'jialiufl_favorites_count', $favorites_count);
            $user_favorites_count = jialiufl_get_user_favorites_count($user_id);
            $user_favorites_count++;
            update_user_meta($user_id, 'jialiufl_favorites_count', $user_favorites_count);
        }
    }
    return $result;
}

// Create favorites and likes tables
// Create user favorites table
function jialiufl_favorites_table() {
    global $wpdb;
    $table_name = jialiufl_get_favorites_db();
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
    jialiufl_favorites_table();
    jialiufl_likes_table();
}
register_activation_hook(plugin_basename(dirname(__DIR__) . '/core.php'), 'jialiufl_create_tables');