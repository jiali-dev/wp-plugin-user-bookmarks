<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Add settings link near Activate/Deactivate
 */
function jialiufl_add_plugin_settings_link($links) {
    $url = esc_url(admin_url('admin.php?page=jialiufl-user-favorites-and-likes'));
    $text = esc_html__('Settings', 'jiali-user-favorites-and-likes');
    $settings_link = '<a href="' . $url . '">' . $text . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'jialiufl_add_plugin_settings_link');

/**
 * Add admin menu and submenus
 */
function jialiufl_add_admin_menu() {
    // Main menu - restricted to admins
    add_menu_page(
        esc_html__('User Favorites and Likes', 'jiali-user-favorites-and-likes'),
        esc_html__('Favorites & Likes', 'jiali-user-favorites-and-likes'),
        'read',
        'jialiufl-user-favorites-and-likes',
        'jialiufl_settings_page',
        'dashicons-heart',
        65
    );

    // Submenu: Settings (same page)
    add_submenu_page(
        'jialiufl-user-favorites-and-likes',
        esc_html__('Settings', 'jiali-user-favorites-and-likes'),
        esc_html__('Settings', 'jiali-user-favorites-and-likes'),
        'read',
        'jialiufl-user-favorites-and-likes',
        'jialiufl_settings_page'
    );

    // Submenu: Liked Posts
    add_submenu_page(
        'jialiufl-user-favorites-and-likes',
        esc_html__('Liked Posts', 'jiali-user-favorites-and-likes'),
        esc_html__('Liked Posts', 'jiali-user-favorites-and-likes'),
        'read',
        'jialiufl-liked-posts',
        'jialiufl_liked_posts_page'
    );

    // Submenu: Favorite Posts
    add_submenu_page(
        'jialiufl-user-favorites-and-likes',
        esc_html__('Favorite Posts', 'jiali-user-favorites-and-likes'),
        esc_html__('Favorite Posts', 'jiali-user-favorites-and-likes'),
        'read',
        'jialiufl-favorite-posts',
        'jialiufl_favorite_posts_page'
    );
    
    // Submenu: Favorite Posts Report
    // Only visible to admins
    add_submenu_page(
        'jialiufl-user-favorites-and-likes',
        esc_html__('Favorite Posts Report', 'jiali-user-favorites-and-likes'),
        esc_html__('Favorite Posts Report', 'jiali-user-favorites-and-likes'),
        'manage_options',
        'jialiufl-favorite-posts-report',
        'jialiufl_favorite_posts_report_page'
    );
}
add_action('admin_menu', 'jialiufl_add_admin_menu');

/**
 * Settings Page Output
 */
function jialiufl_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('User Favorites and Likes Settings', 'jiali-user-favorites-and-likes'); ?></h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('jialiufl_settings_group');
            do_settings_sections('jialiufl-user-favorites-and-likes');
            do_settings_sections('jialiufl-user-favorites-and-likes-style');
            submit_button(esc_html__('Save Changes', 'jiali-user-favorites-and-likes'));
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register settings
 */
function jialiufl_register_settings() {
    register_setting('jialiufl_settings_group', 'jialiufl_enabled_post_types_for_like', [
        'type' => 'array',
        'capability' => 'manage_options',
    ]);
    register_setting('jialiufl_settings_group', 'jialiufl_enabled_post_types_for_favorite', [
        'type' => 'array',
        'capability' => 'manage_options',
    ]);
    register_setting(
        'jialiufl_settings_group',
        'jialiufl_button_position',
        [
            'type' => 'string',
            'default' => 'after',
            'capability' => 'manage_options',
        ]
    );

    if (current_user_can('manage_options')) {
        add_settings_section(
            'jialiufl_main_settings_section',
            esc_html__('Enabled Post Types', 'jiali-user-favorites-and-likes'),
            '__return_false',
            'jialiufl-user-favorites-and-likes'
        );

        add_settings_field(
            'jialiufl_like_enabled_post_types',
            esc_html__('Enabled Post Types for Like', 'jiali-user-favorites-and-likes'),
            'jialiufl_like_post_types_field',
            'jialiufl-user-favorites-and-likes',
            'jialiufl_main_settings_section'
        );

        add_settings_field(
            'jialiufl_fav_enabled_post_types',
            esc_html__('Enabled Post Types for Favorite', 'jiali-user-favorites-and-likes'),
            'jialiufl_favorite_post_types_field',
            'jialiufl-user-favorites-and-likes',
            'jialiufl_main_settings_section'
        );

        add_settings_field(
            'jialiufl_button_position',
            esc_html__('Button Position', 'jiali-user-favorites-and-likes'),
            'jialiufl_button_position_field',
            'jialiufl-user-favorites-and-likes',
            'jialiufl_main_settings_section'
        );
    }

    // Style section (optional access control)
    add_settings_section(
        'jialiufl_style_settings_section',
        esc_html__('Style Settings', 'jiali-user-favorites-and-likes'),
        '__return_false',
        'jialiufl-user-favorites-and-likes-style'
    );

}
add_action('admin_init', 'jialiufl_register_settings');

/**
 * Checkbox field for Like post types
 */
function jialiufl_like_post_types_field() {
    if (!current_user_can('manage_options')) return;

    $selected = get_option('jialiufl_enabled_post_types_for_like', []);
    $post_types = get_post_types(['public' => true], 'objects');

    foreach ($post_types as $type) {
        ?>
        <label>
            <input type="checkbox" name="jialiufl_enabled_post_types_for_like[]" value="<?php echo esc_attr($type->name); ?>"
                <?php checked(in_array($type->name, $selected)); ?> />
            <?php echo esc_html($type->label); ?>
        </label><br>
        <?php
    }
}

/**
 * Checkbox field for Favorite post types
 */
function jialiufl_favorite_post_types_field() {
    if (!current_user_can('manage_options')) return;

    $selected = get_option('jialiufl_enabled_post_types_for_favorite', []);
    $post_types = get_post_types(['public' => true], 'objects');

    foreach ($post_types as $type) {
        ?>
        <label>
            <input type="checkbox" name="jialiufl_enabled_post_types_for_favorite[]" value="<?php echo esc_attr($type->name); ?>"
                <?php checked(in_array($type->name, $selected)); ?> />
            <?php echo esc_html($type->label); ?>
        </label><br>
        <?php
    }
}

/**
 * Radio field for button position
 */
function jialiufl_button_position_field() {
    if (!current_user_can('manage_options')) return;
    $value = get_option('jialiufl_button_position', 'after');
    ?>
    <label>
        <input type="radio" name="jialiufl_button_position" value="before" <?php checked($value, 'before'); ?> />
        <?php esc_html_e('Before Content', 'jiali-user-favorites-and-likes'); ?>
    </label><br>
    <label>
        <input type="radio" name="jialiufl_button_position" value="after" <?php checked($value, 'after'); ?> />
        <?php esc_html_e('After Content', 'jiali-user-favorites-and-likes'); ?>
    </label>
    <?php
}

/**
 * Liked Posts Page
 */
function jialiufl_liked_posts_page() {
    $user_id = get_current_user_id();
    $post_ids = jialiufl_get_user_likes($user_id);
    $posts = new WP_Query([
        'post__in' => ( empty($post_ids) ? [0] : $post_ids ),
        'post_type' => 'any',
        'posts_per_page' => -1,
        'orderby' => 'post__in',
        'update_post_meta_cache' => false, 
        'update_post_term_cache' => false,
        'ignore_sticky_posts' => true 
    ]);

    $table = new Jialiufl_Posts_List_Table([
        'posts' => $posts->posts,
        'columns' => [
            'title'  => __('Title'),
            'author' => __('Author'),
        ],
        'sortable_columns' => [
            'title' => ['post_title', true],
        ]
        
    ]);

    echo '<div class="wrap"><h1>Liked Posts</h1>';
    echo '<form method="post">';
        $table->prepared_items();
        $table->display();
    echo '</form></div>';
}


/**
 * Favorite Posts Page
 */
function jialiufl_favorite_posts_page() {
    $user_id = get_current_user_id();
    $post_ids = jialiufl_get_user_favorites($user_id);

    $posts = new WP_Query([
        'post__in' => ( empty($post_ids) ? [0] : $post_ids ),
        'post_type' => 'any',
        'posts_per_page' => -1,
        'orderby' => 'post__in',
        'update_post_meta_cache' => false, 
        'update_post_term_cache' => false,
        'ignore_sticky_posts' => true 
    ]);

    $table = new Jialiufl_Posts_List_Table([
        'posts' => $posts->posts,
        'columns' => [
            'title'  => __('Title'),
            'author' => __('Author'),
        ],
        'sortable_columns' => [
            'title' => ['post_title', true],
        ]
        
    ]);

    echo '<div class="wrap"><h1>Liked Posts</h1>';
    echo '<form method="post">';
        $table->prepared_items();
        $table->display();
    echo '</form></div>';
}

/**
 * Favorite Posts Report Page
 */
function jialiufl_favorite_posts_report_page() {
    $user_id = get_current_user_id();
    $post_ids = jialiufl_get_user_likes($user_id);
    $posts = new WP_Query([
        'post__in' => ( empty($post_ids) ? [0] : $post_ids ),
        'post_type' => 'any',
        'posts_per_page' => -1,
        'orderby' => 'post__in',
        'update_post_meta_cache' => false, 
        'update_post_term_cache' => false,
        'ignore_sticky_posts' => true 
    ]);

    $table = new Jialiufl_Posts_List_Table([
        'posts' => $posts->posts,
        'columns' => [
            'title'  => __('Title'),
            'author' => __('Author'),
            'count' => __('Count'),
        ],
        'sortable_columns' => [
            'title' => ['post_title', true],
            'count' => ['post_count', true],
        ],
        'action_type' => 'favorite'
    ]);

    echo '<div class="wrap"><h1>Liked Posts</h1>';
    echo '<form method="post">';
        $table->prepared_items();
        $table->display();
    echo '</form></div>';
}
?>
