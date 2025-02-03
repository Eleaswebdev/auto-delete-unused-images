<?php
/**
 * Shared functionality accross different classes
 */

 trait ADUI_Trait_Helpers {
    public function notify($message, $type = "info") {
        return sprintf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>',  $type, $message);
    }
 }