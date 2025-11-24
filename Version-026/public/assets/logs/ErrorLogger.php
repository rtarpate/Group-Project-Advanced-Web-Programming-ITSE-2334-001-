<?php

class ErrorLogger {

    public static function log($message) {
        $logFile = __DIR__ . "/error_log.txt";

        $entry = "[" . date("Y-m-d H:i:s") . "] " . $message . PHP_EOL;

        file_put_contents($logFile, $entry, FILE_APPEND);
    }
}
