<?php
// 1. DATABASE CREDENTIALS (Securely pulled from Environment Variables)
@define('DB_SERVER', getenv('DB_SERVER'));
@define('DB_USERNAME', getenv('DB_USERNAME'));
@define('DB_PASSWORD', getenv('DB_PASSWORD')); 
@define('DB_NAME', getenv('DB_NAME') ?: 'defaultdb'); // Fallback to 'defaultdb' if not set
@define('DB_PORT', getenv('DB_PORT') ?: 2222);        // Fallback to 2222 if not set

// 2. CONNECT TO DATABASE (Must happen before any queries)
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// -------------------------------------------------------------------------
// SITE CONFIGURATION
// -------------------------------------------------------------------------
$sitename = "Your Site Name"; // <-- ADDED THIS LINE (Change to your actual site name)

// -------------------------------------------------------------------------
// IF YOU HAVE ANY QUERIES (like SELECT * FROM settings), PUT THEM HERE
// -------------------------------------------------------------------------

// 3. CRYPTOCURRENCY WALLET ADDRESSES
$USDT_ERC20 = 'XXXXXXXXXXXXXXXXXXX';
$ETH = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';
$BTC = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';
$BNB = 'XXXXXXXXXXXXXXXXXXXXXXXXXX';
$TRX = 'XXXXXXXXXXXXXXXXXXXXXXXXX';
$USDT_TRC20 = 'XXXXXXXXXXXXXXXXX';
$LTC = 'XXXXXXXXXXXXXXXXXXXXXXXX';
$DOGE = 'XXXXXXXXXXXXXXXXXXXXXXX';
$SOL = 'XXXXXXXXXXXXXXXXXXXXXXXXX';
$MATIC = 'XXXXXXXXXXXXXXXXXXXXXXXXX';

?>