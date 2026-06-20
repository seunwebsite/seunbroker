<?php
// -----------------------------------------------------------------------------
// LOAD .ENV
// -----------------------------------------------------------------------------

require_once __DIR__ . '/../vendor/autoload.php';

// Use a more robust path resolution
$dotenvPath = realpath(__DIR__ . '/../');

if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
} else {
    // Optional: Log this or handle it gracefully if you don't want a fatal error
    die("Error: .env file not found in " . $dotenvPath);
}

// -----------------------------------------------------------------------------
// DATABASE CONFIG FROM ENV
// -----------------------------------------------------------------------------

// Use $_ENV or getenv() - $_ENV is generally preferred when using Dotenv
$DB_SERVER   = $_ENV['DB_SERVER']   ?? '127.0.0.1';
$DB_USERNAME = $_ENV['DB_USERNAME'] ?? 'root';
$DB_PASSWORD = $_ENV['DB_PASSWORD'] ?? '';
$DB_NAME     = $_ENV['DB_NAME']     ?? 'test';
$DB_PORT     = (int)($_ENV['DB_PORT'] ?? 3306);

// -----------------------------------------------------------------------------
// CONNECT TO DATABASE
// -----------------------------------------------------------------------------

// Use mysqli_report to handle errors better than manual checks
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $link = mysqli_connect($DB_SERVER, $DB_USERNAME, $DB_PASSWORD, $DB_NAME, $DB_PORT);
    mysqli_set_charset($link, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    // In production, log $e->getMessage() instead of showing it to the user
    die("Database Connection Failed: " . $e->getMessage());
}

// -----------------------------------------------------------------------------
// READY
// -----------------------------------------------------------------------------
?>