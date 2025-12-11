<?php
//------------------------------------------------------
// admin-add-media.php (UPDATED FOR VERSION-031)
// Uses real admin-session.php in the admin folder
// and adds troubleshooting output.
//------------------------------------------------------

session_start();

//--------------------------------------
// Includes (AUTH + DB + LOGGING)
//--------------------------------------
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

//------------------------------------------------------
// Simple optional debug flag (toggle as needed)
//------------------------------------------------------
$DEBUG_MODE = false;

function debug_out($msg) {
    global $DEBUG_MODE;
    if ($DEBUG_MODE) {
        echo "<pre>DEBUG: " . htmlspecialchars($msg) . "</pre>";
    }
}

//------------------------------------------------------
// CONNECT TO DB
//------------------------------------------------------
$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

//------------------------------------------------------
// FETCH DROP-DOWNS DATA
//------------------------------------------------------
try {
    // Media Types
    $stmt = $pdo->query("SELECT media_type_id, type_name FROM media_types ORDER BY type_name ASC");
    $mediaTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Genres
    $stmt = $pdo->query("SELECT genre_id, genre_name FROM genres ORDER BY genre_name ASC");
    $genres = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Content Ratings
    $stmt = $pdo->query("SELECT rating_id, rating_code FROM content_ratings ORDER BY rating_code ASC");
    $contentRatings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    log_error("Error fetching taxonomy data in admin-add-media.php: " . $e->getMessage());
    die("Error loading form data.");
}

$message = '';
$errorMessage = '';

//--------------------------------------
// HANDLE FORM SUBMISSION
//--------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    debug_out('Form submitted.');

    $title             = trim($_POST['title'] ?? '');
    $release_date      = trim($_POST['release_date'] ?? '');
    $director          = trim($_POST['director'] ?? '');
    $genre_id          = trim($_POST['genre_id'] ?? '');
    $media_type_id     = trim($_POST['media_type_id'] ?? '');
    $content_rating_id = trim($_POST['content_rating_id'] ?? '');
    $image_path_raw    = trim($_POST['image_path'] ?? '');

    // Validation
    if ($title === '' || $media_type_id === '' || $genre_id === '') {
        $errorMessage = "Title, Media Type, and Genre are required.";
    } else {
        // If an image was uploaded via file input, you could handle it here.
        // For now, we assume the image_path is a filename.
        $image_path = $image_path_raw !== '' ? $image_path_raw : '0.jpg';

        try {
            $sql = "
                INSERT INTO media
                    (title, release_date, director, genre_id, media_type_id, content_rating_id, image_path)
                VALUES
                    (:title, :release_date, :director, :genre_id, :media_type_id, :content_rating_id, :image_path)
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':title'             => $title,
                ':release_date'      => $release_date ?: null,
                ':director'          => $director ?: null,
                ':genre_id'          => $genre_id ?: null,
                ':media_type_id'     => $media_type_id ?: null,
                ':content_rating_id' => $content_rating_id ?: null,
                ':image_path'        => $image_path,
            ]);

            $message = "Media added successfully!";
        } catch (Exception $e) {
            log_error("Error inserting media in admin-add-media.php: " . $e->getMessage());
            $errorMessage = "Failed to add media.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add Media</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>

<?php include __DIR__ . '/admin-nav.php'; ?>

<main class="admin-main">

    <h1>Add New Media</h1>

    <?php if ($message): ?>
        <p class="admin-success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <p class="admin-error"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form method="post" class="admin-form">

        <label for="title">Title *</label>
        <input type="text" name="title" id="title" required>

        <label for="release_date">Release Date</label>
        <input type="date" name="release_date" id="release_date">

        <label for="director">Director</label>
        <input type="text" name="director" id="director">

        <label for="genre_id">Genre *</label>
        <select name="genre_id" id="genre_id" required>
            <option value="">-- Select Genre --</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?php echo htmlspecialchars($g['genre_id']); ?>">
                    <?php echo htmlspecialchars($g['genre_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="media_type_id">Media Type *</label>
        <select name="media_type_id" id="media_type_id" required>
            <option value="">-- Select Type --</option>
            <?php foreach ($mediaTypes as $mt): ?>
                <option value="<?php echo htmlspecialchars($mt['media_type_id']); ?>">
                    <?php echo htmlspecialchars($mt['type_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="content_rating_id">Content Rating</label>
        <select name="content_rating_id" id="content_rating_id">
            <option value="">-- Select Rating --</option>
            <?php foreach ($contentRatings as $cr): ?>
                <option value="<?php echo htmlspecialchars($cr['rating_id']); ?>">
                    <?php echo htmlspecialchars($cr['rating_code']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="image_path">Image Filename (optional)</label>
        <input type="text" name="image_path" id="image_path" placeholder="e.g., 12.jpg">

        <button type="submit" class="admin-btn">Add Media</button>
    </form>

</main>

</body>
</html>
