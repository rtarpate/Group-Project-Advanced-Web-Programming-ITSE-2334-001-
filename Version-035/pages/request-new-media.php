<<<<<<< HEAD
<?php
// -------------------------------------------------------------
// REQUEST NEW MEDIA (Self-processing version with auto-clear)
// -------------------------------------------------------------

require_once "../includes/DatabaseConnector.php"; 
require_once "../includes/helper.php"; // Clean input function

$successMessage = "";
$errorMessage = "";

// Pre-fill values so form retains user input when errors occur
$media_name = "";
$media_type = "";
$media_description = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Clean input
    $media_name        = clean_input($_POST["media_name"] ?? "");
    $media_type        = clean_input($_POST["media_type"] ?? "");
    $media_description = clean_input($_POST["media_description"] ?? "");

    // Validate required entries
    if (empty($media_name) || empty($media_type) || empty($media_description)) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        try {
            $db = DatabaseConnector::getConnection();

            $sql = "INSERT INTO newmediarequest 
                    (media_name, media_type, media_description, request_date)
                    VALUES 
                    (:media_name, :media_type, :media_description, NOW())";

            $stmt = $db->prepare($sql);

            $stmt->bindParam(":media_name", $media_name);
            $stmt->bindParam(":media_type", $media_type);
            $stmt->bindParam(":media_description", $media_description);

            if ($stmt->execute()) {
                $successMessage = "Your media request has been submitted!";

                // AUTO-CLEAR FORM AFTER SUCCESS
                $media_name = "";
                $media_type = "";
                $media_description = "";
            } else {
                $errorMessage = "An unexpected error occurred. Please try again.";
            }

        } catch (Exception $e) {
            $errorMessage = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Request New Media</title>

    <!-- New Form-Specific CSS -->
    <link rel="stylesheet" href="../assets/css/RequestMedia.css">

    <style>
        /* Fade-out animation for success & error message */
        .fade-out {
            animation: fadeOut 3s forwards ease-in-out;
        }
        @keyframes fadeOut {
            0% { opacity: 1; }
            70% { opacity: 1; }
            100% { opacity: 0; display: none; }
        }
    </style>

    <script>
        // Auto-fade messages on page load
        document.addEventListener("DOMContentLoaded", () => {
            const success = document.querySelector(".success-message");
            const error = document.querySelector(".error-message");

            if (success) success.classList.add("fade-out");
            if (error) error.classList.add("fade-out");
        });
    </script>

</head>

<body>

<?php include "../includes/header.php"; ?>

<div class="request-container">

    <h1 class="request-title">Request New Media</h1>
    <p class="request-subtitle">
        Don’t see something you'd like to review? Suggest a new movie, show, game, manga, anime, or other media here.
    </p>

    <!-- Messages -->
    <?php if (!empty($successMessage)): ?>
        <div class="success-message"><?= $successMessage ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="error-message"><?= $errorMessage ?></div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" action="" class="request-form">

        <label for="media_name">Media Name *</label>
        <input type="text" id="media_name" name="media_name"
            value="<?= htmlspecialchars($media_name) ?>" required>

        <label for="media_type">Type *</label>
        <select id="media_type" name="media_type" required>
            <option value="" <?= $media_type === "" ? "selected" : "" ?>>Select Type</option>
            <option value="Movie"       <?= $media_type === "Movie" ? "selected" : "" ?>>Movie</option>
            <option value="TV Show"     <?= $media_type === "TV Show" ? "selected" : "" ?>>TV Show</option>
            <option value="Video Game"  <?= $media_type === "Video Game" ? "selected" : "" ?>>Video Game</option>
            <option value="Anime"       <?= $media_type === "Anime" ? "selected" : "" ?>>Anime</option>
            <option value="Manga"       <?= $media_type === "Manga" ? "selected" : "" ?>>Manga</option>
            <option value="Book"        <?= $media_type === "Book" ? "selected" : "" ?>>Book</option>
        </select>

        <label for="media_description">Description *</label>
        <textarea id="media_description" name="media_description" required><?= htmlspecialchars($media_description) ?></textarea>

        <button type="submit" class="request-submit-btn">Submit Request</button>
    </form>
</div>

<?php include "../includes/footer.php"; ?>

</body>
</html>
=======
<?php include __DIR__ . '/../includes/header.php'; ?>

<section class="page-section">
    <h1 class="page-title">Request New Media</h1>

    <p class="page-intro">
        Don&apos;t see something you&apos;d like to review? Suggest a new movie, show, game, book, or other media here.
    </p>

    <div id="form-message" class="form-message"></div>

    <form id="request-new-media-form" class="styled-form">
        <div class="form-row">
            <label for="media_name">Media Name <span class="required">*</span></label>
            <input type="text" name="media_name" id="media_name" required>
        </div>

        <div class="form-row">
            <label for="media_type">Type <span class="required">*</span></label>
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
        </div>

        <div class="form-row">
            <label for="media_description">Description <span class="required">*</span></label>
            <textarea name="media_description" id="media_description" rows="4" required></textarea>
        </div>

        <div class="form-row">
            <button type="submit" class="primary-btn">Submit Request</button>
        </div>
    </form>
</section>

<script src="../assets/javascript/form-request-new-media.js"></script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
>>>>>>> 2e3b5184975ebb06bf30c0922ffe5f4e4bf70925
