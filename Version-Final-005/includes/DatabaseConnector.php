<?php
// DatabaseConnector.php — InfinityFree Version

class DatabaseConnector {

    public static function getConnection() {

        // === InfinityFree Credentials ===
        $host     = 'sql311.infinityfree.com';
        $username = 'if0_40641054';
        $password = 'fEPvA34eZHQn';

        // ❗ THIS IS YOUR REAL DATABASE NAME
        $dbname   = 'if0_40641054_starstarmediareviewdatabase';

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT         => false,
            ]);

            return $pdo;

        } catch (PDOException $e) {
            // Uncomment for debugging:
            // die("DB ERROR: " . $e->getMessage());
            return false;
        }
    }
}
?>
