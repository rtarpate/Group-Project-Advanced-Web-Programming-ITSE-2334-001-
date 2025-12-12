<?php
// ============================================================
// helper.php — shared utility functions
// ============================================================

/**
 * Clean user input (trims, removes slashes, HTML escapes)
 */
function clean_input(?string $value): string
{
    if ($value === null) return "";
    return htmlspecialchars(trim(stripslashes($value)), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect to a URL safely
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Debug dump helper (use only during testing)
 */
function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit;
}
