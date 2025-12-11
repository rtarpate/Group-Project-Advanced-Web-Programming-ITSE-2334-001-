<?php
// admin-nav.php
// Shared navigation bar for all admin pages.

$current = basename($_SERVER['PHP_SELF']); // e.g. "admin-dashboard.php"

function nav_active(string $file, string $current): string {
    return $file === $current ? 'active' : '';
}
?>
<nav class="admin-nav">
    <ul>
        <li>
            <a href="admin-dashboard.php" class="<?php echo nav_active('admin-dashboard.php', $current); ?>">
                Dashboard
            </a>
        </li>
        <li>
            <a href="admin-add-media.php" class="<?php echo nav_active('admin-add-media.php', $current); ?>">
                Add Media
            </a>
        </li>
        <li>
            <a href="manage-media.php" class="<?php echo nav_active('manage-media.php', $current); ?>">
                Manage Media
            </a>
        </li>
        <li>
            <a href="manage-taxonomy.php" class="<?php echo nav_active('manage-taxonomy.php', $current); ?>">
                Genres &amp; Types
            </a>
        </li>
        <li>
            <a href="admin-manage-requests.php" class="<?php echo nav_active('admin-manage-requests.php', $current); ?>">
                Media Requests
            </a>
        </li>
        <li>
            <a href="admin-manage-reviews.php" class="<?php echo nav_active('admin-manage-reviews.php', $current); ?>">
                User Reviews
            </a>
        </li>
        <li>
            <a href="admin-logout.php" class="logout">
                Logout
            </a>
        </li>
    </ul>
</nav>
