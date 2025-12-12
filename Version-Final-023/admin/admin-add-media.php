<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/admin-session.php';

require_admin();

// -----------------------------------------------------
// Database Connection
// -----------------------------------------------------
$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

// -----------------------------------------------------
// Load Dynamic Dropdown Data
// -----------------------------------------------------
$types   = [];
$genres  = [];
$ratings = [];

try {
    $types   = $pdo->query("SELECT media_type_id, type_name FROM media_types ORDER BY type_name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $genres  = $pdo->query("SELECT genre_id, genre_name FROM genres ORDER BY genre_name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $ratings = $pdo->query("SELECT rating_id, rating_code FROM content_ratings ORDER BY rating_code ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Optional logging later
}

// -----------------------------------------------------
// Form Handling
// -----------------------------------------------------
$success = "";
$error   = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = clean_input($_POST['title'] ?? '');
    $releaseDate = clean_input($_POST['release_date'] ?? '');
    $director    = clean_input($_POST['director'] ?? '');
    $typeId      = clean_input($_POST['media_type_id'] ?? '');
    $genreId     = clean_input($_POST['genre_id'] ?? '');
    $ratingId    = clean_input($_POST['content_rating_id'] ?? '');
    $imagePath   = clean_input($_POST['image_path'] ?? '');

    if ($title === "" || $typeId === "" || $genreId === "" || $ratingId === "") {
        $error = "Title, media type, genre, and content rating are required.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO media (title, release_date, director, media_type_id, genre_id, content_rating_id, image_path)
                VALUES (:title, :release_date, :director, :type_id, :genre_id, :rating_id, :image_path)
            ");

            $stmt->execute([
                ':title'        => $title,
                ':release_date' => $releaseDate ?: null,
                ':director'     => $director ?: null,
                ':type_id'      => $typeId,
                ':genre_id'     => $genreId,
                ':rating_id'    => $ratingId,
                ':image_path'   => $imagePath ?: null
            ]);

            $success = "Media added successfully!";
        } catch (Exception $e) {
            $error = "Error adding media: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Media - Admin</title>
    <link rel="stylesheet" href="/assets/css/admin-style.css">
</head>

<body>

<?php include __DIR__ . '/admin-nav.php'; ?>

<main class="admin-content">

    <h2 class="admin-title">ADD NEW MEDIA</h2>
    <p class="admin-subtitle">Create a new media item for the website database.</p>

    <section class="admin-panel">

        <?php if ($success): ?>
            <p class="admin-success"><?= htmlspecialchars($success) ?></p>
        <?php elseif ($error): ?>
            <p class="admin-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="admin-form">

            <!-- Title -->
            <label for="title">Title *</label>
            <input type="text" name="title" id="title" required>

            <!-- Release Date -->
            <label for="release_date">Release Date</label>
            <input type="date" name="release_date" id="release_date">

            <!-- Director -->
            <label for="director">Director</label>
            <input type="text" name="director" id="director">

            <!-- Media Type -->
            <label for="media_type_id">Media Type *</label>
            <select name="media_type_id" id="media_type_id" required>
                <option value="">Select Type</option>
                <?php if (count($types) > 0): ?>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= $t['media_type_id'] ?>">
                            <?= htmlspecialchars($t['type_name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">(No media types in database)</option>
                <?php endif; ?>
            </select>

            <!-- Genre -->
            <label for="genre_id">Genre *</label>
            <select name="genre_id" id="genre_id" required>
                <option value="">Select Genre</option>
                <?php if (count($genres) > 0): ?>
                    <?php foreach ($genres as $g): ?>
                        <option value="<?= $g['genre_id'] ?>">
                            <?= htmlspecialchars($g['genre_name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">(No genres in database)</option>
                <?php endif; ?>
            </select>

            <!-- Content Rating -->
            <label for="content_rating_id">Content Rating *</label>
            <select name="content_rating_id" id="content_rating_id" required>
                <option value="">Select Rating</option>
                <?php if (count($ratings) > 0): ?>
                    <?php foreach ($ratings as $r): ?>
                        <option value="<?= $r['rating_id'] ?>">
                            <?= htmlspecialchars($r['rating_code']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php else: ?>
                    <option value="">(No ratings in database)</option>
                <?php endif; ?>
            </select>

            <!-- Image Path -->
            <label for="image_path">Image File Name (e.g., 23.jpg)</label>
            <input type="text" name="image_path" id="image_path" placeholder="23.jpg">

            <button type="submit" class="admin-submit">Add Media</button>

        </form>
    </section>

</main>

<footer class="admin-footer">
    © <?= date('Y'); ?> Star Media Review — Admin Panel
</footer>

</body>
</html>
