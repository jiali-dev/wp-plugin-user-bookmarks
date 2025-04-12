<?php 

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Hook to add the Scroll to Top button and enqueue scripts/styles
function jialiufl_favorite_and_like_button_layout() {
   
    ob_start(); // Start output buffering
    ?>
        <div class="jialiufl-favorite-and-like-button">
            <span class="jiali-like-button">
                <i class="fa-regular fa-heart"></i>
            </span>
            <span class="jiali-favorite-button">
                <i class="fa-regular fa-bookmark"></i>
            </span>
        </div>
    <?php
    $output = ob_get_clean(); 

    // Enqueue the styles and scripts
    wp_enqueue_style( 'jialiufl-fontawesome' );
    wp_enqueue_script( 'jialiufl-fontawesome' );
    wp_enqueue_style( 'jialiufl-styles' );
    wp_enqueue_script( 'jialiufl-main' );

    echo $output;
}
add_action('wp_footer', 'jialiufl_favorite_and_like_button_layout');

?>