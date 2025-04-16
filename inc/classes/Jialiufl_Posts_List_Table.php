<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class Jialiufl_Posts_List_Table extends WP_List_Table {
    
    protected $items_data;
    protected $columns;
    protected $sortable;
    // protected $bulk_actions;
    protected $per_page;
    protected $action_type;

    public function __construct($args = []) {
        parent::__construct([
            'singular' => 'post',
            'plural'   => 'posts',
            'ajax'     => false
        ]);

        $this->per_page = $args['per_page'] ?? 10;

        $this->items_data   = (is_array($args) && isset($args['posts'])) ? $args['posts'] : [];
        $this->columns      = $args['columns'] ?? [
            'title'   => __('Title'),
            'author'  => __('Author'),
        ];

        $this->sortable     = $args['sortable_columns'] ?? [
            'title'  => ['title', true],
        ];

        // $this->bulk_actions = $args['bulk_actions'] ?? [
        //     'delete' => __('Delete')
        // ];

        $this->action_type  = $args['action_type'] ?? 'like';
    }

    public function get_columns() {
        return $this->columns;
    }

    public function get_sortable_columns() {
        return $this->sortable;
    }

    // public function get_bulk_actions() {
    //     return $this->bulk_actions;
    // }

    // public function column_cb($item) {
    //     return sprintf(
    //         '<input type="checkbox" name="post_ids[]" value="%s" />',
    //         esc_attr($item->ID)
    //     );
    // }

    public function column_title($item) {
        if (is_object($item) && property_exists($item, 'ID')) {
            $post_link = get_post_permalink($item->ID);
            $title     = get_the_title($item->ID);
            return sprintf('<a target="_blank" href="%s">%s</a>', esc_url($post_link), esc_html($title));
        }
        return '';
    }

    public function column_author($item) {
        if (is_object($item) && property_exists($item, 'post_author')) {
            return esc_html(get_the_author_meta('display_name', $item->post_author));
        }
        return esc_html__('Unknown Author', 'jiali-user-favorites-and-likes');
    }

    public function column_count($item) {
        return esc_html( $this->action_type === 'like' ? jialiufl_get_post_likes_count($item->ID) : jialiufl_get_post_favorites_count($item->ID) );
    }

    public function prepared_items() {
        $this->_column_headers = [$this->get_columns(), [], $this->get_sortable_columns()];

        $data = $this->items_data;

        // Sorting
        $orderby = $_GET['orderby'] ?? 'title';
        $order   = $_GET['order'] ?? 'asc';

        if (!empty($data) && ((is_object($data[0]) && property_exists($data[0], $orderby)) || (is_array($data[0]) && array_key_exists($orderby, $data[0])))) {
            usort($data, function ($a, $b) use ($orderby, $order) {
                $valA = is_object($a) ? $a->$orderby ?? '' : $a[$orderby] ?? '';
                $valB = is_object($b) ? $b->$orderby ?? '' : $b[$orderby] ?? '';

                return ($order === 'asc') ? strnatcasecmp($valA, $valB) : strnatcasecmp($valB, $valA);
            });
        }

        $per_page = $this->per_page;
        $current_page = max(1, intval($this->get_pagenum()));
        $total_items = count($data);

        $this->items = array_slice($data, ($current_page - 1) * $per_page, $per_page);
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);
    }
}
