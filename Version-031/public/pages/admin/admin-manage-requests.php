<?php
require_once __DIR__ . '/admin-session.php';
require_admin_login();

require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/../../assets/logs/ErrorLogger.php';

$success = '';
$error = '';

try {
    $pdo = DatabaseConnector::getConnection();
} catch (Throwable $e) {
    ErrorLogger::log("DB connection error: " . $e->getMessage());
    $error = "Database connection failed.";
    $pdo = null;
}

// Handle DELETE
if ($pdo && $_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_request_id'])) {

    $requestId = (int) $_POST['delete_request_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM newmediarequest WHERE request_ID = :id");
        $stmt->execute([':id' => $requestId]);

        if ($stmt->rowCount() > 0) {
            $success = "Request deleted.";
        } else {
            $error = "Request not found.";
        }

    } catch (Throwable $e) {
        ErrorLogger::log("Error deleting media request: " . $e->getMessage());
        $error = "Failed to delete request.";
    }
}

// Load all requests
$requests = [];

if ($pdo) {
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
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Requests</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<h1>Manage New Media Requests</h1>

<?php if ($success): ?>
<p class="success-message"><?= htmlspecialchars($success) ?></p>
<?php endif; ?>

<?php if ($error): ?>
<p class="error-message"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Type</th>
    <th>Description</th>
    <th>Date</th>
    <th>Actions</th>
</tr>

<?php foreach ($requests as $rq): ?>
<tr>
    <td><?= $rq['request_ID'] ?></td>
    <td><?= htmlspecialchars($rq['media_name']) ?></td>
    <td><?= htmlspecialchars($rq['media_type']) ?></td>
    <td><?= nl2br(htmlspecialchars($rq['media_description'])) ?></td>
    <td><?= $rq['request_date'] ?></td>
    <td>
        <form method="post">
            <input type="hidden" name="delete_request_id" value="<?= $rq['request_ID'] ?>">
            <button type="submit">Delete</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>
