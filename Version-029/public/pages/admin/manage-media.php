<?php
// ----------------------------------------------------------
// manage-media.php  (Admin - Manage Existing Media)
// Normalized DB: uses genre_id, media_type_id, content_rating_id
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

// Handle POST actions: update_media / delete_media
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_media') {
        $mediaId          = isset($_POST['media_id']) ? (int)$_POST['media_id'] : 0;
        $title            = trim($_POST['title'] ?? '');
        $releaseDateInput = trim($_POST['release_date'] ?? '');
        $director         = trim($_POST['director'] ?? '');
        $genreId          = isset($_POST['genre_id']) ? (int)$_POST['genre_id'] : null;
        $mediaTypeId      = isset($_POST['media_type_id']) ? (int)$_POST['media_type_id'] : null;
        $ratingId         = isset($_POST['content_rating_id']) ? (int)$_POST['content_rating_id'] : null;

        if ($mediaId <= 0 || $title === '') {
            $error = "Media ID and Title are required.";
        } else {
            try {
                // Fetch existing media row for current image_path
                $stmt = $pdo->prepare("SELECT image_path FROM media WHERE media_id = ?");
                $stmt->execute([$mediaId]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$existing) {
                    $error = "Media not found for update.";
                } else {
                    $imagePath = $existing['image_path'] ?: ($mediaId . '.jpg');

                    // Handle optional image upload
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $imagesDir = realpath(__DIR__ . '/../../assets/images');
                        if ($imagesDir) {
                            $targetPath = $imagesDir . DIRECTORY_SEPARATOR . $mediaId . '.jpg';

                            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                            if ($ext === 'png') {
                                // Convert PNG → JPG
                                try {
                                    $png = imagecreatefrompng($_FILES['image']['tmp_name']);
                                    if ($png !== false) {
                                        $bg = imagecreatetruecolor(imagesx($png), imagesy($png));
                                        $white = imagecolorallocate($bg, 255, 255, 255);
                                        imagefilledrectangle($bg, 0, 0, imagesx($png), imagesy($png), $white);
                                        imagecopy($bg, $png, 0, 0, 0, 0, imagesx($png), imagesy($png));
                                        imagejpeg($bg, $targetPath, 90);
                                        imagedestroy($png);
                                        imagedestroy($bg);
                                        $imagePath = $mediaId . '.jpg';
                                    }
                                } catch (Throwable $e) {
                                    ErrorLogger::log("Manage Media - PNG→JPG convert failed: " . $e->getMessage());
                                    $error = "Image conversion failed, but other data may still be updated.";
                                }
                            } else {
                                if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                                    $error = "Failed to save uploaded image, but other data may still be updated.";
                                } else {
                                    $imagePath = $mediaId . '.jpg';
                                }
                            }
                        } else {
                            $error = "Images folder not found. Please verify /assets/images/ exists.";
                        }
                    }

                    // Normalize release date
                    $releaseDate = $releaseDateInput !== '' ? $releaseDateInput : null;

                    $updateSql = "
                        UPDATE media
                        SET title = :title,
                            release_date = :release_date,
                            director = :director,
                            genre_id = :genre_id,
                            media_type_id = :media_type_id,
                            content_rating_id = :content_rating_id,
                            image_path = :image_path
                        WHERE media_id = :media_id
                    ";

                    $updateStmt = $pdo->prepare($updateSql);
                    $updateStmt->execute([
                        ':title'             => $title,
                        ':release_date'      => $releaseDate,
                        ':director'          => $director,
                        ':genre_id'          => $genreId ?: null,
                        ':media_type_id'     => $mediaTypeId ?: null,
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
                $error = "An error occurred while updating media. Check error log.";
            }
        }
    } elseif ($action === 'delete_media') {
        $mediaId = isset($_POST['media_id']) ? (int)$_POST['media_id'] : 0;

        if ($mediaId <= 0) {
            $error = "Invalid media ID for deletion.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT image_path FROM media WHERE media_id = ?");
                $stmt->execute([$mediaId]);
                $media = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($media) {
                    $imagesDir = realpath(__DIR__ . '/../../assets/images');
                    if ($imagesDir) {
                        $imageFile = $imagesDir . DIRECTORY_SEPARATOR . $media['image_path'];
                        if (is_file($imageFile)) {
                            @unlink($imageFile);
                        }
                    }

                    $deleteStmt = $pdo->prepare("DELETE FROM media WHERE media_id = ?");
                    $deleteStmt->execute([$mediaId]);

                    $message = "Media ID {$mediaId} deleted successfully.";
                } else {
                    $error = "Media not found for deletion.";
                }
            } catch (Throwable $e) {
                ErrorLogger::log("Manage Media - Delete Error: " . $e->getMessage());
                $error = "An error occurred while deleting media. Check error log.";
            }
        }
    }
}

// Fetch reference data + list of media + optional 'edit' row
$genres    = [];
$types     = [];
$ratings   = [];
$allMedia  = [];
$editMedia = null;
$editId    = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;

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

        if ($editId > 0) {
            $stmtEdit = $pdo->prepare("SELECT * FROM media WHERE media_id = ?");
            $stmtEdit->execute([$editId]);
            $editMedia = $stmtEdit->fetch(PDO::FETCH_ASSOC);
            if (!$editMedia) {
                $error = "Media ID {$editId} not found.";
                $editId = 0;
            }
        }
    } catch (Throwable $e) {
        ErrorLogger::log("Manage Media - Load Data Error: " . $e->getMessage());
        $error = "Failed to load media data. Check error log.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Media</title>
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
        <a href="admin-logout.php">Logout</a>
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
        <h2>All Media</h2>
        <p>Select a media item to edit or delete.</p>
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
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($allMedia): ?>
                <?php foreach ($allMedia as $row): ?>
                    <tr>
                        <td><?php echo (int)$row['media_id']; ?></td>
                        <td>
                            <?php if (!empty($row['image_path'])): ?>
                                <img src="../../assets/images/<?php echo htmlspecialchars($row['image_path']); ?>"
                                     alt=""
                                     style="max-width:60px; max-height:60px;">
                            <?php else: ?>
                                <span>No image</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['release_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['director']); ?></td>
                        <td><?php echo htmlspecialchars($row['genre_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['type_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['rating_code'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['average_rating']); ?></td>
                        <td><?php echo htmlspecialchars($row['total_ratings']); ?></td>
                        <td>
                            <a href="manage-media.php?edit=<?php echo (int)$row['media_id']; ?>">Edit</a>
                            |
                            <form action="manage-media.php" method="post" style="display:inline;"
                                  onsubmit="return confirm('Delete this media item? This will also remove its ratings and user reviews.');">
                                <input type="hidden" name="action" value="delete_media">
                                <input type="hidden" name="media_id" value="<?php echo (int)$row['media_id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="11">No media found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

    <section style="margin-top:30px;">
        <h2><?php echo $editMedia ? 'Edit Media ID ' . (int)$editMedia['media_id'] : 'Edit Media'; ?></h2>
        <?php if ($editMedia): ?>
            <form action="manage-media.php?edit=<?php echo (int)$editMedia['media_id']; ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_media">
                <input type="hidden" name="media_id" value="<?php echo (int)$editMedia['media_id']; ?>">

                <div>
                    <label for="title">Title (Required):</label><br>
                    <input type="text" id="title" name="title"
                           value="<?php echo htmlspecialchars($editMedia['title']); ?>" required>
                </div>

                <div>
                    <label for="release_date">Release Date:</label><br>
                    <input type="date" id="release_date" name="release_date"
                           value="<?php echo htmlspecialchars($editMedia['release_date']); ?>">
                </div>

                <div>
                    <label for="director">Director / Creator / Author:</label><br>
                    <input type="text" id="director" name="director"
                           value="<?php echo htmlspecialchars($editMedia['director']); ?>">
                </div>

                <div>
                    <label for="genre_id">Genre:</label><br>
                    <select id="genre_id" name="genre_id">
                        <option value="">-- Select Genre --</option>
                        <?php foreach ($genres as $g): ?>
                            <option value="<?php echo (int)$g['genre_id']; ?>"
                                <?php echo ($editMedia['genre_id'] == $g['genre_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($g['genre_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="media_type_id">Media Type:</label><br>
                    <select id="media_type_id" name="media_type_id">
                        <option value="">-- Select Type --</option>
                        <?php foreach ($types as $t): ?>
                            <option value="<?php echo (int)$t['media_type_id']; ?>"
                                <?php echo ($editMedia['media_type_id'] == $t['media_type_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t['type_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="content_rating_id">Content Rating:</label><br>
                    <select id="content_rating_id" name="content_rating_id">
                        <option value="">-- Select Rating --</option>
                        <?php foreach ($ratings as $r): ?>
                            <option value="<?php echo (int)$r['rating_id']; ?>"
                                <?php echo ($editMedia['content_rating_id'] == $r['rating_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($r['rating_code']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <p>Current Image: <?php echo htmlspecialchars($editMedia['image_path']); ?></p>
                    <label for="image">Upload New Image (optional):</label><br>
                    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png">
                    <p>Uploaded image will be saved as <strong><?php echo (int)$editMedia['media_id']; ?>.jpg</strong> in <code>assets/images/</code>.</p>
                </div>

                <div style="margin-top:15px;">
                    <button type="submit">Save Changes</button>
                    <a href="manage-media.php" style="margin-left:10px;">Cancel</a>
                </div>
            </form>
        <?php else: ?>
            <p>Select a media item in the table above and click <strong>Edit</strong> to modify it.</p>
        <?php endif; ?>
    </section>
</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Star Media Review - Admin Panel</p>
</footer>
</body>
</html>
