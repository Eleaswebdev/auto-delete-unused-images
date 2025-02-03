<?php

class ADUI_Loader {
    public static function init() {
        // Load traits
        //require_once ADUI_PLUGIN_DIR . 'traits/trait-helpers.php';

        // Load utility classes
       // require_once ADUI_PLUGIN_DIR . 'utilities/class-logger.php';

        // Load core functionality
        require_once ADUI_PLUGIN_DIR . 'includes/class-activator.php';
        require_once ADUI_PLUGIN_DIR . 'includes/class-deactivator.php';

        // Load Admin classes
        require_once ADUI_PLUGIN_DIR . 'includes/Admin/class-admin-settings.php';

        require_once ADUI_PLUGIN_DIR . 'includes/Assets/class-assets.php';

        // Load Database classes
        require_once ADUI_PLUGIN_DIR . 'includes/Database/class-database-handler.php';

        // Initialize components
        new ADUI_Admin_Settings();
        new ADUI_Database_Handler();
        new ADUI_Assets();
    }
}
