<?php
header('Content-Type: application/json');

require_once __DIR__ . '/DatabaseConnector.php';

try {
    $pdo = DatabaseConnector::getConnection();

    $stmt = $pdo->query("
        SELECT media_id, title
        FROM media
        ORDER BY title ASC
    ");

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to load media list'
    ]);
}
