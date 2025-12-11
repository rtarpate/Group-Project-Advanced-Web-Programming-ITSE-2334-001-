<?php
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../includes/helper.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

$error    = '';
$success  = '';
$requests = [];

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    $error = "Failed to connect to the database.";
} else {

    // Handle approve / delete
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action     = clean('action', 'string', INPUT_POST);
        $requestId  = filter_input(INPUT_POST, 'request_id', FILTER_VALIDATE_INT);

        if (!$requestId) {
            $error = "Invalid request ID.";
        } else {
            try {
                if ($action === 'approve') {

                    // Move from newmediarequest to media table (basic example)
                    $stmt = $pdo->prepare("
                        SELECT * FROM newmediarequest WHERE request_ID = :id
                    ");
                    $stmt->execute([':id' => $requestId]);
                    $rq = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($rq) {
                        $insert = $pdo->prepare("
                            INSERT INTO media (media_title, media_type, media_description)
                            VALUES (:name, :type, :description)
                        ");
                        $insert->execute([
                            ':name'        => $rq['media_name'],
                            ':type'        => $rq['media_type'],
                            ':description' => $rq['media_description'],
                        ]);

                        $del = $pdo->prepare("DELETE FROM newmediarequest WHERE request_ID = :id");
                        $del->execute([':id' => $requestId]);

                        $success = "Request approved and moved to media.";
                    } else {
                        $error = "Request not found.";
                    }

                } elseif ($action === 'delete') {
                    $del = $pdo->prepare("DELETE FROM newmediarequest WHERE request_ID = :id");
                    $del->execute([':id' => $requestId]);
                    $success = "Request deleted.";
                }

            } catch (Throwable $e) {
                ErrorLogger::log("MANAGE REQUESTS ERROR: " . $e->getMessage());
                $error = "An error occurred while updating the request.";
            }
        }
    }

    // Load requests
    if (!$error) {
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
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/style.css">
    <link rel="stylesheet" href="/groupproject/Version-033/assets/css/admin-style.css">
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

            <?php if ($error): ?>
                <div class="admin-message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="admin-message success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <?php if (!$error && empty($requests)): ?>
                <p>No pending requests.</p>
            <?php else: ?>
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
                            <td><?= htmlspecialchars($rq['request_date']) ?></td>
                            <td>
                                <form method="post" class="inline-form">
                                    <input type="hidden" name="request_id" value="<?= $rq['request_ID'] ?>">
                                    <button type="submit" name="action" value="approve" class="admin-btn small">Approve</button>
                                    <button type="submit" name="action" value="delete" class="admin-btn small danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>

        </section>
    </main>

    <footer class="admin-footer">
        <p>&copy; <?php echo date('Y'); ?> Star Media Review — Admin Panel</p>
    </footer>
</div>
</body>
</html>
