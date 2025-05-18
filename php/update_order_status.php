<?php
session_start();
include('config.php');

$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
$status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_STRING);

$allowed_statuses = ['Preparing', 'Out For Delivery'];
if (!$orderId || !in_array($status, $allowed_statuses)) {
    $_SESSION['error'] = "Invalid update request";
    header("Location: ../pages/kitchenDashboard.php");
    exit();
}

try {
    $stmt = $con->prepare("UPDATE ORDERS SET ORDER_Status = ? WHERE ORDER_ID = ?");
    $stmt->bind_param("si", $status, $orderId);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $_SESSION['error'] = "No changes made - order might not exist";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Update failed: " . $e->getMessage();
}

header("Location: ../pages/kitchenDashboard.php");
exit();
?>