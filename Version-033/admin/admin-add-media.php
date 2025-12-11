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
require_admin_login(); // uses your existing function

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = clean('media_title', 'string', INPUT_POST);
    $type        = clean('media_type', 'string', INPUT_POST);
    $description = clean('media_description', 'string', INPUT_POST);
    $image       = $_FILES['image'] ?? null;

    if (!$title || !$type || !$description || !$image) {
        $error = "All fields are required.";
    } else {
        $pdo = DatabaseConnector::getConnection();
        if (!$pdo) {
            $error = "Database connection failed.";
        } else {
            try {
                // Handle image upload
                $imageName = null;

                if ($image && $image['error'] === UPLOAD_ERR_OK) {

                    $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
                    if (!in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        $error = "Only JPG and PNG images are allowed.";
                    } else {
                        $newName = uniqid('media_', true) . '.' . $ext;
                        $target  = __DIR__ . '/../assets/images/' . $newName;

                        if (move_uploaded_file($image['tmp_name'], $target)) {
                            $imageName = $newName;
                        } else {
                            $error = "Failed to move uploaded file.";
                        }
                    }
                }

                if (!$error) {
                    $sql = "
                        INSERT INTO media (media_title, media_type, media_description, media_image)
                        VALUES (:title, :type, :description, :image)
                    ";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':title'       => $title,
                        ':type'        => $type,
                        ':description' => $description,
                        ':image'       => $imageName,
                    ]);

                    $success = "Media added successfully!";
                }

            } catch (Throwable $e) {
                ErrorLogger::log("ADMIN ADD MEDIA ERROR: " . $e->getMessage());
                $error = "An error occurred while adding media.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add Media</title>
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/admin-style.css">
</head>
<body class="admin-body">
<div class="admin-wrapper">

    <header class="admin-header">
        <div class="admin-header-inner">
            <h1>Admin - Add Media</h1>
            <?php include __DIR__ . '/admin-nav.php'; ?>
        </div>
    </header>

    <main class="admin-main">
        <section class="admin-panel">
            <?php if ($error): ?>
                <div class="admin-message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="admin-message success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data" class="admin-form">
                <label for="media_title">Title:</label>
                <input type="text" name="media_title" id="media_title" required>

                <label for="media_type">Type:</label>
                <select name="media_type" id="media_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="Movie">Movie</option>
                    <option value="TV Show">TV Show</option>
                    <option value="Video Game">Video Game</option>
                    <option value="Comic Book">Comic Book</option>
                    <option value="Manga">Manga</option>
                    <option value="Novel">Novel</option>
                    <option value="Web Novel">Web Novel</option>
                    <option value="Web Series">Web Series</option>
                    <option value="Audio Book">Audio Book</option>
                </select>

                <label for="media_description">Description:</label>
                <textarea name="media_description" id="media_description" rows="4" required></textarea>

                <label for="image">Image (JPG/PNG):</label>
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
