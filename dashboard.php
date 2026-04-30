<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'auth.php';
require_login();

$username = htmlspecialchars((string)($_SESSION['username'] ?? 'User'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizExam | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style1.css">
</head>
<body>
<main class="d-flex justify-content-center align-items-center min-vh-100 page-wrap">
    <section class="login-form p-4 p-sm-5" aria-labelledby="dash-title">
        <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
            <div>
                <h1 id="dash-title" class="mb-1 fw-semibold">Dashboard</h1>
                <p class="sub-title mb-0">Welcome, <strong><?= $username ?></strong></p>
            </div>
        </div>

        <hr>

        <div class="text-start">
            <p class="mb-3 muted-text">
                This is your protected entry point. Add your exam pages/routes here next.
            </p>

            <div class="d-grid gap-2">
                <a class="login1 text-decoration-none text-center" href="index.html">Home</a>
                <a class="btn btn-outline-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </section>
</main>
</body>
</html>

