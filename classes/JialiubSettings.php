<?php
// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubSettings {

    private static $instance = null;

    public function __construct() {
        add_filter('plugin_action_links_' . plugin_basename(dirname(__DIR__) . '/jiali-user-bookmarks.php'), [$this, 'addPluginSettingsLink']);
        add_action('admin_menu', [$this, 'registerBookmarkMenu']);
        add_action('admin_init', [$this, 'registerSettings']);
        add_action('wp_enqueue_scripts', [$this, 'enqueueStyles']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueStyles']);
    }

    private function __clone() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add settings link near Activate/Deactivate
     */
    public function addPluginSettingsLink($links) {
        if (!current_user_can('manage_options')) return $links;
        $url = esc_url(admin_url('admin.php?page=jialiub_settings'));
        $text = __('Settings', 'jiali-user-bookmarks');
        $settingsLink = '<a href="' . $url . '">' . $text . '</a>';
        array_unshift($links, $settingsLink);
        return $links;
    }

    /**
     * Add admin menu and submenus
     */
    public function registerBookmarkMenu() {
        // Main Menu - visible for all logged-in users
        add_menu_page(
            /* translators: %s: Plural label for bookmarks */
            sprintf(esc_html__('User %s', 'jiali-user-bookmarks'), JIALIUB_PLURAL_LABEL),
            /* translators: %s: Plural label for bookmarks */
            sprintf(esc_html__('User %s', 'jiali-user-bookmarks'), JIALIUB_PLURAL_LABEL),
            'read',
            'jialiub-user-bookmarks',
            [$this, 'bookmarkedPostsReportPage'],
            'dashicons-plus',
            65
        );

        // Settings submenu
        add_submenu_page(
            'jialiub-user-bookmarks',
            esc_html__('Settings', 'jiali-user-bookmarks'),
            esc_html__('Settings', 'jiali-user-bookmarks'),
            'manage_options',
            'jialiub_settings',
            [$this, 'settingsPage']
        );

        // Shortcodes help
        add_submenu_page(
            'jialiub-user-bookmarks',
            esc_html__('Shortcodes help', 'jiali-user-bookmarks'),
            esc_html__('Shortcodes help', 'jiali-user-bookmarks'),
            'manage_options',
            'jialiub-bookmarks-shortcode-help',
            [$this, 'shortcodesHelpPage']
        );
    }

    /**
     * Settings Page
     */
    public function settingsPage() {
        wp_enqueue_style('jialiub-styles'); ?>
        <div class="jialiub-container jialiub-container--bg-white p-4">
            <h1 class="jialiub-heading">
                <?php echo sprintf(
                    /* translators: %s: Plural label for bookmarks */
                    esc_html__('User %s Settings', 'jiali-user-bookmarks'),
                    esc_html(JIALIUB_PLURAL_LABEL)
                ); ?>
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
    public function registerSettings() {
        
        if (current_user_can('manage_options')) {

            add_settings_section(
                'jialiub_main_settings_section',
                esc_html__('Settings', 'jiali-user-bookmarks'),
                '__return_false',
                'jialiub-user-bookmarks'
            );

            register_setting(
                'jialiub_settings_group', 
                'jialiub_bookmark_enabled_post_types', 
                [
                    'type' => 'array',
                    'capability' => 'manage_options',
                    'sanitize_callback' => function( $input ) {
                        if ( ! is_array( $input ) ) {
                            return [];
                        }
                        $allowed_post_types = get_post_types( [ 'public' => true ], 'names' );
                        $sanitized = array_intersect( $input, $allowed_post_types );
                        return array_values( $sanitized );
                    },
                ]
            );

            add_settings_field(
                'jialiub_bookmark_enabled_post_types',
                /* translators: %s: Plural label for bookmarks */
                sprintf(esc_html__('Enabled Post Types for %s', 'jiali-user-bookmarks'), JIALIUB_PLURAL_LABEL),
                [$this, 'bookmarkPostTypesField'],
                'jialiub-user-bookmarks',
                'jialiub_main_settings_section'
            );

            register_setting(
                'jialiub_settings_group', 
                'jialiub_button_position', 
                [
                    'type' => 'string',
                    'default' => 'after',
                    'capability' => 'manage_options',
                    'sanitize_callback' => function( $value ) {
                        $allowed = [ 'before', 'after' ];
                        return in_array( $value, $allowed, true ) ? $value : 'after';
                    },
                ]
            );

            add_settings_field(
                'jialiub_button_position',
                esc_html__('Button Position', 'jiali-user-bookmarks'),
                [$this, 'buttonPositionField'],
                'jialiub-user-bookmarks',
                'jialiub_main_settings_section'
            );

            register_setting(
                'jialiub_settings_group', 
                'jialiub_singular_label', 
                [
                    'type' => 'string',
                    /* translators: %s: Singular label for bookmarks */
                    'default' => JIALIUB_SINGULAR_LABEL,
                    'sanitize_callback' => 'sanitize_text_field',
                    'capability' => 'manage_options',
                ]
            );
            
            add_settings_field(
                'jialiub_singular_label',
                esc_html__('Singular Label', 'jiali-user-bookmarks'),
                [$this, 'singularLabelField'],
                'jialiub-user-bookmarks',
                'jialiub_main_settings_section'
            );

            register_setting(
                'jialiub_settings_group', 
                'jialiub_plural_label', 
                [
                    'type' => 'string',
                    /* translators: %s: Plural label for bookmarks */
                    'default' => JIALIUB_PLURAL_LABEL,
                    'sanitize_callback' => 'sanitize_text_field',
                    'capability' => 'manage_options',
                ]
            );
            
            add_settings_field(
                'jialiub_plural_label',
                esc_html__('Plural Label', 'jiali-user-bookmarks'),
                [$this, 'pluralLabelField'],
                'jialiub-user-bookmarks',
                'jialiub_main_settings_section'
            );

            register_setting(
                'jialiub_settings_group', 
                'jialiub_action_label', 
                [
                    'type' => 'string',
                    /* translators: %s: Action label for bookmarks */
                    'default' => JIALIUB_ACTION_LABEL,
                    'sanitize_callback' => 'sanitize_text_field',
                    'capability' => 'manage_options',
                ]
            );
            
            add_settings_field(
                'jialiub_action_label',
                esc_html__('Action Label', 'jiali-user-bookmarks'),
                [$this, 'actionLabelField'],
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
                [$this, 'showLabelField'],
                'jialiub-user-bookmarks',
                'jialiub_main_settings_section'
            );

            register_setting(
                'jialiub_settings_group', 
                'jialiub_show_count', 
                [
                    'type' => 'boolean',
                    'default' => true,
                    'sanitize_callback' => function( $value ) {
                        return (bool) $value;
                    },  
                    'capability' => 'manage_options',
                ]
            );
            
            add_settings_field(
                'jialiub_show_count',
                esc_html__('Show Count', 'jiali-user-bookmarks'),
                [$this, 'showCountField'],
                'jialiub-user-bookmarks',
                'jialiub_main_settings_section'
            );

            add_settings_section(
                'jialiub_style_settings_section',
                esc_html__('Custom Styles', 'jiali-user-bookmarks'),
                '__return_false',
                'jialiub-user-bookmarks'
            );

            register_setting(
                'jialiub_settings_group', 
                'jialiub_button_color', 
                'sanitize_hex_color'
            );

            add_settings_field(
                'button_color',
                esc_html__('Button Color', 'jiali-user-bookmarks'), 
                [$this, 'buttonColorCallback'], 
                'jialiub-user-bookmarks', 
                'jialiub_style_settings_section'
            );

            register_setting(
                'jialiub_settings_group', 
                'jialiub_button_hover_color', 
                'sanitize_hex_color'
            );

            add_settings_field(
                'button_hover_color', 
                esc_html__('Button Hover Color', 'jiali-user-bookmarks'), 
                [$this, 'buttonHoverColorCallback'], 
                'jialiub-user-bookmarks', 
                'jialiub_style_settings_section'
            );

            register_setting(
                'jialiub_settings_group', 
                'jialiub_button_active_color', 
                'sanitize_hex_color'
            );

            add_settings_field(
                'button_active_color', 
                esc_html__('Button Active Color', 'jiali-user-bookmarks'), 
                [$this, 'buttonActiveColorCallback'], 
                'jialiub-user-bookmarks', 
                'jialiub_style_settings_section'
            );

            register_setting( 
                'jialiub_settings_group', 
                'jialiub_font_size', 
                [
                    'type' => 'integer',
                    'sanitize_callback' => 'absint',
                    'default' => 14
                ]
            );

            add_settings_field(
                'font_size', 
                esc_html__('Font Size (px)', 'jiali-user-bookmarks'), 
                [$this, 'fontSizeCallback'], 
                'jialiub-user-bookmarks', 
                'jialiub_style_settings_section'    
            );


        }
    }

    /**
     * Checkbox field for post types
     */
    public function bookmarkPostTypesField() {
        if (!current_user_can('manage_options')) return;

        $selected = get_option('jialiub_bookmark_enabled_post_types', []);
        if (!is_array($selected)) {
            $selected = [];
        }
        $postTypes = get_post_types(['public' => true], 'objects');

        foreach ($postTypes as $type) { ?>
            <label>
                <input type="checkbox" 
                       name="jialiub_bookmark_enabled_post_types[]" 
                       value="<?php echo esc_attr($type->name); ?>"
                       <?php checked(in_array($type->name, $selected)); ?> />
                <?php echo esc_html($type->label); ?>
            </label><br>
        <?php }
    }

    /**
     * Radio field for button position
     */
    public function buttonPositionField() {
        if (!current_user_can('manage_options')) return;
        $value = get_option('jialiub_button_position', 'after'); ?>
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
    function singularLabelField() {
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
    function pluralLabelField() {
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
    function actionLabelField() {
        if (!current_user_can('manage_options')) return;
        $value = JIALIUB_ACTION_LABEL;
        ?>
            <input type="text" name="jialiub_action_label" value="<?php echo esc_attr($value); ?>" class="regular-text" />
            <p class="description"><?php esc_html_e('E.g. Bookmark, Favorite, Item', 'jiali-user-bookmarks'); ?></p>
        <?php
    }

    // Callback for field
    function showLabelField() {
        $value = get_option('jialiub_show_label', false);
        ?>
        <label>
            <input type="checkbox" name="jialiub_show_label" value="1" <?php checked($value, true); ?>>
            <?php esc_html_e('Show label next to icon/button', 'jiali-user-bookmarks'); ?>
        </label>
        <?php
    }

    // Callback for field
    function showCountField() {
        $value = get_option('jialiub_show_count', true);
        ?>
        <label>
            <input type="checkbox" name="jialiub_show_count" value="1" <?php checked($value, true); ?>>
            <?php esc_html_e('Show count next to icon/button', 'jiali-user-bookmarks'); ?>
        </label>
        <?php
    }

    // Field Callbacks
    function buttonColorCallback() {
        $value = esc_attr(get_option('jialiub_button_color', '#000'));
        echo '<input type="text" id="jialiub_button_color" class="jialiub-color-field" name="jialiub_button_color" value="' . esc_attr($value) . '">';
    }

    function buttonHoverColorCallback() {
        $value = esc_attr(get_option('jialiub_button_hover_color', '#444'));
        echo '<input type="text" id="jialiub_button_hover_color" class="jialiub-color-field" name="jialiub_button_hover_color" value="' . esc_attr($value) . '">';
    }

    function buttonActiveColorCallback() {
        $value = esc_attr(get_option('jialiub_button_active_color', '#000'));
        echo '<input type="text" id="jialiub_button_active_color" class="jialiub-color-field" name="jialiub_button_active_color" value="' . esc_attr($value) . '">';
    }   

    // Font Size
    function fontSizeCallback() {
        $value = absint( get_option( 'jialiub_font_size' ) ) ?: 14;
        echo '<input type="number" name="jialiub_font_size" value="' . esc_attr($value) . '" class="small-text" min="10" max="36"> px';
    }

    /**
     * Bookmarked posts report page
     */
    public function bookmarkedPostsReportPage() {
        wp_enqueue_style('jialiub-styles');
        echo "<div class='jialiub-container jialiub-container--bg-white p-4'>";
        echo "<h2 class='jialiub-heading'>" . sprintf(
            /* translators: %s: Action label for bookmarks */
            esc_html__('Your %s Posts', 'jiali-user-bookmarks'),
            esc_html(JIALIUB_ACTION_LABEL)
        ) . "</h2>";
        echo wp_kses_post(JialiubViews::getInstance()->renderUserBookmarksTable());
        echo "</div>";

        if (current_user_can('manage_options')) {
            echo "<div class='jialiub-container jialiub-container--bg-white p-4'>";
            echo "<h2 class='jialiub-heading'>" . sprintf(
                /* translators: %s: Action label for bookmarks */
                esc_html__('Top %s Posts', 'jiali-user-bookmarks'),
                esc_html(JIALIUB_ACTION_LABEL)
            ) . "</h2>";
            echo wp_kses_post(JialiubViews::getInstance()->renderTopBookmarksTable());
            echo "</div>";
        }
    }

    /**
     * Shortcodes help page
     */
    public function shortcodesHelpPage() {
        wp_enqueue_style( 'jialiub-styles' );
        ?>
        <div class="jialiub-container jialiub-container--bg-white p-4">
            <h1 class="jialiub-heading"><?php esc_html_e('Shortcodes Help', 'jiali-user-bookmarks'); ?></h1>
            <h2 class="jialiub-heading"><?php esc_html_e('User Bookmarks Shortcode', 'jiali-user-bookmarks'); ?></h2>
            <p><?php esc_html_e('Use the following shortcode to display the Bookmark Button anywhere — including page builder loops, custom templates, and more.', 'jiali-user-bookmarks'); ?></p>
            <pre><code>[jialiub_bookmark_button]</code></pre>
            <p><?php esc_html_e('This shortcode will display a Bookmark Button anywhere on your site.', 'jiali-user-bookmarks'); ?></p>

            <h2 class="jialiub-heading"><?php esc_html_e('User Bookmarks Shortcode', 'jiali-user-bookmarks'); ?></h2>
            <p><?php esc_html_e('Use the following shortcode to display the current user\'s bookmarked posts:', 'jiali-user-bookmarks'); ?></p>
            <pre><code>[jialiub_user_bookmarks_table]</code></pre>
            <p><?php esc_html_e('This shortcode will display a list of posts that the currently logged-in user has bookmarked.', 'jiali-user-bookmarks'); ?></p>

            <h2 class="jialiub-heading"><?php esc_html_e('Top Bookmarks Shortcode', 'jiali-user-bookmarks'); ?></h2>
            <p><?php esc_html_e('Use the following shortcode to display the most bookmarked posts across all users:', 'jiali-user-bookmarks'); ?></p>
            <pre><code>[jialiub_top_bookmarks_table]</code></pre>
            <p><?php esc_html_e('This shortcode will display a list of the most frequently bookmarked posts by all users.', 'jiali-user-bookmarks'); ?></p>
            
            <h3 class="jialiub-heading"><?php esc_html_e('Notice: You can use this in all widgets that accept shortcode.', 'jiali-user-bookmarks'); ?></h3>

        </div>
        <?php    
    }

    /**
     * Enqueue dynamic custom styles
     */
    public function enqueueStyles() {
        $buttonColor = esc_attr(get_option('jialiub_button_color', '#000'));
        $hoverColor = esc_attr(get_option('jialiub_button_hover_color', '#444'));
        $activeColor = esc_attr(get_option('jialiub_button_active_color', '#000'));
        $fontSize = absint( get_option( 'jialiub_font_size' ) ) ?: 14;

        $customCss = "
            .jialiub-bookmark-button { color: {$buttonColor} !important; }
            .jialiub-bookmark-button:hover { color: {$hoverColor} !important; }
            .jialiub-bookmark-button-active { color: {$activeColor} !important; }
            .jialiub-bookmark { font-size: {$fontSize}px !important; }
        ";
        wp_add_inline_style('jialiub-styles', $customCss);
    }
}
