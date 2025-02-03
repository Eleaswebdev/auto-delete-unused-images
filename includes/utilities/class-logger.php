<?php

class ADUI_Logger {
    public static function log_error($message) {
        error_log('[Notify - Custom Notification Plugin] ' . $message);
    }
}
