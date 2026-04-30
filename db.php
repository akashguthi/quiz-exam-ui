<?php
// Central DB connection + schema bootstrap.
// Update the credentials below to match your XAMPP MySQL setup.

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'quiz_app';

$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS);
if ($mysqli->connect_errno) {
    // Avoid leaking internal details to the user.
    http_response_code(500);
    exit('Database connection failed.');
}

try {
    // Ensure DB exists.
    $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$DB_NAME}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $mysqli->select_db($DB_NAME);
    $mysqli->set_charset('utf8mb4');

    // Ensure users table exists.
    $mysqli->query("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `username` VARCHAR(50) NOT NULL,
            `email` VARCHAR(100) NOT NULL,
            `password_hash` VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_users_username` (`username`),
            UNIQUE KEY `uq_users_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit('Database schema error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
}

