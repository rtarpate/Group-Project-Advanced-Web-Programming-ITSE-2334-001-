<?php
// DatabaseConnector.php — InfinityFree version

class DatabaseConnector
{
    public static function getConnection()
    {
        // === InfinityFree credentials ===
        // These three come directly from your screenshot:
        $host     = 'sql311.infinityfree.com';
        $username = 'if0_40641054';
        $password = 'fEPvA34eZHQn';

        // 🔴 IMPORTANT:
        // Replace the DB name below with the EXACT one shown
        // under "Current Databases" in your InfinityFree panel,
        // e.g. if0_40641054_starstarmediareviewdatabase
        $dbname   = 'if0_40641054_starstarmediareviewdatabase';

        try {
            $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT         => false,
            ]);

            return $pdo;
        } catch (PDOException $e) {
            // You can temporarily uncomment the line below
            // while debugging to see the real error:
            // die("DB error: " . $e->getMessage());
            return false;
        }
    }
}
