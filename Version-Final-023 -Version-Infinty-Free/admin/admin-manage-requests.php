<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/admin-session.php';

require_admin();

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

$requests = [];

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request_id'])) {
    $deleteId = (int)$_POST['delete_request_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM newmediarequest WHERE request_ID = :id");
        $stmt->execute([':id' => $deleteId]);
        $statusMessage = "Request #{$deleteId} deleted successfully.";
        $statusType = "success";
    } catch (Exception $e) {
        $statusMessage = "Error deleting request: " . $e->getMessage();
        $statusType = "error";
    }
}


try {
    $stmt = $pdo->query("
        SELECT request_ID, media_name, media_type, media_description, request_date
        FROM newmediarequest
        ORDER BY request_date DESC
    ");
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // optional: log the error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Media Requests - Admin</title>
    <link rel="stylesheet" href="/assets/css/admin-style.css">
</head>
<body>

<?php include __DIR__ . '/admin-nav.php'; ?>

<main class="admin-content">
    <h2 class="admin-title">MANAGE MEDIA REQUESTS</h2>
    <p class="admin-subtitle">View requests submitted by users for new media.</p>

    <section class="admin-panel">

        <?php if (!empty($statusMessage)) : ?>
            <p class="admin-status <?= $statusType === 'success' ? 'success' : 'error' ?>">
                <?= htmlspecialchars($statusMessage); ?>
            </p>
        <?php endif; ?>

        <h2>New Media Requests</h2>
        <p>Below are all media requests submitted by users. Admins may delete requests after reviewing them.</p>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Media Name</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th>Request Date</th>
                    <th>Delete</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($requests as $req) : ?>
                    <tr>
                        <td><?= htmlspecialchars($req['request_ID']); ?></td>
                        <td><?= htmlspecialchars($req['media_name']); ?></td>
                        <td><?= htmlspecialchars($req['media_type']); ?></td>
                        <td><?= htmlspecialchars($req['media_description']); ?></td>
                        <td><?= htmlspecialchars($req['request_date']); ?></td>

                        <td>
                            <form method="post" onsubmit="return confirm('Delete this request?');">
                                <input type="hidden" name="delete_request_id" 
                                    value="<?= (int)$req['request_ID']; ?>">
                                <button type="submit" class="table-action-btn table-delete-btn">
                                    DELETE
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </section>

</main>

<footer class="admin-footer">
    © <?= date('Y'); ?> Star Media Review — Admin Panel
</footer>

</body>
</html>
