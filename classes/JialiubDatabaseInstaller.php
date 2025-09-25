<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubDatabaseInstaller {
    
    public static function installTables()
    {
        global $wpdb;
    
        $table_prefix = $wpdb->prefix;
        
        // Bookmark Table
        $table_name = esc_sql($table_prefix . 'jialiub_bookmarks');
        $charset_collate = $wpdb->get_charset_collate();
        $bookmarks_table_query = "CREATE TABLE IF NOT EXISTS $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id mediumint(9) NOT NULL,
            post_id mediumint(9) NOT NULL,
            date_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id),
            KEY post_id (post_id),
            KEY date_added (date_added)
        ) $charset_collate;";


        // Add all table queries to an array for installation
        $table_queries = [
            $bookmarks_table_query,
        ];

        // For calling dbDelta function
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach ($table_queries as $table_query) {
            dbDelta($table_query);
        }
    }
}