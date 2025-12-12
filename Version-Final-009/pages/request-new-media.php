<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';

$success = "";
$error   = "";

$name  = "";
$type  = "";
$desc  = "";

// ----------------------------------------------
// FETCH MEDIA TYPES FROM DATABASE (Dynamic)
// ----------------------------------------------
try {
    $pdo = DatabaseConnector::getConnection();
    $typeStmt = $pdo->query("SELECT media_type_id, type_name FROM media_types ORDER BY type_name ASC");
    $mediaTypes = $typeStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $mediaTypes = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = clean_input($_POST['media_name'] ?? '');
    $type = clean_input($_POST['media_type'] ?? '');
    $desc = clean_input($_POST['media_description'] ?? '');

    if ($name === "" || $type === "" || $desc === "") {
        $error = "Please fill out all required fields.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO newmediarequest
                (media_name, media_type, media_description, request_date)
                VALUES (:name, :type, :desc, NOW())
            ");

            $stmt->execute([
                ':name' => $name,
                ':type' => $type,
                ':desc' => $desc
            ]);

            $success = "Your request has been submitted!";
            $name = $type = $desc = "";

        } catch (Exception $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="request-form-page">

    <h1 class="page-title">Request New Media</h1>

    <?php if ($success): ?>
        <p class="success-message" id="msg"><?= htmlspecialchars($success) ?></p>
    <?php elseif ($error): ?>
        <p class="error-message" id="msg"><?= htmlspecialchars($error) ?></p>
    <?php else: ?>
        <p id="msg" style="display:none;"></p>
    <?php endif; ?>

    <form action="" method="POST" class="request-form">

        <!-- Media Name Field -->
        <label>Media Name *</label>
        <input type="text" name="media_name" 
               value="<?= htmlspecialchars($name); ?>" required>

        <!-- Dynamic Media Type Dropdown -->
        <label>Type *</label>
        <select name="media_type" required>
            <option value="">Select Type</option>

            <?php foreach ($mediaTypes as $mt): ?>
                <option value="<?= htmlspecialchars($mt['type_name']); ?>"
                    <?= ($type === $mt['type_name']) ? "selected" : "" ?>>
                    <?= htmlspecialchars($mt['type_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Description Field -->
        <label>Description *</label>
        <textarea name="media_description" rows="4" required>
<?= htmlspecialchars($desc); ?>
        </textarea>

        <button type="submit">Submit</button>

    </form>
</section>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const msg = document.getElementById("msg");
    if (msg && msg.textContent.trim() !== "") {
        msg.style.display = "block";
        setTimeout(() => msg.classList.add("fade-out"), 1200);
    }
});
</script>
