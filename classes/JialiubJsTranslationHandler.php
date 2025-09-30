<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

class JialiubJsTranslationHandler {

    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'registerJsTranslation']);
        add_action('admin_enqueue_scripts', [$this, 'registerJsTranslation']);
    }

    public function registerJsTranslation() {
        // Ensure script is already enqueued
        wp_enqueue_script('jialiub-script');

        wp_localize_script(
            'jialiub-script',
            'jialiub_translate_handler', 
            [
                'title' => __('Title', 'jiali-user-bookmarks'),
                'author' => __('Author', 'jiali-user-bookmarks'),
                'actions' => __('Actions', 'jiali-user-bookmarks'),
                'count' => __('Count', 'jiali-user-bookmarks'),
                'error_occurred' => __('An error occurred. Please try again.', 'jiali-user-bookmarks'),
            ]
        );
    }
}