<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubBookmarkFunctions {
    
    protected static $instance = null;
    protected $table_name;
    protected $category_table_name;

    private function __construct() {
        global $wpdb;
        $this->table_name = esc_sql($wpdb->prefix . 'jialiub_bookmarks');
        $this->category_table_name = esc_sql($wpdb->prefix . 'jialiub_bookmarks_categories');
    }

    private function __clone() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get all bookmarks
     * @return array
    */
    public function getTopBookmarks() {
        global $wpdb;

        // Use cache key based on table name
        $cache_key = 'top_bookmarks';
        $results = wp_cache_get( $cache_key, 'jialiub_bookmarks' );

        if ( false === $results ) {
            $results = $wpdb->get_col(
                "SELECT post_id 
                FROM $this->table_name
                GROUP BY post_id 
                ORDER BY COUNT(*) DESC 
                LIMIT 10"
            );

            wp_cache_set( $cache_key, $results, 'jialiub_bookmarks', 10 * MINUTE_IN_SECONDS );
        }

        return $results;
    }

    /**
     * Get bookmarks count for a specific user
     *
     * @param int $user_id
     * @return int
    */
    public function getUserBookmarksCount($user_id) {
        $count = get_user_meta( $user_id, 'jialiub_bookmarks_count', true );
        if ( empty( $count ) ) {
            $count = 0;
        }
        return $count;
    }

    /**
     * Get bookmarks count for a specific post
     *
     * @param int $post_id
     * @return int
    */
    public function getPostBookmarksCount($post_id) {
        $count = get_post_meta( $post_id, 'jialiub_bookmarks_count', true );
        if ( empty( $count ) ) {
            $count = 0;
        }
        return $count;
    }

    /**
     * Get bookmarks for a specific user
     *
     * @param int $user_id
     * @return array
     */
    public function getUserBookmarks($user_id) {
        global $wpdb; 

        // Use cache key based on table name
        $cache_key = 'user_bookmarks';
        $results = wp_cache_get( $cache_key, 'jialiub_bookmarks' );

        if ( false === $results ) {
            $results = $wpdb->get_col(
                $wpdb->prepare(
                    "SELECT post_id 
                    FROM $this->table_name 
                    WHERE user_id = %d", $user_id
                )
            );    
            wp_cache_set( $cache_key, $results, 'jialiub_bookmarks', 10 * MINUTE_IN_SECONDS );
        }
            
        return $results;
    }

    /**
     * Check if a bookmark exists for a user and post
     * @param int $user_id
     * @param int $post_id
     * @return bool
    */
    public function bookmarkExists($user_id, $post_id) {
        global $wpdb;
        $exists = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) 
                FROM $this->table_name 
                WHERE user_id = %d AND post_id = %d",
                $user_id, $post_id
            )
        );
        return $exists > 0; 
    }

    /**
     * Add a bookmark for a user and post
     * @param int $user_id
     * @param int $post_id
     * @return int|false
    */
    public function addBookmark($user_id, $post_id, $category_id = 0) { 
        global $wpdb; 
        $insert = $wpdb->insert($this->table_name, array(
            'user_id' => absint($user_id),
            'post_id' => absint($post_id),
            'category_id' => absint($category_id),
            'created_at'  => current_time('mysql'), // WP local time
            'created_at_gmt' => current_time('mysql', 1), // UTC
        ));
        return $insert;
    }   

    /** Remove a bookmark for a user and post
     * @param int $user_id
     * @param int $post_id
     * @return int|false
    */
    public function removeBookmark($user_id, $post_id) {
        global $wpdb;
        $delete = $wpdb->delete($this->table_name, array(
            'user_id' => absint($user_id),
            'post_id' => absint($post_id)
        ));
        return $delete; 
    }

    /* Toggle Bookmarks 
        * @param int $user_id
        * @param int $post_id
        * @return int|false 
    */
    public function toggleBookmark($user_id, $post_id, $category_id = 0) {
        
        if ($this->bookmarkExists($user_id, $post_id)) {
            $result =  $this->removeBookmark($user_id, $post_id);

            if( $result ) {
                $bookmarks_count = (int) $this->getPostBookmarksCount($post_id);
                $bookmarks_count = max(0, $bookmarks_count - 1);
                update_post_meta($post_id, 'jialiub_bookmarks_count', $bookmarks_count);
    
                $user_bookmarks_count = (int) $this->getUserBookmarksCount($user_id);
                $user_bookmarks_count = max(0, $user_bookmarks_count - 1);
                update_user_meta($user_id, 'jialiub_bookmarks_count', $user_bookmarks_count);
            }
        } else {
            $result =  $this->addBookmark($user_id, $post_id, $category_id);

            if( $result ) {
                $bookmarks_count = (int) $this->getPostBookmarksCount($post_id) + 1;
                update_post_meta($post_id, 'jialiub_bookmarks_count', $bookmarks_count);

                $user_bookmarks_count = (int) $this->getUserBookmarksCount($user_id) + 1;
                update_user_meta($user_id, 'jialiub_bookmarks_count', $user_bookmarks_count);
            }
            
        }

        // Delete cache
        wp_cache_delete('top_bookmarks', 'jialiub_bookmarks');
        wp_cache_delete('user_bookmarks', 'jialiub_bookmarks');

        return $result;
    }

    /**
     * Get user's default bookmark category
     * @param int $user_id
     * @return int
    */
    public function userDefaultBookmarkCategoryID($user_id) {
        global $wpdb;

        $default_category_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id 
                FROM {$this->category_table_name} 
                WHERE user_id = %d AND is_default = 1",
                $user_id
            )
        );
        if( $default_category_id ) {
            return (int) $default_category_id;
        } 

        $inserted = $wpdb->insert(
            $this->category_table_name, 
            array(
                'user_id' => absint($user_id),
                'name' => __('Default', 'jiali-user-bookmarks'),
                'is_default' => 1,
                'is_private' => 1,
                'created_at'  => current_time('mysql'), // WP local time
                'created_at_gmt' => current_time('mysql', 1), // UTC
            ),
            array(
                '%d', // user_id
                '%s', // name
                '%d', // is_default
                '%d', // is_private
                '%s', // created_at
                '%s', // created_at_gmt
            )
        );

        return $inserted ? (int) $wpdb->insert_id : 0;
        
    }
    
}

?>