<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubRegisterAssets {
    
    // Registering all assets
    public static function registerAssets() {

        // Register datatable
        wp_register_style('jialiub-datatable', JIALIUB_ASSETS_URI . '/plugins/datatable/datatable.min.css' , array(), '6.7.2', 'all');
        wp_register_script('jialiub-datatable', JIALIUB_ASSETS_URI . '/plugins/datatable/datatable.min.js' , array(), '6.7.2', true);
        
        // Register Fontawesome
        wp_register_style('jialiub-fontawesome', JIALIUB_ASSETS_URI . '/plugins/fontawesome/all.min.css' , array(), '6.7.2', 'all');
        wp_register_script('jialiub-fontawesome', JIALIUB_ASSETS_URI . '/plugins/fontawesome/all.min.js' , array(), '6.7.2', true);
        
        // Register Notiflix
        wp_register_style('jialiub-notiflix', JIALIUB_ASSETS_URI . '/plugins/notiflix/notiflix.min.css' , array(), '3.2.8', 'all');
        wp_register_script('jialiub-notiflix', JIALIUB_ASSETS_URI . '/plugins/notiflix/notiflix.min.js' , array(), '3.2.8', true);
        wp_register_script('jialiub-notiflix-custom', JIALIUB_ASSETS_URI . '/plugins/notiflix/notiflix-custom.js' , array(), '3.2.8', true);

        // Register datatable
        wp_register_style('jialiub-datatable', JIALIUB_ASSETS_URI . '/plugins/datatable/datatable.min.css' , array(), '2.3.4', 'all');
        wp_register_script('jialiub-datatable', JIALIUB_ASSETS_URI . '/plugins/datatable/datatable.min.js' , array(), '2.3.4', true);
        wp_register_script('jialiub-datatable-custom', JIALIUB_ASSETS_URI . '/plugins/datatable/datatable-custom.js' , array(), '2.3.4', true);
        
        // Register styles
        wp_register_style('jialiub-styles', JIALIUB_ASSETS_URI . '/css/styles.css' , array(), '1.0.0', 'all');
        // Register scripts
        wp_register_script('jialiub-script', JIALIUB_ASSETS_URI . '/js/main.js', array('jquery'), '1.0.0', true);

        // Localize script
        wp_localize_script( 'jialiub-script', 'jialiub_ajax', 
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('jialiub-nonce')
            )
        );
    } 

    public static function adminRegisterAssets() {

        // Register datatable
        wp_register_style('jialiub-datatable', JIALIUB_ASSETS_URI . '/plugins/datatable/datatable.min.css' , array(), '2.3.4', 'all');
        wp_register_script('jialiub-datatable', JIALIUB_ASSETS_URI . '/plugins/datatable/datatable.min.js' , array(), '2.3.4', true);
        wp_register_script('jialiub-datatable-custom', JIALIUB_ASSETS_URI . '/plugins/datatable/datatable-custom.js' , array(), '2.3.4', true);
        
        // Register styles
        wp_register_style('jialiub-styles', JIALIUB_ASSETS_URI . '/css/styles.css' , array(), '1.0.0', 'all');
        // Register scripts
        wp_register_script('jialiub-script', JIALIUB_ASSETS_URI . '/js/main.js', array('jquery'), '1.0.0', true);

        // Localize script
        wp_localize_script( 'jialiub-script', 'jialiub_ajax', 
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('jialiub-nonce')
            )
        );
        
    }
    
}

?>