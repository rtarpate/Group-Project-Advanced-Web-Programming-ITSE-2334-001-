<?php
// ------------------------------
// Helper functions
// ------------------------------
function clean($key, $type = 'string', $src = INPUT_POST)
{
    $filter  = FILTER_SANITIZE_SPECIAL_CHARS;
    $options = 0;

    if ($type === 'email') {
        $filter = FILTER_SANITIZE_EMAIL;
    } elseif ($type === 'url') {
        $filter = FILTER_SANITIZE_URL;
    } elseif ($type === 'int') {
        $filter = FILTER_SANITIZE_NUMBER_INT;
    } elseif ($type === 'float') {
        $filter  = FILTER_SANITIZE_NUMBER_FLOAT;
        $options = FILTER_FLAG_ALLOW_FRACTION;
    }

    $value = filter_input($src, $key, $filter, $options);
    return trim($value ?? '');
}

function esc($val)
{
    return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
}
