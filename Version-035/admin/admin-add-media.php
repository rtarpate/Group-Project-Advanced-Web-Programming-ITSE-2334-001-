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

//--------------------------------------
// DEBUGGING MODE (set to false to hide)
//--------------------------------------
define('ADMIN_ADD_MEDIA_DEBUG', false);

function debug_out(string $msg): void
{
    if (ADMIN_ADD_MEDIA_DEBUG) {
        echo '<pre style="background:#222;color:#0f0;padding:6px 8px;margin:4px 0;border:1px solid #555;font-size:12px;">';
        echo '[ADD_MEDIA DEBUG] ' . htmlspecialchars($msg);
        echo '</pre>';
    }
}

//--------------------------------------
// CONNECT TO DATABASE
//--------------------------------------
try {
    $pdo = DatabaseConnector::getConnection();
    debug_out('Database connection successful.');
} catch (Throwable $e) {
    ErrorLogger::log('DB connection failed in admin-add-media: ' . $e->getMessage());
    debug_out('DB connection FAILED: ' . $e->getMessage());
    die('<strong>Database connection error. Check logs.</strong>');
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
    $image_path        = null;

    debug_out('Collected POST values.');

    // Basic validation
    if ($title === '' || $genre_id === '' || $media_type_id === '' || $content_rating_id === '') {
        $errorMessage = 'Title, Genre, Media Type, and Content Rating are required.';
        debug_out('Validation FAILED: missing required fields.');
    } else {
        debug_out('Validation passed.');

        //--------------------------------------
        // IMAGE MUST BE UPLOADED
        //--------------------------------------
        if (empty($_FILES['image']['name'])) {
            $errorMessage = 'Cover Image is required.';
            debug_out('ERROR: No image uploaded.');
        } else {
            $uploadDir = __DIR__ . '/../../assets/images/';

            if (!is_dir($uploadDir)) {
                debug_out('Images directory missing. Creating...');
                if (!mkdir($uploadDir, 0777, true) && !is_dir($uploadDir)) {
                    $errorMessage = 'Could not create images directory.';
                    debug_out('Failed to create images directory at ' . $uploadDir);
                }
            }

            if ($errorMessage === '') {
                $fileTmp  = $_FILES['image']['tmp_name'];
                $fileName = basename($_FILES['image']['name']);
                $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowed = ['jpg', 'jpeg', 'png'];
                if (!in_array($fileExt, $allowed, true)) {
                    $errorMessage = 'Only JPG, JPEG, or PNG allowed.';
                    debug_out('Invalid image extension: ' . $fileExt);
                } else {
                    $tempName   = uniqid('media_', true) . '.' . $fileExt;
                    $targetPath = $uploadDir . $tempName;

                    if (!move_uploaded_file($fileTmp, $targetPath)) {
                        $errorMessage = 'Failed to upload image.';
                        debug_out('move_uploaded_file FAILED.');
                    } else {
                        $image_path = $tempName;
                        debug_out('Image uploaded as temporary file: ' . $tempName);
                    }
                }
            }
        }

        //--------------------------------------
        // INSERT INTO DATABASE
        //--------------------------------------
        if ($errorMessage === '') {
            debug_out('Attempting INSERT into media table...');

            try {
                $sql = "
                    INSERT INTO media (
                        title,
                        release_date,
                        director,
                        genre_id,
                        media_type_id,
                        content_rating_id,
                        image_path
                    ) VALUES (
                        :title,
                        :release_date,
                        :director,
                        :genre_id,
                        :media_type_id,
                        :content_rating_id,
                        :image_path
                    )
                ";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':title'             => $title,
                    ':release_date'      => $release_date !== '' ? $release_date : null,
                    ':director'          => $director !== '' ? $director : null,
                    ':genre_id'          => $genre_id,
                    ':media_type_id'     => $media_type_id,
                    ':content_rating_id' => $content_rating_id,
                    ':image_path'        => $image_path   // temporary or null
                ]);

                $newMediaId = (int) $pdo->lastInsertId();
                debug_out('Media inserted with ID: ' . $newMediaId);

                //--------------------------------------
                // RENAME IMAGE TO {media_id}.ext
                //--------------------------------------
                if ($image_path !== null) {
                    $uploadDir = __DIR__ . '/../../assets/images/';
                    $oldPath   = $uploadDir . $image_path;
                    $ext       = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
                    $newName   = $newMediaId . '.' . $ext;
                    $newPath   = $uploadDir . $newName;

                    if (file_exists($oldPath)) {
                        if (rename($oldPath, $newPath)) {
                            $update = $pdo->prepare("
                                UPDATE media
                                SET image_path = :image_path
                                WHERE media_id = :id
                            ");
                            $update->execute([
                                ':image_path' => $newName,
                                ':id'         => $newMediaId
                            ]);

                            debug_out('Image renamed to ' . $newName . ' and media.image_path updated.');
                        } else {
                            debug_out('Failed to rename uploaded image from ' . $oldPath . ' to ' . $newPath);
                            ErrorLogger::log('Failed to rename uploaded image for media_id ' . $newMediaId);
                        }
                    } else {
                        debug_out('Temporary image file not found at ' . $oldPath);
                        ErrorLogger::log('Temp image not found while renaming for media_id ' . $newMediaId);
                    }
                }

                //--------------------------------------
                // INITIALIZE media_ratings ROW
                //--------------------------------------
                try {
                    $ratingsStmt = $pdo->prepare("
                        INSERT INTO media_ratings (media_id, average_rating, total_ratings)
                        VALUES (:id, 0, 0)
                        ON DUPLICATE KEY UPDATE
                            average_rating = VALUES(average_rating),
                            total_ratings  = VALUES(total_ratings)
                    ");
                    $ratingsStmt->execute([':id' => $newMediaId]);
                    debug_out('media_ratings row initialized for media_id ' . $newMediaId);
                } catch (Throwable $e) {
                    ErrorLogger::log('Error initializing media_ratings for media_id '
                        . $newMediaId . ': ' . $e->getMessage());
                    debug_out('WARNING: media_ratings initialization failed: ' . $e->getMessage());
                }

                $message = "Media '{$title}' added successfully (ID: {$newMediaId}).";

            } catch (Throwable $e) {
                ErrorLogger::log('Error inserting media in admin-add-media: ' . $e->getMessage());
                $errorMessage = 'An error occurred while adding the media. Please check logs.';
                debug_out('INSERT ERROR: ' . $e->getMessage());
            }
        }
    }
}

//--------------------------------------
// FETCH DROPDOWN OPTIONS
//--------------------------------------
function fetchOptions(PDO $pdo, string $table, string $idField, string $nameField): array
{
    $sql  = "SELECT {$idField} AS id, {$nameField} AS name FROM {$table} ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

debug_out('Loading dropdown data...');
$genres         = fetchOptions($pdo, 'genres',         'genre_id',     'genre_name');
$mediaTypes     = fetchOptions($pdo, 'media_types',    'media_type_id','type_name');
$contentRatings = fetchOptions($pdo, 'content_ratings','rating_id',    'rating_code');
debug_out('Dropdowns loaded.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add New Media</title>
    <link rel="stylesheet" href="/groupproject/Version-035/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-035/assets/css/admin-style.css">
</head>
<body class="admin-body">
<div class="admin-wrapper">

    <header class="admin-header">
        <div class="admin-header-inner">
            <h1>Admin - Add New Media</h1>
            <?php include __DIR__ . '/admin-nav.php'; ?>
        </div>
    </header>

    <main class="admin-main">
        <section class="admin-panel">
            <h2>Add Media</h2>

            <?php if ($message !== ''): ?>
                <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
            <?php endif; ?>

            <?php if ($errorMessage !== ''): ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>

            <form action="admin-add-media.php" method="post" enctype="multipart/form-data" class="admin-form">

                <label for="title">Title <span class="required">*</span>:</label>
                <input type="text" name="title" id="title" required>

                <label for="release_date">Release Date:</label>
                <input type="date" name="release_date" id="release_date">

                <label for="director">Director / Creator:</label>
                <input type="text" name="director" id="director">

                <label for="genre_id">Genre <span class="required">*</span>:</label>
                <select name="genre_id" id="genre_id" required>
                    <option value="">-- Select Genre --</option>
                    <?php foreach ($genres as $g): ?>
                        <option value="<?php echo (int)$g['id']; ?>">
                            <?php echo htmlspecialchars($g['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="media_type_id">Media Type <span class="required">*</span>:</label>
                <select name="media_type_id" id="media_type_id" required>
                    <option value="">-- Select Media Type --</option>
                    <?php foreach ($mediaTypes as $t): ?>
                        <option value="<?php echo (int)$t['id']; ?>">
                            <?php echo htmlspecialchars($t['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="content_rating_id">Content Rating <span class="required">*</span>:</label>
                <select name="content_rating_id" id="content_rating_id" required>
                    <option value="">-- Select Rating --</option>
                    <?php foreach ($contentRatings as $r): ?>
                        <option value="<?php echo (int)$r['id']; ?>">
                            <?php echo htmlspecialchars($r['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="image">Cover Image (JPG / PNG) <span class="required">*</span>:</label>
                <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png" required>

                <button type="submit" class="admin-btn">Add Media</button>

            </form>
        </section>
    </main>

    <footer class="admin-footer">
        <p>&copy; <?php echo date('Y'); ?> Star Media Review — Admin Panel</p>
    </footer>
</div>
</body>
</html>
