<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/admin-session.php';

require_admin();

// ---------------------------------------------------------
// DB CONNECTION
// ---------------------------------------------------------
$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

// ---------------------------------------------------------
// PROCESS POST ACTIONS FOR GENRES / TYPES / RATINGS
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    /* -------------------------------------------------------
       ADD NEW GENRE
    ---------------------------------------------------------*/
    if ($action === 'add_genre') {
        $name = trim($_POST['genre_name']);
        if ($name !== '') {
            $stmt = $pdo->prepare("INSERT INTO genres (genre_name) VALUES (:name)");
            $stmt->execute([':name' => $name]);
        }
        header("Location: manage-taxonomy.php");
        exit;
    }

    /* -------------------------------------------------------
       UPDATE GENRE
    ---------------------------------------------------------*/
    if ($action === 'update_genre') {
        $id = intval($_POST['genre_id']);
        $name = trim($_POST['genre_name']);

        $stmt = $pdo->prepare("UPDATE genres SET genre_name = :name WHERE genre_id = :id");
        $stmt->execute([':name' => $name, ':id' => $id]);

        header("Location: manage-taxonomy.php");
        exit;
    }

    /* -------------------------------------------------------
       DELETE GENRE
    ---------------------------------------------------------*/
    if ($action === 'delete_genre') {
        $id = intval($_POST['genre_id']);

        $stmt = $pdo->prepare("DELETE FROM genres WHERE genre_id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: manage-taxonomy.php");
        exit;
    }

    /* -------------------------------------------------------
       ADD NEW MEDIA TYPE
    ---------------------------------------------------------*/
    if ($action === 'add_type') {
        $name = trim($_POST['type_name']);
        if ($name !== '') {
            $stmt = $pdo->prepare("INSERT INTO media_types (type_name) VALUES (:name)");
            $stmt->execute([':name' => $name]);
        }
        header("Location: manage-taxonomy.php");
        exit;
    }

    /* -------------------------------------------------------
       UPDATE MEDIA TYPE
    ---------------------------------------------------------*/
    if ($action === 'update_type') {
        $id = intval($_POST['media_type_id']);
        $name = trim($_POST['type_name']);

        $stmt = $pdo->prepare("UPDATE media_types SET type_name = :name WHERE media_type_id = :id");
        $stmt->execute([':name' => $name, ':id' => $id]);

        header("Location: manage-taxonomy.php");
        exit;
    }

    /* -------------------------------------------------------
       DELETE MEDIA TYPE
    ---------------------------------------------------------*/
    if ($action === 'delete_type') {
        $id = intval($_POST['media_type_id']);
        $stmt = $pdo->prepare("DELETE FROM media_types WHERE media_type_id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: manage-taxonomy.php");
        exit;
    }

    /* -------------------------------------------------------
       ADD NEW RATING
    ---------------------------------------------------------*/
    if ($action === 'add_rating') {
        $code = trim($_POST['rating_code']);
        $desc = trim($_POST['rating_description']);

        if ($code !== '') {
            $stmt = $pdo->prepare("
                INSERT INTO content_ratings (rating_code, description)
                VALUES (:code, :description)
            ");
            $stmt->execute([
                ':code' => $code,
                ':description' => $desc
            ]);
        }
        header("Location: manage-taxonomy.php");
        exit;
    }

    /* -------------------------------------------------------
       UPDATE RATING
    ---------------------------------------------------------*/
    if ($action === 'update_rating') {
        $id   = intval($_POST['rating_id']);
        $code = trim($_POST['rating_code']);
        $desc = trim($_POST['rating_description']);

        $stmt = $pdo->prepare("
            UPDATE content_ratings
            SET rating_code = :code, description = :description
            WHERE rating_id = :id
        ");
        $stmt->execute([
            ':code' => $code,
            ':description' => $desc,
            ':id' => $id
        ]);

        header("Location: manage-taxonomy.php");
        exit;
    }

    /* -------------------------------------------------------
       DELETE RATING
    ---------------------------------------------------------*/
    if ($action === 'delete_rating') {
        $id = intval($_POST['rating_id']);

        $stmt = $pdo->prepare("DELETE FROM content_ratings WHERE rating_id = :id");
        $stmt->execute([':id' => $id]);

        header("Location: manage-taxonomy.php");
        exit;
    }
}

// ---------------------------------------------------------
// LOAD TAXONOMY DATA
// ---------------------------------------------------------
$types = [];
$genres = [];
$ratings = [];

try {
    $typesStmt = $pdo->query("SELECT media_type_id, type_name FROM media_types ORDER BY type_name ASC");
    $types = $typesStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

try {
    $genresStmt = $pdo->query("SELECT genre_id, genre_name FROM genres ORDER BY genre_name ASC");
    $genres = $genresStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

try {
    $ratingStmt = $pdo->query("SELECT rating_id, rating_code, description FROM content_ratings ORDER BY rating_code ASC");
    $ratings = $ratingStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Genres, Types & Ratings - Admin</title>
    <link rel="stylesheet" href="/assets/css/admin-style.css">
</head>
<body>

<?php include __DIR__ . '/admin-nav.php'; ?>

<main class="admin-content">

    <h2 class="admin-title">GENRES, TYPES & RATINGS</h2>
    <p class="admin-subtitle">Manage the site's classification system.</p>

    <!-- ---------------------------------------------- -->
    <!-- GENRES SECTION -->
    <!-- ---------------------------------------------- -->
    <section class="admin-panel">
        <h3>Genres</h3>

        <h4>Add New Genre</h4>
        <form method="post">
            <input type="hidden" name="action" value="add_genre">
            <input type="text" name="genre_name" required>
            <button class="admin-btn">ADD GENRE</button>
        </form>

        <h4>Existing Genres</h4>
        <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name (Editable)</th>
                    <th>Save</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($genres as $g): ?>
                <tr>
                    <td><?= $g['genre_id']; ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="action" value="update_genre">
                            <input type="hidden" name="genre_id" value="<?= $g['genre_id']; ?>">
                            <input type="text" name="genre_name" value="<?= htmlspecialchars($g['genre_name']); ?>">
                    </td>
                    <td><button class="admin-btn-save">SAVE</button></td>
                    </form>
                    <td>
                        <form method="post" onsubmit="return confirm('Delete this genre?')">
                            <input type="hidden" name="action" value="delete_genre">
                            <input type="hidden" name="genre_id" value="<?= $g['genre_id']; ?>">
                            <button class="admin-btn-delete">DELETE</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>

    </section>

    <!-- ---------------------------------------------- -->
    <!-- MEDIA TYPES SECTION -->
    <!-- ---------------------------------------------- -->
    <section class="admin-panel">
        <h3>Media Types</h3>

        <h4>Add New Media Type</h4>
        <form method="post">
            <input type="hidden" name="action" value="add_type">
            <input type="text" name="type_name" required>
            <button class="admin-btn">ADD MEDIA TYPE</button>
        </form>

        <h4>Existing Media Types</h4>
        <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name (Editable)</th>
                    <th>Save</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($types as $t): ?>
                <tr>
                    <td><?= $t['media_type_id']; ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="action" value="update_type">
                            <input type="hidden" name="media_type_id" value="<?= $t['media_type_id']; ?>">
                            <input type="text" name="type_name" value="<?= htmlspecialchars($t['type_name']); ?>">
                    </td>
                    <td>
                            <button class="admin-btn-save">SAVE</button>
                        </form>
                    </td>
                    <td>
                        <form method="post" onsubmit="return confirm('Delete this media type?')">
                            <input type="hidden" name="action" value="delete_type">
                            <input type="hidden" name="media_type_id" value="<?= $t['media_type_id']; ?>">
                            <button class="admin-btn-delete">DELETE</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </section>

    <!-- ---------------------------------------------- -->
    <!-- CONTENT RATINGS SECTION -->
    <!-- ---------------------------------------------- -->
    <section class="admin-panel">
        <h3>Content Ratings</h3>

        <h4>Add New Rating</h4>
        <form method="post">
            <input type="hidden" name="action" value="add_rating">

            <label>Code:</label>
            <input type="text" name="rating_code" required>

            <label>Description (optional):</label>
            <input type="text" name="rating_description">

            <button class="admin-btn">ADD RATING</button>
        </form>

        <h4>Existing Ratings</h4>

        <div class="admin-table-container">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Update</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($ratings as $r): ?>
                <tr>
                    <td><?= $r['rating_id']; ?></td>

                    <td>
                        <form method="post">
                            <input type="hidden" name="action" value="update_rating">
                            <input type="hidden" name="rating_id" value="<?= $r['rating_id']; ?>">

                            <input type="text"
                                   name="rating_code"
                                   value="<?= htmlspecialchars($r['rating_code']); ?>"
                                   required>
                    </td>

                    <td>
                            <input type="text"
                                   name="rating_description"
                                   value="<?= htmlspecialchars($r['description']); ?>">
                    </td>

                    <td>
                            <button class="admin-btn-save">SAVE</button>
                        </form>
                    </td>

                    <td>
                        <form method="post" onsubmit="return confirm('Delete this rating?');">
                            <input type="hidden" name="action" value="delete_rating">
                            <input type="hidden" name="rating_id" value="<?= $r['rating_id']; ?>">
                            <button class="admin-btn-delete">DELETE</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </section>

</main>

<footer class="admin-footer">
    © <?= date('Y'); ?> Star Media Review — Admin Panel
</footer>

</body>
</html>
