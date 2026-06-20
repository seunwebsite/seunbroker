<?php
include 'header.php'; // Gives us $link and $user_id
if(isset($_POST['inv_id']) && isset($_POST['profit'])) {
    $inv_id = intval($_POST['inv_id']);
    $profit = floatval($_POST['profit']);
    mysqli_query($link, "UPDATE investments SET current_profit = '$profit' WHERE id = '$inv_id' AND user_id = '$user_id'");
}
?>