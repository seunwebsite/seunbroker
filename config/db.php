<?php
// -----------------------------------------------------------------------------
// DATABASE CONNECTION FILE (CLEAN + SAFE)
// -----------------------------------------------------------------------------

// Only define constants if they have NOT already been defined elsewhere
if (!defined('DB_SERVER')) {
    define('DB_SERVER', 'localhost');
}

if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'root');
}

if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', '');
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'ddfichain');
}

if (!defined('DB_PORT')) {
    define('DB_PORT', 3307);
}

// -----------------------------------------------------------------------------
// CONNECT TO DATABASE
// -----------------------------------------------------------------------------

$link = mysqli_connect(
    DB_SERVER,
    DB_USERNAME,
    DB_PASSWORD,
    DB_NAME,
    DB_PORT
);

// Check connection
if (!$link) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

// Optional: set charset (recommended for fintech apps)
mysqli_set_charset($link, "utf8mb4");

// -----------------------------------------------------------------------------
// SUCCESSFUL CONNECTION
// -----------------------------------------------------------------------------

// You can optionally uncomment this for testing
// echo "DB Connected Successfully";

?>