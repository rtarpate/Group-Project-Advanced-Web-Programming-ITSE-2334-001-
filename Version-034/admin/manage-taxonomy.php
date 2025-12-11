<?php
session_start();
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
// require_once __DIR__ . '/../includes/helper.php'; // if/when needed
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

$message = '';
$error   = '';

try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    ErrorLogger::log("Manage Taxonomy - DB Connection Failed: " . $e->getMessage());
    die("Database connection failed. Please check the error log.");
}

function safeTrim(string $key): string {
    return isset($_POST[$key]) ? trim($_POST[$key]) : '';
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        // ---------------- Genres ----------------
        if ($action === 'add_genre') {
            $name = safeTrim('genre_name');
            if ($name === '') {
                $error = "Genre name cannot be empty.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO genres (genre_name) VALUES (:name)");
                $stmt->execute([':name' => $name]);
                $message = "Genre '{$name}' added successfully.";
            }
        } elseif ($action === 'update_genre') {
            $id   = (int)($_POST['genre_id'] ?? 0);
            $name = safeTrim('genre_name');
            if ($id <= 0 || $name === '') {
                $error = "Genre ID and name are required.";
            } else {
                $stmt = $pdo->prepare("UPDATE genres SET genre_name = :name WHERE genre_id = :id");
                $stmt->execute([':name' => $name, ':id' => $id]);
                $message = "Genre updated successfully.";
            }
        } elseif ($action === 'delete_genre') {
            $id = (int)($_POST['genre_id'] ?? 0);
            if ($id <= 0) {
                $error = "Invalid genre ID.";
            } else {
                $check = $pdo->prepare("SELECT COUNT(*) FROM media WHERE genre_id = ?");
                $check->execute([$id]);
                if ($check->fetchColumn() > 0) {
                    $error = "Cannot delete genre: it is currently used by one or more media items.";
                } else {
                    $stmt = $pdo->prepare("DELETE FROM genres WHERE genre_id = ?");
                    $stmt->execute([$id]);
                    $message = "Genre deleted successfully.";
                }
            }
        }

        // ---------------- Media Types ----------------
        elseif ($action === 'add_media_type') {
            $name = safeTrim('type_name');
            if ($name === '') {
                $error = "Media type name cannot be empty.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO media_types (type_name) VALUES (:name)");
                $stmt->execute([':name' => $name]);
                $message = "Media type '{$name}' added successfully.";
            }
        } elseif ($action === 'update_media_type') {
            $id   = (int)($_POST['media_type_id'] ?? 0);
            $name = safeTrim('type_name');
            if ($id <= 0 || $name === '') {
                $error = "Media type ID and name are required.";
            } else {
                $stmt = $pdo->prepare("UPDATE media_types SET type_name = :name WHERE media_type_id = :id");
                $stmt->execute([':name' => $name, ':id' => $id]);
                $message = "Media type updated successfully.";
            }
        } elseif ($action === 'delete_media_type') {
            $id = (int)($_POST['media_type_id'] ?? 0);
            if ($id <= 0) {
                $error = "Invalid media type ID.";
            } else {
                $check = $pdo->prepare("SELECT COUNT(*) FROM media WHERE media_type_id = ?");
                $check->execute([$id]);
                if ($check->fetchColumn() > 0) {
                    $error = "Cannot delete media type: it is currently used by one or more media items.";
                } else {
                    $stmt = $pdo->prepare("DELETE FROM media_types WHERE media_type_id = ?");
                    $stmt->execute([$id]);
                    $message = "Media type deleted successfully.";
                }
            }
        }

        // ---------------- Content Ratings ----------------
        elseif ($action === 'add_rating') {
            $code = safeTrim('rating_code');
            $desc = safeTrim('rating_description');
            if ($code === '') {
                $error = "Content rating code cannot be empty.";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO content_ratings (rating_code, description)
                    VALUES (:code, :description)
                ");
                $stmt->execute([
                    ':code'        => $code,
                    ':description' => $desc !== '' ? $desc : null
                ]);
                $message = "Content rating '{$code}' added successfully.";
            }
        } elseif ($action === 'update_rating') {
            $id   = (int)($_POST['rating_id'] ?? 0);
            $code = safeTrim('rating_code');
            $desc = safeTrim('rating_description');
            if ($id <= 0 || $code === '') {
                $error = "Rating ID and code are required.";
            } else {
                $stmt = $pdo->prepare("
                    UPDATE content_ratings
                    SET rating_code = :code, description = :description
                    WHERE rating_id = :id
                ");
                $stmt->execute([
                    ':code'        => $code,
                    ':description' => $desc !== '' ? $desc : null,
                    ':id'          => $id
                ]);
                $message = "Content rating updated successfully.";
            }
        } elseif ($action === 'delete_rating') {
            $id = (int)($_POST['rating_id'] ?? 0);
            if ($id <= 0) {
                $error = "Invalid rating ID.";
            } else {
                $check = $pdo->prepare("SELECT COUNT(*) FROM media WHERE content_rating_id = ?");
                $check->execute([$id]);
                if ($check->fetchColumn() > 0) {
                    $error = "Cannot delete rating: it is currently used by one or more media items.";
                } else {
                    $stmt = $pdo->prepare("DELETE FROM content_ratings WHERE rating_id = ?");
                    $stmt->execute([$id]);
                    $message = "Content rating deleted successfully.";
                }
            }
        }

    } catch (Throwable $e) {
        ErrorLogger::log("Manage Taxonomy - Action Error ({$action}): " . $e->getMessage());
        $error = "An error occurred while processing the request. Check error log.";
    }
}

// Load all taxonomy data
try {
    $genres = $pdo->query("SELECT genre_id, genre_name FROM genres ORDER BY genre_name")->fetchAll(PDO::FETCH_ASSOC);
    $mediaTypes = $pdo->query("SELECT media_type_id, type_name FROM media_types ORDER BY media_type_id")->fetchAll(PDO::FETCH_ASSOC);
    $ratings = $pdo->query("SELECT rating_id, rating_code, description FROM content_ratings ORDER BY rating_code")->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    ErrorLogger::log("Manage Taxonomy - Load Data Error: " . $e->getMessage());
    $error = "Failed to load taxonomy data. Check error log.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Genres, Types & Ratings</title>
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/admin-style.css">

</head>
<body>
<header class="admin-header">
    <div class="admin-header-inner">
        <h1>Admin - Manage Genres, Types &amp; Ratings</h1>
        <?php include __DIR__ . '/admin-nav.php'; ?>
    </div>
</header>


<main class="content">
    <?php if ($message): ?>
        <div class="success-message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <!-- Genres -->
    <section>
        <h2>Genres</h2>
        <h3>Add New Genre</h3>
        <form action="manage-taxonomy.php" method="post">
            <input type="hidden" name="action" value="add_genre">
            <label for="genre_name_new">Genre Name:</label>
            <input type="text" id="genre_name_new" name="genre_name" required>
            <button type="submit">Add Genre</button>
        </form>

        <h3>Existing Genres</h3>
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name (Editable)</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($genres): ?>
                <?php foreach ($genres as $g): ?>
                    <tr>
                        <td><?php echo (int)$g['genre_id']; ?></td>
                        <td>
                            <form action="manage-taxonomy.php" method="post">
                                <input type="hidden" name="action" value="update_genre">
                                <input type="hidden" name="genre_id" value="<?php echo (int)$g['genre_id']; ?>">
                                <input type="text" name="genre_name" value="<?php echo htmlspecialchars($g['genre_name']); ?>">
                                <button type="submit">Save</button>
                            </form>
                        </td>
                        <td>
                            <form action="manage-taxonomy.php" method="post"
                                  onsubmit="return confirm('Delete this genre? Only allowed if no media uses it.');">
                                <input type="hidden" name="action" value="delete_genre">
                                <input type="hidden" name="genre_id" value="<?php echo (int)$g['genre_id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No genres found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Media Types -->
    <section style="margin-top:30px;">
        <h2>Media Types</h2>
        <h3>Add New Media Type</h3>
        <form action="manage-taxonomy.php" method="post">
            <input type="hidden" name="action" value="add_media_type">
            <label for="type_name_new">Type Name:</label>
            <input type="text" id="type_name_new" name="type_name" required>
            <button type="submit">Add Media Type</button>
        </form>

        <h3>Existing Media Types</h3>
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name (Editable)</th>
                <th>Delete</th>
            </tr>
            </thead>
            <tbody>
            <?php if ($mediaTypes): ?>
                <?php foreach ($mediaTypes as $t): ?>
                    <tr>
                        <td><?php echo (int)$t['media_type_id']; ?></td>
                        <td>
                            <form action="manage-taxonomy.php" method="post">
                                <input type="hidden" name="action" value="update_media_type">
                                <input type="hidden" name="media_type_id" value="<?php echo (int)$t['media_type_id']; ?>">
                                <input type="text" name="type_name" value="<?php echo htmlspecialchars($t['type_name']); ?>">
                                <button type="submit">Save</button>
                            </form>
                        </td>
                        <td>
                            <form action="manage-taxonomy.php" method="post"
                                  onsubmit="return confirm('Delete this media type? Only allowed if no media uses it.');">
                                <input type="hidden" name="action" value="delete_media_type">
                                <input type="hidden" name="media_type_id" value="<?php echo (int)$t['media_type_id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3">No media types found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

    <!-- Content Ratings -->
    <section style="margin-top:30px;">
        <h2>Content Ratings</h2>
        <h3>Add New Rating</h3>
        <form action="manage-taxonomy.php" method="post">
            <input type="hidden" name="action" value="add_rating">
            <label for="rating_code_new">Code:</label>
            <input type="text" id="rating_code_new" name="rating_code" required>
            <label for="rating_description_new">Description (optional):</label>
            <input type="text" id="rating_description_new" name="rating_description">
            <button type="submit">Add Rating</button>
        </form>

        <h3>Existing Ratings</h3>
        <table border="1" cellpadding="6" cellspacing="0">
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
            <?php if ($ratings): ?>
                <?php foreach ($ratings as $r): ?>
                    <tr>
                        <td><?php echo (int)$r['rating_id']; ?></td>
                        <td>
                            <form action="manage-taxonomy.php" method="post">
                                <input type="hidden" name="action" value="update_rating">
                                <input type="hidden" name="rating_id" value="<?php echo (int)$r['rating_id']; ?>">
                                <input type="text" name="rating_code" value="<?php echo htmlspecialchars($r['rating_code']); ?>">
                        </td>
                        <td>
                                <input type="text" name="rating_description" value="<?php echo htmlspecialchars($r['description'] ?? ''); ?>">
                        </td>
                        <td>
                                <button type="submit">Save</button>
                            </form>
                        </td>
                        <td>
                            <form action="manage-taxonomy.php" method="post"
                                  onsubmit="return confirm('Delete this rating? Only allowed if no media uses it.');">
                                <input type="hidden" name="action" value="delete_rating">
                                <input type="hidden" name="rating_id" value="<?php echo (int)$r['rating_id']; ?>">
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No content ratings found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </section>

</main>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Star Media Review - Admin Panel</p>
</footer>
</body>
</html>
