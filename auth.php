<?php
// Shared session + auth guard helpers.

// Keep the user logged in for 30 days (local dev: HTTP so Secure=false).
$lifetimeSeconds = 60 * 60 * 24 * 30;
session_set_cookie_params($lifetimeSeconds, '/', '', false, true);
session_start();

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        header('Location: login.html', true, 302);
        exit;
    }
}

