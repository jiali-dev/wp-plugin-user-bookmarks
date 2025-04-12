<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Add link for setting near Active/Deactive 
function jialiufl_add_plugin_settings_link($links) {
    $settings_link = '<a href="' . admin_url('themes.php?page=jialiufl-user-favorites-and-likes') . '">' . __('Settings', 'jiali-user-favorites-and-likes') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(dirname(__DIR__) . '/core.php'), 'jialiufl_add_plugin_settings_link');

// Register settings
function jialiufl_add_admin_menu() {
    add_theme_page(
        __('User Favorites and Likes Settings', 'jiali-user-favorites-and-likes'), // Page title
        __('User Favorites and Likes', 'jiali-user-favorites-and-likes'), // Menu title
        'manage_options', // Capability
        'jialiufl-user-favorites-and-likes', // Menu slug
        'jialiufl_settings_page' // Callback function
    );
}
add_action('admin_menu', 'jialiufl_add_admin_menu');

// Settings section
function jialiufl_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('User Favorites and Likes Settings', 'jiali-user-favorites-and-likes'); ?></h1>
        <form method="post" action="options.php">
            <?php
            // settings_fields('jialiufl_settings_group');
            // do_settings_sections('jialiufl-user-favorites-and-likes');
            // submit_button(__('Save Changes', 'jiali-user-favorites-and-likes'));
            ?>
        </form>
    </div>
    <?php
}

?>