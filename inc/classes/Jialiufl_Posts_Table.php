<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Jialiufl_Posts_List_Table extends WP_List_Table {
    
    private $post_ids = [];
    private $title = 'Posts';
    private $post_type = 'any';

    public function __construct($args = []) {
        parent::__construct([
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => false,
        ]);

        $this->post_ids  = $args['post_ids'] ?? [];
        $this->title     = $args['title'] ?? 'Posts';
        $this->post_type = $args['post_type'] ?? 'any';
    }

    public function get_columns() {
        return [
            'title' => 'Title',
            'date'  => 'Date',
        ];
    }

    public function prepare_items() {
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($this->post_ids);
        // Slice the post IDs array manually for pagination
        $paged_post_ids = array_slice($this->post_ids, ($current_page - 1) * $per_page, $per_page);
    
        $this->_column_headers = array($this->get_columns());

        if (empty($paged_post_ids)) {
            $this->items = [];
            $this->set_pagination_args([
                'total_items' => 0,
                'per_page'    => $per_page,
            ]);
            return;
        }
    
        $query = new WP_Query([
            'post__in' => $paged_post_ids,
            'orderby' => 'post__in',
            'post_type' => $this->post_type,
            'posts_per_page' => -1, // We already paginated with array_slice
        ]);

        $items = [];

        foreach ($query->posts as $post) {
            $items[] = [
                'title' => '<a target="_blank" href="' . get_post_permalink($post) . '">' . esc_html(get_the_title($post)) . '</a>',
                'date'  => get_the_date('', $post),
            ];
        }
        $this->items = $items;
    
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);

    }    

    public function column_default($item, $column_name) {
        return $item[$column_name] ?? '';
    }

    public function render_table() {
        echo '<div class="wrap">';
            echo '<h1>' . esc_html($this->title) . '</h1>';
            $this->prepare_items();
            $this->display();
        echo '</div>';
    }

}

