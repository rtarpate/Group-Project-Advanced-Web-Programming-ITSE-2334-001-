<?php
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

$success = '';
$error = '';

try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    ErrorLogger::log("DB connection error: " . $e->getMessage());
    $error = "Database connection failed.";
    $pdo = null;
}

if ($pdo) {
    // Handle delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request_id'])) {
        $id = (int) $_POST['delete_request_id'];
        try {
            $stmt = $pdo->prepare("DELETE FROM newmediarequest WHERE request_ID = :id");
            $stmt->execute([':id' => $id]);
            $success = "Request deleted successfully.";
        } catch (Throwable $e) {
            ErrorLogger::log("Error deleting media request: " . $e->getMessage());
            $error = "Failed to delete request.";
        }
    }

    // Load requests
    if ($error === '') {
        try {
            $stmt = $pdo->query("
                SELECT request_ID, media_name, media_type, media_description, request_date
                FROM newmediarequest
                ORDER BY request_date DESC
            ");
            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (Throwable $e) {
            ErrorLogger::log("Error fetching media requests: " . $e->getMessage());
            $error = "Failed to load data.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Requests</title>
    <link rel="stylesheet" href="/groupproject/Version-041/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-041/assets/css/admin-style.css">
</head>
<body class="admin-body">
<div class="admin-wrapper">

    <header class="admin-header">
        <div class="admin-header-inner">
            <h1>Admin - Manage New Media Requests</h1>
            <?php include __DIR__ . '/admin-nav.php'; ?>
        </div>
    </header>

    <main class="admin-main">
        <section class="admin-panel">
            <h2>New Media Requests</h2>

            <?php if ($success): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <?php if ($error): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if (!empty($requests)): ?>
                <div class="admin-table-wrapper">
                    <table class="admin-table">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Media Name</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Request Date</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($requests as $rq): ?>
                            <tr>
                                <td><?= $rq['request_ID'] ?></td>
                                <td><?= htmlspecialchars($rq['media_name']) ?></td>
                                <td><?= htmlspecialchars($rq['media_type']) ?></td>
                                <td><?= nl2br(htmlspecialchars($rq['media_description'])) ?></td>
                                <td><?= $rq['request_date'] ?></td>
                                <td>
                                    <form method="post" style="margin:0;">
                                        <input type="hidden" name="delete_request_id"
                                               value="<?= $rq['request_ID'] ?>">
                                        <button type="submit">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No media requests found.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer class="admin-footer">
        <p>&copy; <?php echo date('Y'); ?> Star Media Review — Admin Panel</p>
    </footer>
</div>
</body>
</html>
