<?php
// 🔥 ADD THESE 3 LINES (ERROR DEBUG)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'db.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';

if (is_logged_in()) {
    header('Location: dashboard.php', true, 302);
    exit;
}

function show_error(array $messages): void
{
    $items = '';
    foreach ($messages as $m) {
        $safe = htmlspecialchars($m, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $items .= "<li>{$safe}</li>";
    }

    echo <<<HTML
<h2>Signup Error</h2>
<ul>{$items}</ul>
<a href="register.html">Go Back</a>
HTML;
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.html', true, 302);
    exit;
}

// 🔥 GET FORM DATA
$username = trim((string)($_POST['username'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$password = (string)($_POST['password'] ?? '');
$confirm = (string)($_POST['confirm_password'] ?? '');

$errors = [];

// 🔥 VALIDATION
if ($username === '') {
    $errors[] = 'Username is required.';
}

if ($email === '') {
    $errors[] = 'Email is required.';
}

if ($password === '') {
    $errors[] = 'Password is required.';
}

if ($password !== $confirm) {
    $errors[] = 'Passwords do not match.';
}

if (!empty($errors)) {
    show_error($errors);
}

// 🔥 HASH PASSWORD
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $mysqli->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $email, $passwordHash);

    $stmt->execute();

    // 🔥 DEBUG OUTPUT
    echo "✅ Inserted successfully!";
    exit;

} catch (mysqli_sql_exception $e) {
    echo "❌ ERROR: " . $e->getMessage();
    exit;
}