<?php

class Auto_Delete_Unused_Images_Loader {
    public static function init() {
        // Load traits
        //require_once plugin_dir_path(__FILE__) . 'traits/trait-helpers.php';

        // Load utility classes
       // require_once plugin_dir_path(__FILE__) . 'utilities/class-logger.php';

        // Load core functionality
        require_once plugin_dir_path(__FILE__) . 'class-activator.php';
        require_once plugin_dir_path(__FILE__) . 'class-deactivator.php';

        // Load Admin classes
        require_once plugin_dir_path(__FILE__) . 'Admin/class-admin-settings.php';

        require_once plugin_dir_path(__FILE__) . 'Assets/class-assets.php';

        // Load Database classes
        require_once plugin_dir_path(__FILE__) . 'Database/class-database-handler.php';

        // Initialize components
        new Auto_Delete_Unused_Images_Admin_Settings();
        new Auto_Delete_Unused_Images_Database_Handler();
        new Auto_Delete_Unused_Images_Assets();
    }
}
