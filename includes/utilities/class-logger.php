<?php

class Simple_Notify_Logger {
    public static function log_error($message) {
        error_log('[Notify - Custom Notification Plugin] ' . $message);
    }
}
