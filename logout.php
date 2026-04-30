<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';

// Clear session variables.
$_SESSION = [];

// Expire session cookie.
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'] ?? '/',
        $params['domain'] ?? '',
        false,
        true
    );
}

session_destroy();

header('Location: login.html', true, 302);
exit;

