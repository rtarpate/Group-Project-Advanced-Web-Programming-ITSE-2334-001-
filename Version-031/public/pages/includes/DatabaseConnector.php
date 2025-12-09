<?php

require_once __DIR__
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . 'assets'
    . DIRECTORY_SEPARATOR . 'logs'
    . DIRECTORY_SEPARATOR . 'ErrorLogger.php';

class DatabaseConnector
{
    public static function getConnection()
    {
        $config = require __DIR__ . DIRECTORY_SEPARATOR . 'DatabaseConfig.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

        try {
            return new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            ErrorLogger::log("DB CONNECTION ERROR: " . $e->getMessage());
            return null;
        }
    }
}
