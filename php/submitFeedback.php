<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id']) || !isset($_POST['order_id']) || !isset($_POST['rating'])) {
    header("Location: ../../pages/aast/uniUserLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_POST['order_id']);
$rating = intval($_POST['rating']);

// Validate rating
if ($rating < 1 || $rating > 5) {
    die("Invalid rating value");
}

$stmt = $con->prepare("UPDATE ORDERS 
                      SET ORDER_Feedback = ? 
                      WHERE ORDER_ID = ? 
                      AND USERS_ID = ?
                      AND ORDER_Status = 'Delivered'");
$stmt->bind_param("iii", $rating, $order_id, $user_id);
$stmt->execute();

header("Location: ../pages/user/orders.php");
exit();