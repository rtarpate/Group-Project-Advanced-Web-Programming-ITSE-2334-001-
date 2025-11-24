<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

// SAFE, OS-INDEPENDENT PATHS
require_once __DIR__ . DIRECTORY_SEPARATOR . "DatabaseConnector.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . "starstarmediareviewdatabase.php";
require_once __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "assets" . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . "ErrorLogger.php";

header("Content-Type: application/json");

try {
    $db = DatabaseConnector::getConnection();

    if (!$db) {
        echo json_encode(["status" => "error", "message" => "Could not connect to database"]);
        exit;
    }

    $sql = "
        SELECT 
            m.media_id,
            m.title,
            m.image_path,
            m.genre,
            m.content_rating,
            m.release_date,
            mt.type_name AS media_type,
            IFNULL(r.average_rating, 0) AS average_rating,
            IFNULL(r.total_ratings, 0) AS total_ratings
        FROM media m
        LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
        LEFT JOIN media_ratings r ON m.media_id = r.media_id
        ORDER BY m.title ASC;
    ";

    $stmt = $db->query($sql);
    $results = $stmt->fetchAll();

    echo json_encode([
        "status" => "success",
        "data" => $results
    ]);

} catch (Exception $e) {

    ErrorLogger::log("GET-REVIEWS ERROR: " . $e->getMessage());

    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
        "line" => $e->getLine(),
        "file" => $e->getFile()
    ]);
}
