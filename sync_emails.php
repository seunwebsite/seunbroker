<?php

$secret_key = $_ENV['CRON_SECRET_KEY'];

if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    header("HTTP/1.1 403 Forbidden");
    die('Unauthorized access.');
}

// 2. INCLUDE YOUR DATABASE CONNECTION
require_once __DIR__ . '../config/db.php'; 
// Note: $link is now available from your db.php file

// 3. GMAIL CONFIG FROM ENV (Ensure these are in your .env file)
$gmail_user = $_ENV['GMAIL_USER'];
$gmail_app_password = $_ENV['GMAIL_PASS'];

// 4. Connect to Gmail via IMAP
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$inbox = imap_open($hostname, $gmail_user, $gmail_app_password);

if (!$inbox) {
    die('Cannot connect to Gmail: ' . imap_last_error());
}

// 5. Search for UNSEEN emails
$emails = imap_search($inbox, 'UNSEEN');

if ($emails) {
    foreach ($emails as $email_number) {
        $overview = imap_fetch_overview($inbox, $email_number, 0);
        $subject = mysqli_real_escape_string($link, $overview[0]->subject ?? '(No Subject)');
        $from = mysqli_real_escape_string($link, $overview[0]->from ?? 'Unknown');
        
        // Fetch body
        $body = mysqli_real_escape_string($link, imap_fetchbody($inbox, $email_number, 1));
        
        // 6. Save to Database using your $link
        $sql = "INSERT INTO emails (subject, sender, body) VALUES ('$subject', '$from', '$body')";
        mysqli_query($link, $sql);

        // Mark as READ
        imap_setflag_full($inbox, $email_number, "\\Seen");
        
        echo "Imported: $subject <br>";
    }
} else {
    echo "No new emails to process.";
}

imap_close($inbox);
?>