<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php', true, 302);
    exit;
}

function show_error(string $message): void
{
    $safe = htmlspecialchars($message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizExam | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<main class="d-flex justify-content-center align-items-center min-vh-100 page-wrap">
    <section class="login-form text-center p-4 p-sm-5">
        <h1 class="mb-2 fw-semibold" id="login-title">Login failed</h1>
        <p class="sub-title mb-4">{$safe}</p>
        <a href="login.html" class="login1 d-inline-block text-decoration-none">Try again</a>
        <div class="mt-3">
            <a href="register.html" class="link-btn p-0 text-decoration-none">Create an account</a>
        </div>
    </section>
</main>
</body>
</html>
HTML;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html', true, 302);
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$password = (string)($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    show_error('Username and password are required.');
}

// Debug: show detailed SQL errors if `login.php?debug=1` was used.
$debug = (isset($_GET['debug']) && (string)$_GET['debug'] === '1');

try {
    // Fetch user by username (use bind_result for compatibility).
    $stmt = $mysqli->prepare("SELECT `id`, `username`, `password_hash` FROM `users` WHERE `username` = ? LIMIT 1");
    $stmt->bind_param('s', $username);
    $stmt->execute();

    $stmt->bind_result($id, $uname, $passwordHash);
    if (!$stmt->fetch()) {
        show_error($debug ? 'No user found for that username.' : 'Invalid username or password.');
    }

    if (empty($passwordHash) || !password_verify($password, (string)$passwordHash)) {
        show_error($debug ? 'Password hash mismatch.' : 'Invalid username or password.');
    }
} catch (mysqli_sql_exception $e) {
    if ($debug) {
        show_error('Database error: ' . $e->getMessage());
    }
    show_error('Login failed. Please try again later.');
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int)$id;
$_SESSION['username'] = (string)$uname;

header('Location: dashboard.php', true, 302);
exit;
