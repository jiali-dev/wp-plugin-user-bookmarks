<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;


/**
 * Add settings link near Activate/Deactivate
 */
function jialiub_add_plugin_settings_link($links) {
    if (!current_user_can('manage_options')) return;
    $url = esc_url(admin_url('admin.php?page=jialiub-user-bookmarks'));
    $text = esc_html__('Settings', 'jiali-user-bookmarks');
    $settings_link = '<a href="' . $url . '">' . $text . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'jialiub_add_plugin_settings_link');

/**
 * Add admin menu and submenus
 */
function jialiub_register_bookmark_menu() {
    
    // Main Menu - visible for all logged-in users
    add_menu_page(
        /* translators: %s: Plural label for bookmarks */
        sprintf( esc_html__('User %s', 'jiali-user-bookmarks'), JIALIUB_PLURAL_LABEL ),  
        /* translators: %s: Plural label for bookmarks */
        sprintf( esc_html__('User %s', 'jiali-user-bookmarks'), JIALIUB_PLURAL_LABEL ),
        'read', // "read" ensures only logged-in users can see it,
        'jialiub-user-bookmarks',
        'jialiub_bookmarked_posts_report_page',
        'dashicons-plus',
        65
    );

    // Settings submenu (same as main page)
    add_submenu_page(
        'jialiub-user-bookmarks',
        esc_html__('Settings', 'jiali-user-bookmarks'),
        esc_html__('Settings', 'jiali-user-bookmarks'),
        'manage_options',
        'jialiub_settings',
        'jialiub_settings_page'
    );

    // Bookmarks Posts Report
    add_submenu_page(
        'jialiub-user-bookmarks',
        esc_html__('Shortcodes help', 'jiali-user-bookmarks'),
        esc_html__('Shortcodes help', 'jiali-user-bookmarks'),
        'manage_options',
        'jialiub-bookmarks-shortcode-help',
        'jialiub_bookmarks_shortcode_help_page'
    );
    

}
add_action('admin_menu', 'jialiub_register_bookmark_menu');

/**
 * Settings Page Output
 */
function jialiub_settings_page() {
    ?>
    <div class="wrap">
        <h1>
            <?php 
                /* translators: %s: Plural label for bookmarks */
                echo sprintf( esc_html__('User %s Settings', 'jiali-user-bookmarks'), esc_html(JIALIUB_PLURAL_LABEL) );
            ?>
        </h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('jialiub_settings_group');
            do_settings_sections('jialiub-user-bookmarks');
            do_settings_sections('jialiub-user-bookmarks-style');
            submit_button(esc_html__('Save Changes', 'jiali-user-bookmarks'));
            ?>
        </form>
    </div>
    <?php
}

/**
 * Register settings
 */
function jialiub_register_settings() {
    
    register_setting('jialiub_settings_group', 'jialiub_bookmark_enabled_post_types', [
        'type' => 'array',
        'capability' => 'manage_options',
    ]);

    if (current_user_can('manage_options')) {
        
        register_setting(
            'jialiub_settings_group',
            'jialiub_button_position',
            [
                'type' => 'string',
                'default' => 'after',
                'capability' => 'manage_options',
            ]
        );

        add_settings_section(
            'jialiub_main_settings_section',
            esc_html__('Enabled Post Types', 'jiali-user-bookmarks'),
            '__return_false',
            'jialiub-user-bookmarks'
        );

        add_settings_field(
            'jialiub_bookmark_enabled_post_types',
            /* translators: %s: Plural label for bookmarks */
            sprintf( esc_html__('Enabled Post Types for %s', 'jiali-user-bookmarks'), JIALIUB_PLURAL_LABEL ),
            'jialiub_bookmark_post_types_field',
            'jialiub-user-bookmarks',
            'jialiub_main_settings_section'
        );

        add_settings_field(
            'jialiub_button_position',
            esc_html__('Button Position', 'jiali-user-bookmarks'),
            'jialiub_button_position_field',
            'jialiub-user-bookmarks',
            'jialiub_main_settings_section'
        );

        register_setting('jialiub_settings_group', 'jialiub_singular_label', [
            'type' => 'string',
            /* translators: %s: Singular label for bookmarks */
            'default' => sprintf( esc_html__('%s', 'jiali-user-bookmarks'), JIALIUB_SINGULAR_LABEL ),
            'sanitize_callback' => 'sanitize_text_field',
            'capability' => 'manage_options',
        ]);
        
        add_settings_field(
            'jialiub_singular_label',
            esc_html__('Singular Label', 'jiali-user-bookmarks'),
            'jialiub_singular_label_field',
            'jialiub-user-bookmarks',
            'jialiub_main_settings_section'
        );

        register_setting('jialiub_settings_group', 'jialiub_plural_label', [
            'type' => 'string',
            /* translators: %s: Plural label for bookmarks */
            'default' => sprintf( esc_html__('%s', 'jiali-user-bookmarks'), JIALIUB_PLURAL_LABEL ),
            'sanitize_callback' => 'sanitize_text_field',
            'capability' => 'manage_options',
        ]);
        
        add_settings_field(
            'jialiub_plural_label',
            esc_html__('Plural Label', 'jiali-user-bookmarks'),
            'jialiub_plural_label_field',
            'jialiub-user-bookmarks',
            'jialiub_main_settings_section'
        );

        register_setting('jialiub_settings_group', 'jialiub_action_label', [
            'type' => 'string',
            /* translators: %s: Action label for bookmarks */
            'default' => sprintf( esc_html__('%s', 'jiali-user-bookmarks'), JIALIUB_ACTION_LABEL ),
            'sanitize_callback' => 'sanitize_text_field',
            'capability' => 'manage_options',
        ]);
        
        add_settings_field(
            'jialiub_action_label',
            esc_html__('Action Label', 'jiali-user-bookmarks'),
            'jialiub_action_label_field',
            'jialiub-user-bookmarks',
            'jialiub_main_settings_section'
        );
        
        register_setting('jialiub_settings_group', 'jialiub_show_label', [
            'type' => 'boolean',
            'default' => false,
            'sanitize_callback' => function( $value ) {
                return (bool) $value;
            },  
            'capability' => 'manage_options',
        ]);
        
        add_settings_field(
            'jialiub_show_label',
            esc_html__('Show Label', 'jiali-user-bookmarks'),
            'jialiub_show_label_field',
            'jialiub-user-bookmarks',
            'jialiub_main_settings_section'
        );

    }

}
add_action('admin_init', 'jialiub_register_settings');

/**
 * Checkbox field for Bookmark post types
 */
function jialiub_bookmark_post_types_field() {
    if (!current_user_can('manage_options')) return;

    $selected = get_option('jialiub_bookmark_enabled_post_types', []);
    if (!is_array($selected)) {
        $selected = [];
    }
    $post_types = get_post_types(['public' => true], 'objects');

    foreach ($post_types as $type) {
        ?>
        <label>
            <input type="checkbox" name="jialiub_bookmark_enabled_post_types[]" value="<?php echo esc_attr($type->name); ?>"
                <?php checked(in_array($type->name, $selected)); ?> />
            <?php echo esc_html($type->label); ?>
        </label><br>
        <?php
    }
}

/**
 * Radio field for button position
 */
function jialiub_button_position_field() {
    if (!current_user_can('manage_options')) return;
    $value = get_option('jialiub_button_position', 'after');
    ?>
    <label>
        <input type="radio" name="jialiub_button_position" value="before" <?php checked($value, 'before'); ?> />
        <?php esc_html_e('Before Content', 'jiali-user-bookmarks'); ?>
    </label><br>
    <label>
        <input type="radio" name="jialiub_button_position" value="after" <?php checked($value, 'after'); ?> />
        <?php esc_html_e('After Content', 'jiali-user-bookmarks'); ?>
    </label>
    <?php
}

/* 
 * Field for singular label
*/
function jialiub_singular_label_field() {
    if (!current_user_can('manage_options')) return;
    $value = JIALIUB_SINGULAR_LABEL;
    ?>
    <input type="text" name="jialiub_singular_label" value="<?php echo esc_attr($value); ?>" class="regular-text" />
    <p class="description"><?php esc_html_e('E.g. Bookmark, Favorite, Item', 'jiali-user-bookmarks'); ?></p>
    <?php
}

/* 
 * Field for plural label
*/
function jialiub_plural_label_field() {
    if (!current_user_can('manage_options')) return;
    $value = JIALIUB_PLURAL_LABEL;
    ?>
    <input type="text" name="jialiub_plural_label" value="<?php echo esc_attr($value); ?>" class="regular-text" />
    <p class="description"><?php esc_html_e('E.g. Bookmark, Favorite, Item', 'jiali-user-bookmarks'); ?></p>
    <?php
}

/* 
 * Field for action label
*/
function jialiub_action_label_field() {
    if (!current_user_can('manage_options')) return;
    $value = JIALIUB_ACTION_LABEL;
    ?>
    <input type="text" name="jialiub_action_label" value="<?php echo esc_attr($value); ?>" class="regular-text" />
    <p class="description"><?php esc_html_e('E.g. Bookmark, Favorite, Item', 'jiali-user-bookmarks'); ?></p>
    <?php
}

// Callback for field
function jialiub_show_label_field() {
    $value = get_option('jialiub_show_label', false);
    ?>
    <label>
        <input type="checkbox" name="jialiub_show_label" value="1" <?php checked($value, true); ?>>
        <?php esc_html_e('Show label next to icon/button', 'jiali-user-bookmarks'); ?>
    </label>
    <?php
}

/**
 * Bookmark Posts Report Page
 */
function jialiub_bookmarked_posts_report_page() {
    echo "<div class='jiali-container jialiub-container--bg-white p-4'>";
        echo wp_kses_post(
            sprintf(
                '<h2 class="jialiub-heading">%s</h2>',
                sprintf(
                    /* translators: %s is the action label */
                    esc_html__('Your %s Posts', 'jiali-user-bookmarks'),
                    esc_html(JIALIUB_ACTION_LABEL)
                )
            )
        );
        echo wp_kses_post( jialiub_render_user_bookmarks_table() );
    echo "</div>";

    if( current_user_can( 'manage_options' )) {
        echo "<div class='jiali-container jialiub-container--bg-white p-4'>";
            echo wp_kses_post(
            sprintf(
                    '<h2 class="jialiub-heading">%s</h2>',
                    sprintf(
                        /* translators: %s is the action label */
                        esc_html__('Top %s Posts', 'jiali-user-bookmarks'),
                        esc_html(JIALIUB_ACTION_LABEL)
                    )
                )
            );
            echo wp_kses_post( jialiub_render_top_bookmarks_table() );
        echo "</div>";
    }


}

?>
