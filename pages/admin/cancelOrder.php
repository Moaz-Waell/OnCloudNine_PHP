<?php
session_start();
include('../../php/config.php');

if (!isset($_SESSION['admin_id'])) {
    die("Unauthorized access");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);

    $stmt = $con->prepare("UPDATE ORDERS 
                          SET ORDER_Status = 'Cancelled' 
                          WHERE ORDER_ID = ?");
    $stmt->bind_param("i", $order_id);

    if ($stmt->execute()) {
        header("Location: ../../pages/admin/admin_landing.php");
    } else {
        $_SESSION['error'] = "Error cancelling order";
        header("Location: ../pages/admin/admin_landing.php");
    }
    exit();
}
?>