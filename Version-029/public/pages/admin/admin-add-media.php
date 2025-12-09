<?php
//------------------------------------------------------
// admin-add-media.php (FULLY UPDATED FOR NORMALIZED DB)
//------------------------------------------------------

session_start();
require_once __DIR__ . "/admin-session.php";
require_admin_login();

require_once __DIR__ . "/../includes/DatabaseConnector.php";
require_once __DIR__ . "/../includes/ErrorLogger.php";

// Connect to DB
try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    ErrorLogger::log("DB Connection Failed: " . $e->getMessage());
    die("Database connection failed.");
}

$message = "";
$error = "";

// ---------------------------------------------
// Load genres, media types, and content ratings
// ---------------------------------------------
try {
    $genres = $pdo->query("SELECT genre_id, genre_name FROM genres ORDER BY genre_name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $types = $pdo->query("SELECT media_type_id, type_name FROM media_types ORDER BY type_name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $ratings = $pdo->query("SELECT rating_id, rating_code FROM content_ratings ORDER BY rating_code ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    ErrorLogger::log("Taxonomy Load Error: " . $e->getMessage());
    die("Failed to load taxonomy data.");
}

// ---------------------------------------------
// Handle form submission
// ---------------------------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"] ?? "");
    $release_date = trim($_POST["release_date"] ?? "");
    $director = trim($_POST["director"] ?? "");
    $genre_id = $_POST["genre_id"] ?? null;
    $media_type_id = $_POST["media_type_id"] ?? null;
    $rating_id = $_POST["content_rating_id"] ?? null;

    if ($title === "" || !$genre_id || !$media_type_id || !$rating_id) {
        $error = "Please fill out all required fields.";
    } else {
        try {
            // -------------------------------
            // Insert the media entry FIRST
            // -------------------------------
            $sql = "
                INSERT INTO media
                    (title, release_date, director, genre_id, media_type_id, content_rating_id, image_path)
                VALUES
                    (:title, :release_date, :director, :genre_id, :media_type_id, :rating_id, 'temp.jpg')
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":title" => $title,
                ":release_date" => $release_date !== "" ? $release_date : null,
                ":director" => $director !== "" ? $director : null,
                ":genre_id" => $genre_id,
                ":media_type_id" => $media_type_id,
                ":rating_id" => $rating_id
            ]);

            // ID of newly inserted media
            $media_id = $pdo->lastInsertId();

            // -----------------------------------------
            // Insert into media_ratings with 0/0 rating
            // -----------------------------------------
            $ratingSQL = "
                INSERT INTO media_ratings (media_id, average_rating, total_ratings)
                VALUES (:media_id, 0, 0)
            ";

            $stmt = $pdo->prepare($ratingSQL);
            $stmt->execute([":media_id" => $media_id]);

            // -----------------------------------------
            // Handle image upload
            // -----------------------------------------
            $image_path = $media_id . ".jpg";
            $full_path = realpath(__DIR__ . "/../../assets/images");

            if ($full_path && isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
                $target = $full_path . "/" . $image_path;

                // Convert PNG → JPG internally if needed
                $ext = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));

                if ($ext === "png") {
                    // Load PNG and convert to JPG
                    $png = imagecreatefrompng($_FILES["image"]["tmp_name"]);
                    $bg = imagecreatetruecolor(imagesx($png), imagesy($png));
                    $white = imagecolorallocate($bg, 255, 255, 255);
                    imagefilledrectangle($bg, 0, 0, imagesx($png), imagesy($png), $white);
                    imagecopy($bg, $png, 0, 0, 0, 0, imagesx($png), imagesy($png));
                    imagejpeg($bg, $target, 90);
                    imagedestroy($png);
                    imagedestroy($bg);
                } else {
                    // Save file normally
                    move_uploaded_file($_FILES["image"]["tmp_name"], $target);
                }
            }

            // -----------------------------------------
            // Update media entry with final image path
            // -----------------------------------------
            $updateSQL = "
                UPDATE media
                SET image_path = :img
                WHERE media_id = :id
            ";

            $stmt = $pdo->prepare($updateSQL);
            $stmt->execute([
                ":img" => $image_path,
                ":id" => $media_id
            ]);

            $message = "Media added successfully! (Media ID: {$media_id})";

        } catch (Throwable $e) {
            ErrorLogger::log("Add Media Error: " . $e->getMessage());
            $error = "Failed to add media. Check error log.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Add New Media</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<header>
    <h1>Add New Media</h1>
    <nav>
        <a href="../admin/admin-dashboard.php">Dashboard</a>
        <a href="../admin/manage-media.php">Manage Media</a>
        <a href="../admin/manage-taxonomy.php">Manage Genres & Types</a>
        <a href="../admin/admin-logout.php">Logout</a>
    </nav>
</header>

<main class="content">

<?php if ($message): ?>
    <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form action="admin-add-media.php" method="POST" enctype="multipart/form-data">

    <label>Title (Required)</label>
    <input type="text" name="title" required>

    <label>Release Date</label>
    <input type="date" name="release_date">

    <label>Director / Creator / Author</label>
    <input type="text" name="director">

    <!-- Genre Dropdown -->
    <label>Genre (Required)</label>
    <select name="genre_id" required>
        <option value="">-- Select Genre --</option>
        <?php foreach ($genres as $g): ?>
            <option value="<?php echo $g['genre_id']; ?>">
                <?php echo htmlspecialchars($g['genre_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Media Type Dropdown -->
    <label>Media Type (Required)</label>
    <select name="media_type_id" required>
        <option value="">-- Select Type --</option>
        <?php foreach ($types as $t): ?>
            <option value="<?php echo $t['media_type_id']; ?>">
                <?php echo htmlspecialchars($t['type_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Content Rating Dropdown -->
    <label>Content Rating (Required)</label>
    <select name="content_rating_id" required>
        <option value="">-- Select Rating --</option>
        <?php foreach ($ratings as $r): ?>
            <option value="<?php echo $r['rating_id']; ?>">
                <?php echo htmlspecialchars($r['rating_code']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Upload Image (Optional)</label>
    <input type="file" name="image" accept=".jpg,.jpeg,.png">

    <button type="submit">Add Media</button>

</form>

</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Star Media Review - Admin Panel</p>
</footer>

</body>
</html>
