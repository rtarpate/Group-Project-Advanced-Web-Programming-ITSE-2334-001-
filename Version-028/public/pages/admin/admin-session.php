<?php
// admin-session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function is_admin_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin_login(): void
{
    if (!is_admin_logged_in()) {
        header('Location: admin-login.php');
        exit;
    }
}

function get_admin_name(): string
{
    return $_SESSION['admin_name'] ?? 'Admin';
}
