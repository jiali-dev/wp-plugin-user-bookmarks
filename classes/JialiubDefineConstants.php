<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubDefineConstants {
    
    // Registering all assets
    public static function define() {

        define('JIALIUB_PLUGIN_PATH', plugin_dir_path(__FILE__));
        define('JIALIUB_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('JIALIUB_CLASSES_PATH', JIALIUB_PLUGIN_PATH . 'classes/');
        define('JIALIUB_ASSETS_URI', JIALIUB_PLUGIN_URL . 'assets');
        define('JIALIUB_CSS_URI', JIALIUB_ASSETS_URI . 'css');
        define('JIALIUB_JS_URI', JIALIUB_ASSETS_URI . 'js');
        $singular = get_option('jialiub_singular_label');
        $plural = get_option('jialiub_plural_label');
        define('JIALIUB_SINGULAR_LABEL', !empty($singular) ? $singular : esc_html__( 'Bookmark', 'jiali-user-bookmarks' ) );
        define('JIALIUB_PLURAL_LABEL', !empty($plural) ? $plural : esc_html__( 'Bookmarks', 'jiali-user-bookmarks' ) );

    } 
    
}

?>