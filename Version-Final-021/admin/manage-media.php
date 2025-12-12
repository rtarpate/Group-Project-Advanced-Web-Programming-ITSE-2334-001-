<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/admin-session.php';

require_admin();

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}

$stmt = $pdo->query("
    SELECT 
        m.media_id,
        m.title,
        mt.type_name,
        m.release_date
    FROM media m
    LEFT JOIN media_types mt ON m.media_type_id = mt.media_type_id
    ORDER BY m.title ASC
");
?>

<h1>Manage Media</h1>

<table class="admin-table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Type</th>
            <th>Release Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($stmt->rowCount() === 0): ?>
            <tr>
                <td colspan="4">No media found.</td>
            </tr>
        <?php endif; ?>

        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['type_name'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($row['release_date']) ?></td>
                <td>
                    <form method="POST" action="/router/router.php?action=deleteMedia" onsubmit="return confirm('Delete this media?');">
                        <input type="hidden" name="media_id" value="<?= $row['media_id'] ?>">
                        <button type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
