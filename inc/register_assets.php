<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Add theme assets --> Register for globaly and enqueue for specific page
function jialiufl_register_assets() {
    // Register Fontawesome
    wp_register_style('jialiufl-fontawesome', JIALIUFL_ASSETS_URI . '/plugins/fontawesome/all.min.css' , array(), '6.7.2', 'all');
    wp_register_script('jialiufl-fontawesome', JIALIUFL_ASSETS_URI . '/plugins/fontawesome/all.min.js' , array(), '6.7.2', true);
    
    // Register Notiflix
    wp_register_style('jialiufl-notiflix', JIALIUFL_ASSETS_URI . '/plugins/notiflix/notiflix.min.css' , array(), '3.2.8', 'all');
    wp_register_script('jialiufl-notiflix', JIALIUFL_ASSETS_URI . '/plugins/notiflix/notiflix.min.js' , array(), '3.2.8', true);
    wp_register_script('jialiufl-notiflix-custom', JIALIUFL_ASSETS_URI . '/plugins/notiflix/notiflix-custom.js' , array(), '3.2.8', true);

    // Register styles
    wp_register_style('jialiufl-styles', JIALIUFL_CSS_URI . '/styles.css' , array(), '1.0.0', 'all');
    // Register scripts
    wp_register_script('jialiufl-script', JIALIUFL_JS_URI . '/main.js', array('jquery'), '1.0.0', true);

    // Localize script
    wp_localize_script( 'jialiufl-script', 'jialiufl_ajax', 
        array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('jialiufl-nonce')
        )
    );
}
add_action('wp_enqueue_scripts', 'jialiufl_register_assets');

?>