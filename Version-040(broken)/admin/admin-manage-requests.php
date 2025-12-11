<?php
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../assets/logs/ErrorLogger.php';

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

// Handle deletion if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $deleteId = (int) $_POST['delete_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM newmediarequest WHERE request_ID = :id");
        $stmt->execute([':id' => $deleteId]);
    } catch (Exception $e) {
        log_error("Error deleting media request: " . $e->getMessage());
    }
}

// Fetch all media requests
try {
    $stmt = $pdo->query("
        SELECT 
            request_ID,
            media_name,
            media_type,
            media_description,
            request_date
        FROM newmediarequest
        ORDER BY request_date DESC, request_ID DESC
    ");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    log_error("Error fetching media requests: " . $e->getMessage());
    $requests = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Media Requests</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin-style.css">
</head>
<body>

<?php include __DIR__ . '/admin-nav.php'; ?>

<main class="admin-main">
    <h1>Manage New Media Requests</h1>

    <?php if (empty($requests)): ?>
        <p>No media requests found.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Media Name</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Requested On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($req['request_ID']); ?></td>
                        <td><?php echo htmlspecialchars($req['media_name']); ?></td>
                        <td><?php echo htmlspecialchars($req['media_type']); ?></td>
                        <td><?php echo nl2br(htmlspecialchars($req['media_description'])); ?></td>
                        <td><?php echo htmlspecialchars($req['request_date']); ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Delete this request?');">
                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($req['request_ID']); ?>">
                                <button type="submit" class="admin-btn admin-btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

</main>

</body>
</html>
