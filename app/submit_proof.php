<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/fichain/config/db.php'; 

if (!isset($_SESSION['user_id'])) { exit("Unauthorized"); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount_usd = floatval($_POST['amount']);
    
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/fichain/uploads/";
    if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
    
    $file_name = time() . "_" . basename($_FILES["proof"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["proof"]["tmp_name"], $target_file)) {
        $stmt = $link->prepare("INSERT INTO transactions (user_id, type, amount_usd, status, tx_hash) VALUES (?, 'deposit', ?, 'pending', ?)");
        $stmt->bind_param("ids", $user_id, $amount_usd, $file_name);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Deposit request submitted! Awaiting admin confirmation.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $stmt->error]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error uploading file.']);
    }
}
?>