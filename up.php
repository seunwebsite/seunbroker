<?php
require __DIR__ . '/config/db.php';

// Check if $link is established
if ($link) {
    // FORCE a real query to reset the Aiven inactivity timer
    $result = mysqli_query($link, "SELECT 1");
    
    if ($result) {
        http_response_code(200);
        echo "OK";
    } else {
        // If the query fails, the DB is likely sleeping/off
        http_response_code(500);
        echo "Database Query Failed";
    }
} else {
    http_response_code(500);
    echo "Connection Failed";
}
?>