<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubCore {

    private static $instance = null;

    public function __construct() {
        $this->defineConstants();
        $this->registerAutoload();
        $this->init();
    }

    private function __clone() {}

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function registerAutoload() {
        spl_autoload_register(function ($class_name) {
            // Only autoload classes starting with "Ab"
            if (strpos($class_name, 'Jialiub') === 0) {
                $file = JIALIUB_CLASSES_PATH . $class_name . '.php';
                if (file_exists($file)) {
                    require_once $file;
                }
            }
        });
    }

    private function defineConstants() {
        define('JIALIUB_PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('JIALIUB_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('JIALIUB_CLASSES_PATH', JIALIUB_PLUGIN_PATH . 'classes/');
        define('JIALIUB_ASSETS_URI', JIALIUB_PLUGIN_URL . 'assets');
        $singular = get_option('jialiub_singular_label');
        $plural = get_option('jialiub_plural_label');
        $action = get_option('jialiub_action_label');
        define('JIALIUB_SINGULAR_LABEL', !empty($singular) ? $singular : esc_html__( 'Bookmark', 'jiali-user-bookmarks' ) );
        define('JIALIUB_PLURAL_LABEL', !empty($plural) ? $plural : esc_html__( 'Bookmarks', 'jiali-user-bookmarks' ) );
        define('JIALIUB_ACTION_LABEL', !empty($action) ? $action : esc_html__( 'Bookmarked', 'jiali-user-bookmarks' ) );
    }

    private function init() {
        // Hook into WordPress
        add_action('init', [$this, 'startOutputBuffers']);
   
        add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
        add_action('admin_enqueue_scripts', [$this, 'adminRegisterAssets']);

        include_once( ABSPATH.'wp-includes/pluggable.php'); // For getting wp_get_current_user and etc. 
        // include_once(JIALIUB_PLUGIN_PATH.'inc/functions.php');
        include_once(JIALIUB_PLUGIN_PATH.'inc/front.php');
        include_once(JIALIUB_PLUGIN_PATH.'inc/settings.php');
        include_once(JIALIUB_PLUGIN_PATH.'inc/shortcodes.php');
        include_once(JIALIUB_PLUGIN_PATH.'inc/ajax-functions.php');
        
    }

    // Start output buffering
    public static function startOutputBuffers() {
        ob_start();
    }

    // Activate plugin
    public static function registerActivation() {
        JialiubDatabaseInstaller::installTables();
    }

    // Registering all assets
    public function registerAssets() {
        JialiubRegisterAssets::registerAssets();
    }

    public function adminRegisterAssets() {
        JialiubRegisterAssets::adminRegisterAssets();
    }

    public static function uninstallation() {
		// Deactivation logic here
	}
}