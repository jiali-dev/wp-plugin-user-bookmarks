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

    private function defineConstants() {
        JialiubDefineConstants::defien();
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

    private function init() {
        // Hook into WordPress
        add_action('plugins_loaded', [$this, 'loadTextdomain']);
        add_action('init', [$this, 'startOutputBuffers']);
   
        add_action('wp_enqueue_scripts', [$this, 'registerAssets']);
        add_action('admin_enqueue_scripts', [$this, 'adminRegisterAssets']);

        include_once( ABSPATH.'wp-includes/pluggable.php'); // For getting wp_get_current_user and etc. 
        // include_once(JIALIUB_PLUGIN_PATH.'inc/functions.php');
        include_once(JIALIUB_PLUGIN_PATH.'inc/bookmark-functions.php');
        include_once(JIALIUB_PLUGIN_PATH.'inc/front.php');
        include_once(JIALIUB_PLUGIN_PATH.'inc/settings.php');
        include_once(JIALIUB_PLUGIN_PATH.'inc/shortcodes.php');
        include_once(JIALIUB_PLUGIN_PATH.'inc/ajax-functions.php');
        include_once(JIALIUB_CLASSES_PATH.'JialiubPostsListTable.php');
        
    }

    // Load Textdomain for translations
    public static function loadTextdomain() {
        load_plugin_textdomain('jiali-user-bookmarks', false, 'languages');
    }

    // Start output buffering
    public static function startOutputBuffers() {
        ob_start();
    }

    // Activate plugin
    public static function registerActivation() {

    }

    // Registering all assets
    public function registerAssets() {
        JialiubRegisterAssets::registerAssets();
    }

    public function adminRegisterAssets() {
        //    wp_enqueue_style('ab-admin-style', JIALIUB_PLUGIN_URL . 'assets/css/admin-style.css', [], '1.0.0');    
    }

    public static function uninstallation() {
		// Deactivation logic here
	}
}