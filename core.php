<?php

/**
 * Plugin Name: Jiali User Bookmarks
 * Plugin URI: https://mahyarerad.com
 * Description: Let your users easily bookmark posts. Lightweight, AJAX-based, and perfect for saving content they love.
 * Version: 1.0.0
 * Author: Mahyar Rad
 * Author URI: https://mahyarerad.com/
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jiali-user-bookmarks
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Include Core class
require_once plugin_dir_path(__FILE__) . 'JialiubCore.php';

// Start plugin
JialiubCore::getInstance();

// Activation hook
register_activation_hook(__FILE__, ['JialiubCore', 'registerActivation']);

// Uninstallation hook
register_uninstall_hook(__FILE__, ['JialiubCore', 'uninstallation'] );

/*
Prefix Guidance for Aabgine POS Plugin

Constants:      JIALIUB_      (e.g. JIALIUB_PLUGIN_URL)
Class Names:    Jialiub       (e.g. JialiubCore )
DB Tables:      jialiub_         (e.g. jialiub_orders, jialiub_customers)
Functions:      jialiub_         (e.g.  jialiub_add_bookmark())
Text Domain:    jiali-user-bookmarks       (for translations)

Always use these prefixes to avoid conflicts and keep code organized.
*/

?>
