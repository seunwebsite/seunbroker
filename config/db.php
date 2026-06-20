<?php
// -----------------------------------------------------------------------------
// LOAD .ENV
// -----------------------------------------------------------------------------

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// -----------------------------------------------------------------------------
// DATABASE CONFIG FROM ENV
// -----------------------------------------------------------------------------

$DB_SERVER   = $_ENV['DB_SERVER'] ?? '127.0.0.1';
$DB_USERNAME = $_ENV['DB_USERNAME'] ?? 'root';
$DB_PASSWORD = $_ENV['DB_PASSWORD'] ?? '';
$DB_NAME     = $_ENV['DB_NAME'] ?? 'ddfichain';
$DB_PORT     = $_ENV['DB_PORT'] ?? 3306;

// -----------------------------------------------------------------------------
// CONNECT TO DATABASE
// -----------------------------------------------------------------------------

$link = mysqli_connect(
    $DB_SERVER,
    $DB_USERNAME,
    $DB_PASSWORD,
    $DB_NAME,
    (int)$DB_PORT
);

// Check connection
if (!$link) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Set charset (important for fintech/security apps)
mysqli_set_charset($link, "utf8mb4");

// -----------------------------------------------------------------------------
// READY
// -----------------------------------------------------------------------------

?>