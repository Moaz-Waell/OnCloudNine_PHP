<?php
session_start();
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../pages/aast/uniUserLogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $order_id = intval($_POST['order_id']);

    $stmt = $con->prepare("UPDATE ORDERS 
                          SET ORDER_Status = 'Cancelled' 
                          WHERE ORDER_ID = ? 
                          AND USERS_ID = ?
                          AND ORDER_Status IN ('Pending', 'In Progress')");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();

    header("Location: ../pages/user/orders.php");
    exit();
}