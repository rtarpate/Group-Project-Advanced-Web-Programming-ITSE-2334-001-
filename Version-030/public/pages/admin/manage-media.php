<?php
// ----------------------------------------------------------
// manage-media.php  (Admin - Inline Manage Media)
// ----------------------------------------------------------

session_start();
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../../assets/logs/ErrorLogger.php';

$message = '';
$error   = '';

try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    ErrorLogger::log("Manage Media - DB Connection Failed: " . $e->getMessage());
    $error = "Database connection failed. Please check the error log.";
    $pdo = null;
}

// ---------------------------------------------------
// Handle POST actions: update_media / delete_media
// ---------------------------------------------------
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action  = $_POST['action'] ?? '';
    $mediaId = isset($_POST['media_id']) ? (int)$_POST['media_id'] : 0;

    if ($mediaId <= 0) {
        $error = "Invalid media ID.";
    } else {
        if ($action === 'update_media') {

            $title       = trim($_POST['title'] ?? '');
            $releaseDate = trim($_POST['release_date'] ?? '');
            $director    = trim($_POST['director'] ?? '');
            $genreId     = isset($_POST['genre_id']) ? (int)$_POST['genre_id'] : null;
            $typeId      = isset($_POST['media_type_id']) ? (int)$_POST['media_type_id'] : null;
            $ratingId    = isset($_POST['content_rating_id']) ? (int)$_POST['content_rating_id'] : null;

            if ($title === '') {
                $error = "Title is required.";
            } else {
                try {
                    // Get current image_path
                    $stmt = $pdo->prepare("SELECT image_path FROM media WHERE media_id = :id");
                    $stmt->execute([':id' => $mediaId]);
                    $current = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$current) {
                        $error = "Media item not found.";
                    } else {
                        $imagePath = $current['image_path'] ?: ($mediaId . '.jpg');

                        // Handle image upload (optional)
                        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                            $imagesDir = realpath(__DIR__ . '/../../assets/images');
                            if ($imagesDir) {
                                $target = $imagesDir . DIRECTORY_SEPARATOR . $mediaId . '.jpg';

                                $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                                if ($ext === 'png') {
                                    // Convert PNG → JPG with white background
                                    try {
                                        $png = imagecreatefrompng($_FILES['image']['tmp_name']);
                                        if ($png !== false) {
                                            $bg = imagecreatetruecolor(imagesx($png), imagesy($png));
                                            $white = imagecolorallocate($bg, 255, 255, 255);
                                            imagefilledrectangle($bg, 0, 0, imagesx($png), imagesy($png), $white);
                                            imagecopy($bg, $png, 0, 0, 0, 0, imagesx($png), imagesy($png));
                                            imagejpeg($bg, $target, 90);
                                            imagedestroy($png);
                                            imagedestroy($bg);
                                            $imagePath = $mediaId . '.jpg';
                                        }
                                    } catch (Throwable $ie) {
                                        ErrorLogger::log("Manage Media - PNG→JPG convert failed: " . $ie->getMessage());
                                        $error = "Image conversion failed, but other data may still update.";
                                    }
                                } else {
                                    if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                                        $error = "Failed to save uploaded image, but other data may still update.";
                                    } else {
                                        $imagePath = $mediaId . '.jpg';
                                    }
                                }
                            } else {
                                $error = "Images folder not found at /assets/images. Please verify it exists.";
                            }
                        }

                        // Normalize release date (allow empty)
                        $releaseDateValue = ($releaseDate !== '') ? $releaseDate : null;

                        // Update media row
                        $updateSql = "
                            UPDATE media
                            SET
                                title             = :title,
                                release_date      = :release_date,
                                director          = :director,
                                genre_id          = :genre_id,
                                media_type_id     = :media_type_id,
                                content_rating_id = :content_rating_id,
                                image_path        = :image_path
                            WHERE media_id = :media_id
                        ";

                        $updateStmt = $pdo->prepare($updateSql);
                        $updateStmt->execute([
                            ':title'             => $title,
                            ':release_date'      => $releaseDateValue,
                            ':director'          => $director !== '' ? $director : null,
                            ':genre_id'          => $genreId ?: null,
                            ':media_type_id'     => $typeId ?: null,
                            ':content_rating_id' => $ratingId ?: null,
                            ':image_path'        => $imagePath,
                            ':media_id'          => $mediaId
                        ]);

                        if (!$error) {
                            $message = "Media ID {$mediaId} updated successfully.";
                        }
                    }
                } catch (Throwable $e) {
                    ErrorLogger::log("Manage Media - Update Error: " . $e->getMessage());
                    $error = "An error occurred while updating the media item. Check the error log.";
                }
            }

        } elseif ($action === 'delete_media') {

            try {
                // Delete image file if exists
                $stmt = $pdo->prepare("SELECT image_path FROM media WHERE media_id = :id");
                $stmt->execute([':id' => $mediaId]);
                $media = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($media) {
                    $imagesDir = realpath(__DIR__ . '/../../assets/images');
                    if ($imagesDir && !empty($media['image_path'])) {
                        $file = $imagesDir . DIRECTORY_SEPARATOR . $media['image_path'];
                        if (is_file($file)) {
                            @unlink($file);
                        }
                    }

                    // Delete media (and rely on FK constraints/cascades if set)
                    $delStmt = $pdo->prepare("DELETE FROM media WHERE media_id = :id");
                    $delStmt->execute([':id' => $mediaId]);

                    $message = "Media ID {$mediaId} deleted successfully.";
                } else {
                    $error = "Media item not found for deletion.";
                }
            } catch (Throwable $e) {
                ErrorLogger::log("Manage Media - Delete Error: " . $e->getMessage());
                $error = "An error occurred while deleting the media item. Check the error log.";
            }
        }
    }
}

// ---------------------------------------------------
// Load genres, media types, ratings, and all media
// ---------------------------------------------------
$genres  = [];
$types   = [];
$ratings = [];
$allMedia = [];

if ($pdo) {
    try {
        $genres = $pdo
            ->query("SELECT genre_id, genre_name FROM genres ORDER BY genre_name")
            ->fetchAll(PDO::FETCH_ASSOC);

        $types = $pdo
            ->query("SELECT media_type_id, type_name FROM media_types ORDER BY type_name")
            ->fetchAll(PDO::FETCH_ASSOC);

        $ratings = $pdo
            ->query("SELECT rating_id, rating_code FROM content_ratings ORDER BY rating_code")
            ->fetchAll(PDO::FETCH_ASSOC);

        $sql = "
            SELECT 
                m.media_id,
                m.title,
                m.release_date,
                m.director,
                m.image_path,
                m.genre_id,
                m.media_type_id,
                m.content_rating_id,
                g.genre_name,
                mt.type_name,
                cr.rating_code,
                IFNULL(r.average_rating, 0) AS average_rating,
                IFNULL(r.total_ratings, 0)  AS total_ratings
            FROM media m
            LEFT JOIN genres g ON m.genre_id = g.genre_id
            LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
            LEFT JOIN content_ratings cr ON m.content_rating_id = cr.rating_id
            LEFT JOIN media_ratings r ON m.media_id = r.media_id
            ORDER BY m.media_id
        ";
        $allMedia = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    } catch (Throwable $e) {
        ErrorLogger::log("Manage Media - Load Data Error: " . $e->getMessage());
        $error = "Failed to load media or taxonomy data. Check the error log.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Media (Inline)</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<header>
    <h1>Admin - Manage Media</h1>
    <nav>
        <a href="admin-dashboard.php">Dashboard</a>
        <a href="admin-add-media.php">Add Media</a>
        <a href="manage-media.php" class="active">Manage Media</a>
        <a href="manage-taxonomy.php">Manage Genres & Types</a>
        <a href="admin-manage-requests.php">Manage Requests</a>
        <a href="admin-manage-reviews.php">Manage Reviews</a>
        <a href="admin-logout.php">Logout</a>
        <a href="../index.html">View Site</a>
    </nav>
</header>

<main class="content">
    <?php if ($message): ?>
        <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <section>
        <h2>All Media (Inline Editing)</h2>
        <p>Edit fields directly in the table and click <strong>Save</strong> for that row. Use <strong>Delete</strong> to remove an item.</p>

        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Release Date</th>
                <th>Director / Creator</th>
                <th>Genre</th>
                <th>Type</th>
                <th>Content Rating</th>
                <th>Avg Rating</th>
                <th>Total Ratings</th>
                <th>Save</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($allMedia): ?>
                <?php foreach ($allMedia as $row): ?>
                    <tr>
                        <form action="manage-media.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="update_media">
                            <input type="hidden" name="media_id" value="<?php echo (int)$row['media_id']; ?>">

                            <td><?php echo (int)$row['media_id']; ?></td>

                            <td>
                                <?php if (!empty($row['image_path'])): ?>
                                    <img src="../../assets/images/<?php echo htmlspecialchars($row['image_path']); ?>"
                                         alt=""
                                         style="max-width:60px; max-height:60px; display:block; margin-bottom:4px;">
                                <?php else: ?>
                                    <span>No image</span><br>
                                <?php endif; ?>
                                <input type="file" name="image" accept=".jpg,.jpeg,.png">
                            </td>

                            <td>
                                <input type="text" name="title"
                                       value="<?php echo htmlspecialchars($row['title']); ?>"
                                       required>
                            </td>

                            <td>
                                <input type="date" name="release_date"
                                       value="<?php echo htmlspecialchars($row['release_date']); ?>">
                            </td>

                            <td>
                                <input type="text" name="director"
                                       value="<?php echo htmlspecialchars($row['director']); ?>">
                            </td>

                            <td>
                                <select name="genre_id">
                                    <option value="">-- Genre --</option>
                                    <?php foreach ($genres as $g): ?>
                                        <option value="<?php echo (int)$g['genre_id']; ?>"
                                            <?php echo ($row['genre_id'] == $g['genre_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($g['genre_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <select name="media_type_id">
                                    <option value="">-- Type --</option>
                                    <?php foreach ($types as $t): ?>
                                        <option value="<?php echo (int)$t['media_type_id']; ?>"
                                            <?php echo ($row['media_type_id'] == $t['media_type_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($t['type_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <select name="content_rating_id">
                                    <option value="">-- Rating --</option>
                                    <?php foreach ($ratings as $r): ?>
                                        <option value="<?php echo (int)$r['rating_id']; ?>"
                                            <?php echo ($row['content_rating_id'] == $r['rating_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($r['rating_code']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td><?php echo htmlspecialchars($row['average_rating']); ?></td>
                            <td><?php echo htmlspecialchars($row['total_ratings']); ?></td>

                            <td>
                                <button type="submit">Save</button>
                            </td>
                        </form>

                        <td>
                            <form action="manage-media.php" method="post"
                                  onsubmit="return confirm('Delete this media item? This will also remove its ratings and user reviews if cascades are configured.');">
                                <input type="hidden" name="action" value="delete_media">
                                <input type="hidden" name="media_id" value="<?php echo (int)$row['media_id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="12">No media found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Star Media Review - Admin Panel</p>
</footer>
</body>
</html>
