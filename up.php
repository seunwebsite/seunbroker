<?php
require __DIR__ . '/config/db.php';

// Now check if $link exists
if (isset($link) && mysqli_ping($link)) {
    http_response_code(200);
    echo "OK";
} else {
    http_response_code(500);
    echo "Database Error";
}