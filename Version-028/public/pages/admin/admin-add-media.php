<?php
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/starstarmediareviewdatabase.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../../assets/logs/ErrorLogger.php';

$db = DatabaseConnector::getConnection();
if (!$db) {
    die("Database connection error.");
}

// Fetch media types for dropdown
$mediaTypes = [];
try {
    $stmt = $db->query("SELECT media_type_id, type_name FROM media_types ORDER BY type_name ASC");
    $mediaTypes = $stmt->fetchAll();
} catch (PDOException $e) {
    ErrorLogger::log("FETCH MEDIA TYPES ERROR: " . $e->getMessage());
}

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title         = clean('title', 'string', INPUT_POST);
    $release_date  = clean('release_date', 'string', INPUT_POST);
    $director      = clean('director', 'string', INPUT_POST);
    $genre         = clean('genre', 'string', INPUT_POST);
    $media_type_id = (int) clean('media_type_id', 'int', INPUT_POST);
    $content_rating= clean('content_rating', 'string', INPUT_POST);

    if ($title === '' || $release_date === '' || $director === '' || $genre === '' || $media_type_id <= 0 || $content_rating === '') {
        $error = 'All fields are required.';
    } elseif (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload an image file.';
    } else {
        try {
            $db->beginTransaction();

            // Insert media with temporary image_path
            $sql = "INSERT INTO media (title, release_date, director, genre, media_type_id, content_rating, image_path)
                    VALUES (:title, :release_date, :director, :genre, :media_type_id, :content_rating, 'temp')";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':title'         => $title,
                ':release_date'  => $release_date,
                ':director'      => $director,
                ':genre'         => $genre,
                ':media_type_id' => $media_type_id,
                ':content_rating'=> $content_rating,
            ]);

            $mediaId = (int)$db->lastInsertId();

            // Handle image upload and convert/rename to {media_id}.jpg
            $imageTmpPath = $_FILES['image_file']['tmp_name'];
            $imageMime    = mime_content_type($imageTmpPath);

            $targetDir    = __DIR__ . '/../../assets/images/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            $finalName    = $mediaId . '.jpg';
            $targetPath   = $targetDir . $finalName;

            // Accept PNG or JPEG; always save as JPG
            if ($imageMime === 'image/png') {
                $image = imagecreatefrompng($imageTmpPath);
                if (!$image) {
                    throw new RuntimeException("Failed to read PNG image.");
                }
                imagejpeg($image, $targetPath, 90);
                imagedestroy($image);
            } elseif ($imageMime === 'image/jpeg' || $imageMime === 'image/jpg') {
                if (!move_uploaded_file($imageTmpPath, $targetPath)) {
                    throw new RuntimeException("Failed to move uploaded JPG image.");
                }
            } else {
                throw new RuntimeException("Unsupported image format. Please upload PNG or JPG.");
            }

            // Update media image_path
            $updateSql = "UPDATE media SET image_path = :path WHERE media_id = :id";
            $stmt2 = $db->prepare($updateSql);
            $stmt2->execute([
                ':path' => $finalName,
                ':id'   => $mediaId,
            ]);

            // Initialize media_ratings entry (0 / 0)
            $initRatingSql = "INSERT INTO media_ratings (media_id, average_rating, total_ratings)
                              VALUES (:id, 0, 0)";
            $stmt3 = $db->prepare($initRatingSql);
            $stmt3->execute([':id' => $mediaId]);

            $db->commit();

            $success = "Media added successfully with ID {$mediaId}.";
        } catch (Throwable $e) {
            $db->rollBack();
            ErrorLogger::log("ADMIN ADD MEDIA ERROR: " . $e->getMessage());
            $error = 'Failed to add media. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Media - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<header>
    <h1>Admin - Add New Media</h1>
</header>

<div class="container">
    <p><a href="admin-dashboard.php">&larr; Back to Dashboard</a></p>

    <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="release_date">Release Date:</label>
        <input type="date" name="release_date" id="release_date" required>

        <label for="director">Director / Creator:</label>
        <input type="text" name="director" id="director" required>

        <label for="genre">Genre:</label>
        <select name="genre" id="genre" required>
            <option value="">-- Select Genre --</option>
            <option value="Action">Action</option>
            <option value="Adventure">Adventure</option>
            <option value="Comedy">Comedy</option>
            <option value="Drama">Drama</option>
            <option value="Fantasy">Fantasy</option>
            <option value="Horror">Horror</option>
            <option value="Mystery">Mystery</option>
            <option value="Romance">Romance</option>
            <option value="Science Fiction">Science Fiction</option>
            <option value="Thriller">Thriller</option>
            <option value="Documentary">Documentary</option>
        </select>

        <label for="media_type_id">Media Type:</label>
        <select name="media_type_id" id="media_type_id" required>
            <option value="">-- Select Type --</option>
            <?php foreach ($mediaTypes as $mt): ?>
                <option value="<?php echo (int)$mt['media_type_id']; ?>">
                    <?php echo htmlspecialchars($mt['type_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="content_rating">Content Rating:</label>
        <select name="content_rating" id="content_rating" required>
            <option value="">-- Select Rating --</option>
            <option value="G">G</option>
            <option value="PG">PG</option>
            <option value="PG-13">PG-13</option>
            <option value="R">R</option>
            <option value="NC-17">NC-17</option>
            <option value="E">E (Everyone)</option>
            <option value="E10+">E10+</option>
            <option value="T">T (Teen)</option>
            <option value="M">M (Mature)</option>
            <option value="A">A (Adults Only)</option>
        </select>

        <label for="image_file">Poster / Cover Image (PNG or JPG):</label>
        <input type="file" name="image_file" id="image_file" accept=".png,.jpg,.jpeg" required>

        <button type="submit">Add Media</button>
    </form>
</div>

</body>
</html>
