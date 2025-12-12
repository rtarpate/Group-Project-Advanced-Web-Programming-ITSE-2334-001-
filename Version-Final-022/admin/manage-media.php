<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/admin-session.php';

require_admin();

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

/* =========================================================
   HANDLE SAVE / DELETE ACTIONS
========================================================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    /* ---------- SAVE ---------- */
    if (isset($_POST['action']) && $_POST['action'] === 'save') {

        $stmt = $pdo->prepare("
            UPDATE media SET
                title = :title,
                release_date = :release_date,
                director = :director,
                media_type_id = :media_type_id,
                genre_id = :genre_id,
                content_rating_id = :content_rating_id,
                image_path = :image_path
            WHERE media_id = :media_id
        ");

        $stmt->execute([
            ':title'              => $_POST['title'],
            ':release_date'       => $_POST['release_date'],
            ':director'           => $_POST['director'],
            ':media_type_id'      => $_POST['media_type_id'],
            ':genre_id'           => $_POST['genre_id'] ?: null,
            ':content_rating_id'  => $_POST['content_rating_id'] ?: null,
            ':image_path'         => $_POST['image_path'],
            ':media_id'           => $_POST['media_id']
        ]);
    }

    /* ---------- DELETE ---------- */
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {

        $stmt = $pdo->prepare("DELETE FROM media WHERE media_id = ?");
        $stmt->execute([$_POST['media_id']]);
    }
}

/* =========================================================
   LOAD DROPDOWN DATA
========================================================= */
$types = $pdo->query("
    SELECT media_type_id, type_name 
    FROM media_types 
    ORDER BY type_name
")->fetchAll(PDO::FETCH_ASSOC);

$genres = $pdo->query("
    SELECT genre_id, genre_name 
    FROM genres 
    ORDER BY genre_name
")->fetchAll(PDO::FETCH_ASSOC);

$ratings = $pdo->query("
    SELECT rating_id, rating_code 
    FROM content_ratings 
    ORDER BY rating_code
")->fetchAll(PDO::FETCH_ASSOC);

/* =========================================================
   LOAD MEDIA
========================================================= */
$media = $pdo->query("
    SELECT * FROM media
    ORDER BY title ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Media</title>

<link rel="stylesheet" href="/assets/css/admin-style.css">
<link rel="stylesheet" href="/assets/css/admin-manage-media.css">
</head>

<body>

<?php include 'admin-nav.php'; ?>

<div class="admin-container">
    <h1 class="admin-title">Manage Media</h1>
    <p class="admin-subtitle">Edit, update, or delete media entries</p>

    <div class="table-wrapper">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Genre</th>
                    <th>Rating</th>
                    <th>Release Date</th>
                    <th>Director</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($media as $m): ?>
                <tr>
                <form method="POST">

                    <input type="hidden" name="media_id" value="<?= $m['media_id'] ?>">

                    <td data-label="Title">
                        <input type="text" name="title"
                               value="<?= htmlspecialchars($m['title']) ?>" required>
                    </td>

                    <td data-label="Type">
                        <select name="media_type_id" required>
                            <?php foreach ($types as $t): ?>
                                <option value="<?= $t['media_type_id'] ?>"
                                    <?= $t['media_type_id'] == $m['media_type_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($t['type_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td data-label="Genre">
                        <select name="genre_id">
                            <option value="">—</option>
                            <?php foreach ($genres as $g): ?>
                                <option value="<?= $g['genre_id'] ?>"
                                    <?= $g['genre_id'] == $m['genre_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($g['genre_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td data-label="Rating">
                        <select name="content_rating_id">
                            <option value="">—</option>
                            <?php foreach ($ratings as $r): ?>
                                <option value="<?= $r['rating_id'] ?>"
                                    <?= $r['rating_id'] == $m['content_rating_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($r['rating_code']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>

                    <td data-label="Release Date">
                        <input type="date" name="release_date"
                               value="<?= $m['release_date'] ?>">
                    </td>

                    <td data-label="Director">
                        <input type="text" name="director"
                               value="<?= htmlspecialchars($m['director']) ?>">
                    </td>

                    <td data-label="Image">
                        <input type="text" name="image_path"
                               value="<?= htmlspecialchars($m['image_path']) ?>">
                    </td>

                    <td data-label="Actions" class="actions">
                        <button type="submit" name="action" value="save" class="btn-save">
                            Save
                        </button>

                        <button type="submit" name="action" value="delete"
                                class="btn-delete"
                                onclick="return confirm('Delete this media and ALL related reviews?');">
                            Delete
                        </button>
                    </td>

                </form>
                </tr>
            <?php endforeach; ?>
            </tbody>

        </table>
    </div>
</div>

</body>
</html>
