<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';
session_start();

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

// Fetch user by username.
$stmt = $mysqli->prepare("SELECT `id`, `username`, `password_hash` FROM `users` WHERE `username` = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || empty($user['password_hash'])) {
    show_error('Invalid username or password.');
}

if (!password_verify($password, $user['password_hash'])) {
    show_error('Invalid username or password.');
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = (string)$user['username'];

header('Location: index.html', true, 302);
exit;
