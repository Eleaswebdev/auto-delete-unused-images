<?php
/**
 * Plugin Name: Unused Images Cleaner
 * Plugin URI:  https://github.com/Eleaswebdev/auto-delete-unused-images
 * Description: Detects and deletes unused images from the WordPress media library including bulk delete.
 * Version: 1.0.0
 * Author: Eleas Kanchon
 * Author URI: https://github.com/Eleaswebdev
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: unused-images-cleaner
 */

// Prevent direct access.
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin directory path
define('UIC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UIC_PLUGIN_DIR_URL', plugin_dir_url(__FILE__));

// Autoload class files
require_once UIC_PLUGIN_DIR . 'includes/class-loader.php';

// Initialize the loader
UIC_Loader::init();


