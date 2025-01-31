<?php
/**
 * Plugin Name: Auto Delete Unused Images
 * Plugin URI:  https://github.com/Eleaswebdev/auto-delete-unused-images
 * Description: Automatically detects and deletes unused images from the WordPress media library. Includes bulk delete, scheduled cleanup, and backup options.
 * Version:     1.0.0
 * Author: Eleas Kanchon
 * Author URI: https://github.com/Eleaswebdev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: auto-delete-unused-images
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin directory path
define('ADUI_PLUGIN_DIR', plugin_dir_path(__FILE__));


// Autoload class files
require_once ADUI_PLUGIN_DIR . 'includes/class-loader.php';
//require_once plugin_dir_path(__FILE__) . 'includes/class-loader.php';

// Initialize the loader
Auto_Delete_Unused_Images_Loader::init();


