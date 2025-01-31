<?php

class Auto_Delete_Unused_Images_Assets {
    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'load_assets'));
    }

    // Load JavaScript & CSS
    public function load_assets($hook) {
        if ($hook !== 'toplevel_page_unused-images-cleaner') {
            return;
        }
        wp_enqueue_script('unused-images-cleaner', plugin_dir_url(__FILE__) . '../../assets/js/script.js', array('jquery'), '1.0', true);
        wp_localize_script('unused-images-cleaner', 'unusedImagesCleaner', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('unused_images_nonce')
        ));
        wp_enqueue_style('unused-images-cleaner', plugin_dir_url(__FILE__) . '../../assets/css/style.css');
    }
}
