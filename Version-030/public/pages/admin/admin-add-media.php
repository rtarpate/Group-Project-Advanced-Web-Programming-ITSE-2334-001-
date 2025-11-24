<?php
//------------------------------------------------------
// admin-add-media.php (FULLY UPDATED FOR NORMALIZED DB)
//------------------------------------------------------

session_start();
require_once __DIR__ . "/../admin-session.php";
require_admin_login();

require_once __DIR__ . "/../includes/DatabaseConnector.php";
require_once __DIR__ . "/../logs/ErrorLogger.php";


// Connect to DB
try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    ErrorLogger::log("DB connection failed in admin-add-media: " . $e->getMessage());
    die("Database connection failed.");
}

$message = "";
$errorMessage = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title          = trim($_POST["title"] ?? "");
    $director       = trim($_POST["director"] ?? "");
    $release_date   = trim($_POST["release_date"] ?? "");
    $genre_id       = $_POST["genre_id"] ?? "";
    $media_type_id  = $_POST["media_type_id"] ?? "";
    $content_rating_id = $_POST["content_rating_id"] ?? "";
    $image_path     = null;

    // Basic validation
    if ($title === "" || $media_type_id === "" || $genre_id === "" || $content_rating_id === "") {
        $errorMessage = "Title, Media Type, Genre, and Content Rating are required.";
    } else {

        // Handle file upload if provided
        if (!empty($_FILES["image"]["name"])) {
            $uploadDir = __DIR__ . "/../assets/images/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileTmp  = $_FILES["image"]["tmp_name"];
            $fileName = basename($_FILES["image"]["name"]);
            $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowed = ["jpg", "jpeg", "png"];
            if (!in_array($fileExt, $allowed)) {
                $errorMessage = "Only JPG, JPEG, and PNG files are allowed for images.";
            } else {
                // We will initially store the file with a temporary name then rename after insert
                $tempName = uniqid("media_", true) . "." . $fileExt;
                $targetPath = $uploadDir . $tempName;

                if (!move_uploaded_file($fileTmp, $targetPath)) {
                    $errorMessage = "Failed to upload image file.";
                } else {
                    $image_path = $tempName;  // temporarily store this, we may rename
                }
            }
        }

        if ($errorMessage === "") {
            try {
                // Insert into media table (without final image_path name based on ID yet)
                $sql = "
                    INSERT INTO media (title, release_date, director, genre_id, media_type_id, content_rating_id, image_path)
                    VALUES (:title, :release_date, :director, :genre_id, :media_type_id, :content_rating_id, :image_path)
                ";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ":title"            => $title,
                    ":release_date"     => $release_date !== "" ? $release_date : null,
                    ":director"         => $director !== "" ? $director : null,
                    ":genre_id"         => $genre_id,
                    ":media_type_id"    => $media_type_id,
                    ":content_rating_id"=> $content_rating_id,
                    ":image_path"       => $image_path  // temporary or null
                ]);

                $newMediaId = (int)$pdo->lastInsertId();

                // If we uploaded an image, rename it to match the media_id
                if ($image_path !== null) {
                    $uploadDir  = __DIR__ . "/../assets/images/";
                    $oldPath    = $uploadDir . $image_path;
                    $ext        = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));
                    $newName    = $newMediaId . "." . $ext;
                    $newPath    = $uploadDir . $newName;

                    if (file_exists($oldPath)) {
                        if (rename($oldPath, $newPath)) {
                            // Update the media row with the final image name
                            $upd = $pdo->prepare("UPDATE media SET image_path = :image_path WHERE media_id = :id");
                            $upd->execute([
                                ":image_path" => $newName,
                                ":id"         => $newMediaId
                            ]);
                        } else {
                            ErrorLogger::log("Failed to rename uploaded image for media_id {$newMediaId}");
                        }
                    } else {
                        ErrorLogger::log("Temp image file not found for media_id {$newMediaId}");
                    }
                }

                // Also create initial row in media_ratings with 0/0 if it doesn't exist
                $ratingsStmt = $pdo->prepare("
                    INSERT INTO media_ratings (media_id, average_rating, total_ratings)
                    VALUES (:id, 0, 0)
                    ON DUPLICATE KEY UPDATE average_rating = VALUES(average_rating), total_ratings = VALUES(total_ratings)
                ");
                $ratingsStmt->execute([":id" => $newMediaId]);

                $message = "Media '{$title}' added successfully with ID {$newMediaId}.";
            } catch (Throwable $e) {
                ErrorLogger::log("Error inserting media in admin-add-media: " . $e->getMessage());
                $errorMessage = "An error occurred while adding media. Please check logs.";
            }
        }
    }
}

// Fetch dropdown data
function fetchDropdownOptions(PDO $pdo, string $table, string $idField, string $nameField): array
{
    $sql = "SELECT {$idField} AS id, {$nameField} AS name FROM {$table} ORDER BY name ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$genres          = fetchDropdownOptions($pdo, "genres", "genre_id", "genre_name");
$mediaTypes      = fetchDropdownOptions($pdo, "media_types", "media_type_id", "type_name");
$contentRatings  = fetchDropdownOptions($pdo, "content_ratings", "rating_id", "rating_code");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Add New Media</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<header>
    <h1>Admin Panel - Add New Media</h1>
    <nav>
        <ul>
            <li><a href="admin-dashboard.php">Dashboard</a></li>
            <li><a href="admin-add-media.php" class="active">Add Media</a></li>
            <li><a href="admin-manage-requests.php">Manage Requests</a></li>
            <li><a href="admin-manage-reviews.php">Manage Reviews</a></li>
            <li><a href="admin-logout.php">Logout</a></li>
        </ul>
    </nav>
</header>

<main>

    <?php if ($message !== ""): ?>
        <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($errorMessage !== ""): ?>
        <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form action="admin-add-media.php" method="post" enctype="multipart/form-data" class="admin-form">

        <label for="title">Title<span class="required">*</span>:</label>
        <input type="text" name="title" id="title" required>

        <label for="director">Director / Creator:</label>
        <input type="text" name="director" id="director">

        <label for="release_date">Release Date:</label>
        <input type="date" name="release_date" id="release_date">

        <label for="genre_id">Genre<span class="required">*</span>:</label>
        <select name="genre_id" id="genre_id" required>
            <option value="">-- Select Genre --</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?php echo $g['id']; ?>">
                    <?php echo htmlspecialchars($g['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="media_type_id">Media Type<span class="required">*</span>:</label>
        <select name="media_type_id" id="media_type_id" required>
            <option value="">-- Select Media Type --</option>
            <?php foreach ($mediaTypes as $mt): ?>
                <option value="<?php echo $mt['id']; ?>">
                    <?php echo htmlspecialchars($mt['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="content_rating_id">Content Rating<span class="required">*</span>:</label>
        <select name="content_rating_id" id="content_rating_id" required>
            <option value="">-- Select Rating --</option>
            <?php foreach ($contentRatings as $cr): ?>
                <option value="<?php echo $cr['id']; ?>">
                    <?php echo htmlspecialchars($cr['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="image">Cover Image (optional):</label>
        <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png">

        <button type="submit">Add Media</button>

    </form>

</main>

<footer>
    <p>&copy; <?php echo date("Y"); ?> Star Media Review - Admin Panel</p>
</footer>

</body>
</html>
