<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubBookmarkFunctions {
    
    protected static $instance = null;
    protected $table_name;

    private function __construct() {
        global $wpdb;
        $this->table_name = esc_sql($wpdb->prefix . 'jialiub_bookmarks');
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

        $results = $wpdb->get_col(
            "SELECT post_id 
            FROM $this->table_name 
            GROUP BY post_id 
            ORDER BY COUNT(*) DESC 
            LIMIT 10"
        );

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
        $results = $wpdb->get_col(
            $wpdb->prepare("SELECT post_id FROM $this->table_name WHERE user_id = %d", $user_id)
        );      
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
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $this->table_name WHERE user_id = %d AND post_id = %d", $user_id, $post_id));
        return $exists > 0; 
    }

    /**
     * Add a bookmark for a user and post
     * @param int $user_id
     * @param int $post_id
     * @return int|false
    */
    public function addBookmark($user_id, $post_id) { 
        global $wpdb; 
        $insert = $wpdb->insert($this->table_name, array(
            'user_id' => absint($user_id),
            'post_id' => absint($post_id),
            'date_added'  => current_time('mysql'), // WP local time
            'date_added_gmt' => current_time('mysql', 1), // UTC
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
    public function toggleBookmark($user_id, $post_id) {
        
        if ($this->bookmarkExists($user_id, $post_id)) {
            $result =  $this->removeBookmark($user_id, $post_id);

            if( $result ) {
                $bookmarks_count = $this->getPostBookmarksCount($post_id);
                $bookmarks_count--;
                update_post_meta($post_id, 'jialiub_bookmarks_count', $bookmarks_count);
    
                $user_bookmarks_count = $this->getUserBookmarksCount($user_id);
                $user_bookmarks_count--;
                update_user_meta($user_id, 'jialiub_bookmarks_count', $user_bookmarks_count);
            }
        } else {
            $result =  $this->addBookmark($user_id, $post_id);

            if( $result ) {
                $bookmarks_count = $this->getPostBookmarksCount($post_id);
                $bookmarks_count++;
                update_post_meta($post_id, 'jialiub_bookmarks_count', $bookmarks_count);

                $user_bookmarks_count = $this->getUserBookmarksCount($user_id);
                $user_bookmarks_count++;
                update_user_meta($user_id, 'jialiub_bookmarks_count', $user_bookmarks_count);
            }
            
        }
        return $result;
    }
    
}

?>