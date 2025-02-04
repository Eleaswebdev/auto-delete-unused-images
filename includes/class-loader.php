<?php

class UIC_Loader {
    public static function init() {
        // Load core functionality
        require_once UIC_PLUGIN_DIR . 'includes/class-activator.php';
        require_once UIC_PLUGIN_DIR . 'includes/class-deactivator.php';

        // Load Admin classes
        require_once UIC_PLUGIN_DIR . 'includes/Admin/class-admin-settings.php';

        require_once UIC_PLUGIN_DIR . 'includes/Assets/class-assets.php';

        // Load Database classes
        require_once UIC_PLUGIN_DIR . 'includes/Database/class-database-handler.php';

        // Initialize components
        new UIC_Admin_Settings();
        new UIC_Database_Handler();
        new UIC_Assets();
    }
}
