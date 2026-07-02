<?php
require __DIR__ . '/config/db.php';

// Use /tmp for the log file - this works on almost all web servers
$logFile = sys_get_temp_dir() . '/db_heartbeat.log';
$timestamp = date("Y-m-d H:i:s");

if ($link) {
    $result = mysqli_query($link, "SELECT 1");
    
    if ($result) {
        file_put_contents($logFile, "[$timestamp] SUCCESS: Query executed successfully.\n", FILE_APPEND);
        http_response_code(200);
        echo "OK - Heartbeat query successful at $timestamp";
    } else {
        $error = mysqli_error($link);
        file_put_contents($logFile, "[$timestamp] FAILED: Query failed. Error: $error\n", FILE_APPEND);
        http_response_code(500);
        echo "Database Query Failed: $error";
    }
} else {
    file_put_contents($logFile, "[$timestamp] FAILED: Could not connect to database.\n", FILE_APPEND);
    http_response_code(500);
    echo "Connection Failed";
}
?>