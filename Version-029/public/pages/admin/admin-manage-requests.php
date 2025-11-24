<?php
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/starstarmediareviewdatabase.php';
require_once __DIR__ . '/../../assets/logs/ErrorLogger.php';

$success = '';
$error   = '';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_request_id'])) {
    $requestId = (int) $_POST['delete_request_id'];

    if ($requestId > 0) {
        if (StarStarMediaDatabase::deleteNewMediaRequest($requestId)) {
            $success = 'Request deleted.';
        } else {
            $error = 'Failed to delete request.';
        }
    }
}

$requests = StarStarMediaDatabase::getAllNewMediaRequests();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage New Media Requests - Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<header>
    <h1>Admin - Manage New Media Requests</h1>
</header>

<div class="container">
    <p><a href="admin-dashboard.php">&larr; Back to Dashboard</a></p>

    <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (empty($requests)): ?>
        <p>No media requests found.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <tr>
                <th>ID</th>
                <th>Media Name</th>
                <th>Type</th>
                <th>Description</th>
                <th>Requested At</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?php echo (int)$r['request_id']; ?></td>
                    <td><?php echo htmlspecialchars($r['media_name']); ?></td>
                    <td><?php echo htmlspecialchars($r['media_type']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($r['media_description'])); ?></td>
                    <td><?php echo htmlspecialchars($r['request_date']); ?></td>
                    <td>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="delete_request_id" value="<?php echo (int)$r['request_id']; ?>">
                            <button type="submit" onclick="return confirm('Delete this request?');">
                                Delete
                            </button>
                        </form>
                        <!-- Future: link to pre-fill Add Media form -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
