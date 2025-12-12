<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/admin-session.php';
require_admin();

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    http_response_code(500);
    echo '<h2 style="text-align:center;margin-top:40px;">Database connection failed.</h2>';
    echo '<p style="text-align:center;">Check your InfinityFree DB credentials in <code>includes/DatabaseConfig.php</code>.</p>';
    exit;
}
$statusMessage = "";
$statusType = "";

// Handle Save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save') {

    $mediaId = $_POST['media_id'];
    $title = trim($_POST['title']);
    $type = $_POST['media_type_id'];
    $genre = $_POST['genre_id'];
    $rating = $_POST['content_rating_id'];
    $release = $_POST['release_date'];
    $director = trim($_POST['director']);

    $imageFile = null;

    // FIXED HOSTING-SAFE UPLOAD PATH
    if (!empty($_FILES['image']['name'])) {
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $uploadDir = realpath(__DIR__ . '/../assets/images');

        if ($uploadDir !== false) {
            $targetPath = $uploadDir . '/' . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                $imageFile = $fileName;
            }
        }
    }

    $sql = "UPDATE media
            SET title = ?, media_type_id = ?, genre_id = ?, content_rating_id = ?, release_date = ?, director = ?" .
            ($imageFile ? ", image_path = ?" : "") .
            " WHERE media_id = ?";

    $params = [$title, $type, $genre, $rating, $release, $director];
    if ($imageFile) $params[] = $imageFile;
    $params[] = $mediaId;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $statusMessage = "Media updated successfully.";
    $statusType = "success";
}

// handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete') {
    $stmt = $pdo->prepare("DELETE FROM media WHERE media_id = ?");
    $stmt->execute([$_POST['media_id']]);
    $statusMessage = "Media deleted successfully.";
    $statusType = "success";
}

// Load all media
$stmt = $pdo->query("SELECT m.*, mt.type_name, g.genre_name, cr.rating_code
                     FROM media m
                     LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
                     LEFT JOIN genres g ON m.genre_id = g.genre_id
                     LEFT JOIN content_ratings cr ON m.content_rating_id = cr.rating_id
                     ORDER BY m.media_id DESC");

$mediaItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load taxonomy
$types = $pdo->query("SELECT * FROM media_types ORDER BY type_name")->fetchAll(PDO::FETCH_ASSOC);
$genres = $pdo->query("SELECT * FROM genres ORDER BY genre_name")->fetchAll(PDO::FETCH_ASSOC);
$ratings = $pdo->query("SELECT * FROM content_ratings ORDER BY rating_code")->fetchAll(PDO::FETCH_ASSOC);

// include admin UI…