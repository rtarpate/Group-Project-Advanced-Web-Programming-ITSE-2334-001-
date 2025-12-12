<?php
require_once __DIR__ . '/../includes/DatabaseConnector.php';
require_once __DIR__ . '/admin-session.php';

require_admin();

$pdo = DatabaseConnector::getConnection();
if (!$pdo) {
    die("Database connection failed.");
}
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
        <?php
        $stmt = $pdo->query("SELECT media_id, media_title, release_date FROM media ORDER BY media_title");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['media_title']) . "</td>";
            echo "<td>" . htmlspecialchars($row['release_date']) . "</td>";
            echo "<td>
                    <form method='POST' action='/router/router.php?action=deleteMedia'>
                        <input type='hidden' name='media_id' value='{$row['media_id']}'>
                        <button type='submit'>Delete</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        ?>
    </tbody>
</table>
