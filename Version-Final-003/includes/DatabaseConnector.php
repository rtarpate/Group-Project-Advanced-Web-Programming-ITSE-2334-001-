<?php
// DatabaseConnector.php — InfinityFree compatible

class DatabaseConnector {

    public static function getConnection() {

        // IMPORTANT — Replace these with your InfinityFree DB values.
        $host     = "sqlXXX.epizy.com";   // example: sql212.epizy.com
        $username = "epiz_12345678";      // your InfinityFree username
        $password = "YOUR_DB_PASSWORD";   // the password InfinityFree gave you
        $dbname   = "epiz_12345678_main"; // your InfinityFree database name

        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_PERSISTENT         => false,
            ]);

            return $pdo;

        } catch (PDOException $e) {
            return false;  // InfinityFree hides errors unless debug mode is enabled
        }
    }
}
?>
