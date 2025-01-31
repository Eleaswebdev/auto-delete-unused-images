<?php

class Auto_Delete_Unused_Images_Admin_Settings {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_scan_unused_images', array($this, 'scan_unused_images'));
        add_action('wp_ajax_delete_unused_images', array($this, 'delete_unused_images'));
    }

    // Add menu page in admin
    public function add_admin_menu() {
        add_menu_page(
            'Unused Images Cleaner',
            'Unused Images',
            'manage_options',
            'unused-images-cleaner',
            array($this, 'admin_page'),
            'dashicons-trash'
        );
    }

    // Admin Page Content
    public function admin_page() {
        ?>
        <div class="wrap">
            <h2>Unused Images Cleaner</h2>
            <p>Click the button below to scan for unused images.</p>
            <button id="scan-unused-images" class="button button-primary">Scan Now</button>
            <button id="delete-selected-images" class="button button-danger" style="display: none;">Delete Selected</button>
            <div id="results"></div>
        </div>
        <?php
    }

    // Scan for unused images
    public function scan_unused_images() {
        check_ajax_referer('unused_images_nonce', 'security');
        $database_handler = new Auto_Delete_Unused_Images_Database_Handler();
        $unused_images = $database_handler->get_unused_images();
        wp_send_json_success($unused_images);
    }

    // Delete selected images
    public function delete_unused_images() {
        check_ajax_referer('unused_images_nonce', 'security');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized access.');
        }
        
        $image_ids = isset($_POST['image_ids']) ? array_map('intval', $_POST['image_ids']) : array();
        $database_handler = new Auto_Delete_Unused_Images_Database_Handler();
        $database_handler->delete_images($image_ids);
        wp_send_json_success('Selected images deleted successfully.');
    }
}
