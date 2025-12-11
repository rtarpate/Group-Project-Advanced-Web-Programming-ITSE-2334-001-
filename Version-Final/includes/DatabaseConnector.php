<?php
// ============================================================
// DatabaseConnector.php — PDO connection helper
// ============================================================

require_once __DIR__ . '/DatabaseConfig.php';

class DatabaseConnector
{
    public static function getConnection(): ?PDO
    {
        try {
            $dsn = "mysql:host=" . DatabaseConfig::$DB_HOST . ";dbname=" . DatabaseConfig::$DB_NAME . ";charset=utf8mb4";

            $pdo = new PDO($dsn, DatabaseConfig::$DB_USER, DatabaseConfig::$DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false
            ]);

            return $pdo;
        } catch (Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            return null;
        }
    }
}
