<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';
session_start();

function show_error(array $messages): void
{
    $items = '';
    foreach ($messages as $m) {
        $safe = htmlspecialchars($m, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $items .= "<li>{$safe}</li>";
    }

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizExam | Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<main class="d-flex justify-content-center align-items-center min-vh-100 page-wrap">
    <section class="login-form text-center p-4 p-sm-5">
        <h1 class="mb-2 fw-semibold" id="login-title">Sign up failed</h1>
        <p class="sub-title mb-3">Please fix the following:</p>
        <ul class="text-start mb-3" style="max-width: 320px; margin-left: auto; margin-right: auto;">
            {$items}
        </ul>
        <a href="register.html" class="login1 d-inline-block text-decoration-none">Try again</a>
        <div class="mt-3">
            <a href="login.html" class="link-btn p-0 text-decoration-none">I already have an account</a>
        </div>
    </section>
</main>
</body>
</html>
HTML;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.html', true, 302);
    exit;
}

$username = trim((string)($_POST['username'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$confirm = (string)($_POST['confirm_password'] ?? '');

$errors = [];

if ($username === '') {
    $errors[] = 'Username is required.';
} elseif (mb_strlen($username) < 3) {
    $errors[] = 'Username must be at least 3 characters.';
}

if ($email === '') {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid email address.';
}

if ($password === '') {
    $errors[] = 'Password is required.';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters.';
}

if ($confirm === '') {
    $errors[] = 'Confirm password is required.';
} elseif ($password !== $confirm) {
    $errors[] = 'Passwords do not match.';
}

if (!empty($errors)) {
    show_error($errors);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $mysqli->prepare("INSERT INTO `users` (`username`, `email`, `password_hash`) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $email, $passwordHash);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    // MySQL duplicate entry (username/email).
    if ((int)$e->getCode() === 1062) {
        show_error(['Username or email already exists.']);
    }
    show_error(['Registration failed. Please try again later.']);
}

header('Location: login.html', true, 302);
exit;
