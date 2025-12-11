<?php
// manage-media.php
// Admin: edit, update, delete media items

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/admin-session.php';

require_admin();

// --------------------------------------------------
// DB CONNECTION
// --------------------------------------------------
$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

$statusMessage = "";
$statusType = ""; // success | error

// --------------------------------------------------
// PROCESS SAVE / DELETE
// --------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action  = $_POST['action'] ?? '';
    $mediaId = isset($_POST['media_id']) ? (int)$_POST['media_id'] : 0;

    if ($mediaId > 0) {

        $imagesDir  = __DIR__ . '/../assets/images/';
        $webImgBase = '/assets/images/';
        $placeholder = 'no-image.png';

        if ($action === 'save') {

            // Clean inputs
            $title       = clean_input($_POST['title'] ?? '');
            $releaseDate = clean_input($_POST['release_date'] ?? '');
            $director    = clean_input($_POST['director'] ?? '');
            $genreId     = (int)($_POST['genre_id'] ?? 0);
            $typeId      = (int)($_POST['media_type_id'] ?? 0);
            $ratingId    = (int)($_POST['content_rating_id'] ?? 0);
            $currentImagePath = clean_input($_POST['current_image_path'] ?? '');

            // -----------------------------
            // IMAGE UPLOAD HANDLING
            // -----------------------------
            $newImagePath = $currentImagePath;

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['image']['tmp_name'];
                $orig = $_FILES['image']['name'];
                $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg','jpeg','png'])) {
                    $fileName = $mediaId . '.' . $ext;
                    $target = $imagesDir . $fileName;

                    if (move_uploaded_file($tmp, $target)) {
                        $newImagePath = $fileName;
                    }
                }
            }

            // -----------------------------
            // UPDATE MEDIA ROW
            // -----------------------------
            try {
                $stmt = $pdo->prepare("
                    UPDATE media
                    SET
                        title             = :title,
                        release_date      = :release_date,
                        director          = :director,
                        genre_id          = :genre_id,
                        media_type_id     = :type_id,
                        content_rating_id = :rating_id,
                        image_path        = :image_path
                    WHERE media_id       = :media_id
                ");

                $stmt->execute([
                    ':title'        => $title,
                    ':release_date' => $releaseDate ?: null,
                    ':director'     => $director ?: null,
                    ':genre_id'     => $genreId ?: null,
                    ':type_id'      => $typeId ?: null,
                    ':rating_id'    => $ratingId ?: null,
                    ':image_path'   => $newImagePath ?: null,
                    ':media_id'     => $mediaId
                ]);

                $statusMessage = "Media ID {$mediaId} saved successfully.";
                $statusType = "success";
            } catch (Exception $e) {
                $statusMessage = "Error saving media ID {$mediaId}: " . $e->getMessage();
                $statusType = "error";
            }

        } elseif ($action === 'delete') {

            $currentImagePath = clean_input($_POST['current_image_path'] ?? '');

            try {
                // delete image file
                if ($currentImagePath) {
                    $fullPath = $imagesDir . $currentImagePath;
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }

                $stmt = $pdo->prepare("DELETE FROM media WHERE media_id = :media_id");
                $stmt->execute([':media_id' => $mediaId]);

                $statusMessage = "Media ID {$mediaId} deleted.";
                $statusType = "success";
            } catch (Exception $e) {
                $statusMessage = "Error deleting media ID {$mediaId}: " . $e->getMessage();
                $statusType = "error";
            }
        }
    }
}

// --------------------------------------------------
// LOAD DROPDOWNS (types, genres, ratings)
// --------------------------------------------------
$mediaTypes = $pdo->query("SELECT media_type_id, type_name FROM media_types ORDER BY type_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$genres     = $pdo->query("SELECT genre_id, genre_name FROM genres ORDER BY genre_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$ratings    = $pdo->query("SELECT rating_id, rating_code FROM content_ratings ORDER BY rating_code ASC")->fetchAll(PDO::FETCH_ASSOC);

// --------------------------------------------------
// LOAD MEDIA LIST
// --------------------------------------------------
$sql = "
    SELECT 
        m.media_id,
        m.title,
        m.release_date,
        m.director,
        m.genre_id,
        m.media_type_id,
        m.content_rating_id,
        m.image_path,
        g.genre_name,
        mt.type_name,
        cr.rating_code,
        COALESCE(mr.average_rating,0) AS avg_rating,
        COALESCE(mr.total_ratings,0) AS total_ratings
    FROM media m
    LEFT JOIN genres g           ON m.genre_id = g.genre_id
    LEFT JOIN media_types mt     ON m.media_type_id = mt.media_type_id
    LEFT JOIN content_ratings cr ON m.content_rating_id = cr.rating_id
    LEFT JOIN media_ratings mr   ON m.media_id = mr.media_id
    ORDER BY m.media_id ASC
";
$mediaList = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// --------------------------------------------------
// IMAGE HELPER
// --------------------------------------------------
function mediaImage(array $row): string {
    $mediaId = (int)$row['media_id'];
    $image = $row['image_path'] ?? '';

    $dir = __DIR__ . '/../assets/images/';
    $web = '/assets/images/';
    $placeholder = 'no-image.png';

    if ($image && file_exists($dir . $image)) return $web . $image;
    if (file_exists($dir . "$mediaId.jpg")) return $web . "$mediaId.jpg";
    if (file_exists($dir . "$mediaId.png")) return $web . "$mediaId.png";
    return $web . $placeholder;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Media - Admin</title>
<link rel="stylesheet" href="/assets/css/admin-style.css">
<link rel="stylesheet" href="/assets/css/admin-manage-media.css">
</head>

<body>

<?php include __DIR__ . '/admin-nav.php'; ?>

<main class="admin-content">

    <h2 class="admin-title">MANAGE MEDIA</h2>
    <p class="admin-subtitle">Edit or remove any media entry below.</p>

    <section class="admin-panel">

        <?php if ($statusMessage): ?>
            <p class="<?= $statusType === 'success' ? 'status-success' : 'status-error' ?>">
                <?= htmlspecialchars($statusMessage); ?>
            </p>
        <?php endif; ?>

        <?php if (empty($mediaList)): ?>
            <p>No media found.</p>
        <?php else: ?>

<!-- ⭐⭐ NEW RESPONSIVE WRAPPER ⭐⭐ -->
<div class="responsive-table-container">

<table class="admin-table manage-media-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Upload New</th>
            <th>Title</th>
            <th>Release Date</th>
            <th>Director / Creator</th>
            <th>Genre</th>
            <th>Type</th>
            <th>Rating</th>
            <th>Avg Rating</th>
            <th>Total</th>
            <th>Save</th>
            <th>Delete</th>
        </tr>
    </thead>

    <tbody>
<?php foreach ($mediaList as $row): ?>
    <?php $img = mediaImage($row); ?>
    <tr>
        <form method="post" enctype="multipart/form-data">

        <td data-label="ID">
            <?= $row['media_id'] ?>
            <input type="hidden" name="media_id" value="<?= $row['media_id'] ?>">
            <input type="hidden" name="current_image_path" value="<?= htmlspecialchars($row['image_path'] ?? '') ?>">
        </td>

        <td data-label="Image">
            <img src="<?= $img ?>" alt="">
        </td>

        <td data-label="Upload New">
            <input type="file" name="image" accept=".jpg,.jpeg,.png">
        </td>

        <td data-label="Title">
            <input type="text" name="title" value="<?= htmlspecialchars($row['title']) ?>">
        </td>

        <td data-label="Release Date">
            <input type="date" name="release_date" value="<?= htmlspecialchars($row['release_date']) ?>">
        </td>

        <td data-label="Director/Creator">
            <input type="text" name="director" value="<?= htmlspecialchars($row['director']) ?>">
        </td>

        <td data-label="Genre">
            <select name="genre_id">
                <option value="">-- Genre --</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?= $g['genre_id'] ?>" 
                        <?= $g['genre_id'] == $row['genre_id'] ? "selected" : "" ?>>
                        <?= htmlspecialchars($g['genre_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <td data-label="Type">
            <select name="media_type_id">
                <option value="">-- Type --</option>
                <?php foreach ($mediaTypes as $t): ?>
                    <option value="<?= $t['media_type_id'] ?>" 
                        <?= $t['media_type_id'] == $row['media_type_id'] ? "selected" : "" ?>>
                        <?= htmlspecialchars($t['type_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <td data-label="Rating">
            <select name="content_rating_id">
                <option value="">-- Rating --</option>
                <?php foreach ($ratings as $r): ?>
                    <option value="<?= $r['rating_id'] ?>" 
                        <?= $r['rating_id'] == $row['content_rating_id'] ? "selected" : "" ?>>
                        <?= htmlspecialchars($r['rating_code']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>

        <td data-label="Avg Rating">
            <?= number_format($row['avg_rating'], 2) ?>
        </td>

        <td data-label="Total Ratings">
            <?= $row['total_ratings'] ?>
        </td>

        <td data-label="Save">
            <button type="submit" name="action" value="save" class="table-action-btn">SAVE</button>
        </td>

        <td data-label="Delete">
            <button type="submit" name="action" value="delete" class="table-action-btn table-delete-btn"
                onclick="return confirm('Delete this media item?');">
                DELETE
            </button>
        </td>

        </form>
    </tr>
<?php endforeach; ?>
</tbody>

</table>

</div><!-- end responsive wrapper -->

        <?php endif; ?>

    </section>
</main>

<footer class="admin-footer">
    © <?= date('Y'); ?> Star Media Review — Admin Panel
</footer>

</body>
</html>
