<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubDatabaseInstaller {
    
    public static function installTables()
    {
        global $wpdb;
    
        $table_prefix = $wpdb->prefix;
        
        // Bookmark Table
        $bookmarks_table_name = esc_sql($table_prefix . 'jialiub_bookmarks');
        $charset_collate = $wpdb->get_charset_collate();
        $bookmarks_table_query = "CREATE TABLE IF NOT EXISTS $bookmarks_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id mediumint(9) NOT NULL,
            post_id mediumint(9) NOT NULL,
            category_id mediumint(9) DEFAULT NULL,
            created_at DATETIME NOT NULL,
            created_at_gmt DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY post_id (post_id),
            KEY category_id (category_id),
            KEY created_at (created_at),
            KEY created_at_gmt (created_at_gmt)
        ) $charset_collate;";

        // Bookmarks Categories Table
        $bookmarks_categories_table_name = esc_sql($table_prefix . 'jialiub_bookmarks_categories');
        $bookmarks_categories_table_query = "CREATE TABLE IF NOT EXISTS $bookmarks_categories_table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            user_id mediumint(9) NOT NULL,
            is_default tinyint(1) NOT NULL DEFAULT 0,
            is_private tinyint(1) NOT NULL DEFAULT 0,
            created_at DATETIME NOT NULL,
            created_at_gmt DATETIME NOT NULL,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        // Add all table queries to an array for installation
        $table_queries = [
            $bookmarks_table_query,
            $bookmarks_categories_table_query,
        ];

        // For calling dbDelta function
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($table_queries as $table_query) {
            dbDelta($table_query);
        }
    }
}