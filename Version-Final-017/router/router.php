<?php


header('Content-Type: application/json');
ini_set('display_errors', 0);

require_once __DIR__ . '/../includes/DatabaseConnector.php';

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

case 'getMedia':
    try {
        $sql = "
            SELECT
                m.media_id,
                m.media_title,
                m.release_date,
                m.director,
                m.media_image,
                m.media_type,
                g.genre_name,
                m.content_rating,
                ROUND(AVG(r.rating), 2) AS average_rating,
                COUNT(r.review_id) AS review_count
            FROM media m
            LEFT JOIN genres g ON m.genre_id = g.genre_id
            LEFT JOIN reviews r ON m.media_id = r.media_id
            GROUP BY m.media_id
            ORDER BY m.media_title ASC
        ";

        $stmt = $pdo->query($sql);
        $media = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'media' => $media
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Server error',
            'error' => $e->getMessage()
        ]);
    }
    exit;

