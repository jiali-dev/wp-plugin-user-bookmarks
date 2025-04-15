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
    // Main menu
    add_menu_page(
        esc_html__('User Favorites and Likes', 'jiali-user-favorites-and-likes'),
        esc_html__('Favorites & Likes', 'jiali-user-favorites-and-likes'),
        'manage_options',
        'jialiufl-user-favorites-and-likes',
        'jialiufl_settings_page',
        'dashicons-heart',
        65
    );

    // Submenu: Settings (same page as main)
    add_submenu_page(
        'jialiufl-user-favorites-and-likes',
        esc_html__('Settings', 'jiali-user-favorites-and-likes'),
        esc_html__('Settings', 'jiali-user-favorites-and-likes'),
        'manage_options',
        'jialiufl-user-favorites-and-likes',
        'jialiufl_settings_page'
    );

    // Submenu: Liked Posts
    add_submenu_page(
        'jialiufl-user-favorites-and-likes',
        esc_html__('Liked Posts', 'jiali-user-favorites-and-likes'),
        esc_html__('Liked Posts', 'jiali-user-favorites-and-likes'),
        'manage_options',
        'jialiufl-liked-posts',
        'jialiufl_liked_posts_page'
    );

    // Submenu: Favorite Posts
    add_submenu_page(
        'jialiufl-user-favorites-and-likes',
        esc_html__('Favorite Posts', 'jiali-user-favorites-and-likes'),
        esc_html__('Favorite Posts', 'jiali-user-favorites-and-likes'),
        'manage_options',
        'jialiufl-favorite-posts',
        'jialiufl_favorite_posts_page'
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
    register_setting('jialiufl_settings_group', 'jialiufl_enabled_post_types_for_like');
    register_setting('jialiufl_settings_group', 'jialiufl_enabled_post_types_for_favorite');
    register_setting('jialiufl_settings_group', 'jialiufl_button_position');

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
add_action('admin_init', 'jialiufl_register_settings');

/**
 * Checkbox field for Like post types
 */
function jialiufl_like_post_types_field() {
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
    $liked_post_ids = jialiufl_get_user_likes($user_id); // must return array of post IDs

    $table = new Jialiufl_Posts_List_Table([
        'post_ids' => $liked_post_ids,
        'title'    => 'Liked Posts',
    ]);

    $table->render_table();
}

/**
 * Favorite Posts Page
 */
function jialiufl_favorite_posts_page() {
    $user_id = get_current_user_id();
    $fav_post_ids = jialiufl_get_user_favorites($user_id);

    $table = new Jialiufl_Posts_List_Table([
        'post_ids' => $fav_post_ids,
        'title'    => 'Favorited Posts',
    ]);

    $table->render_table();
}

?>
