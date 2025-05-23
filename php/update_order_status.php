<?php
session_start();
require_once '../php/config.php';


// Validate input
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    $_SESSION['error'] = "Invalid request parameters";
    header("Location: ../pages/kitchenDashboard.php");
    exit();
}

$order_id = (int) $_POST['order_id'];
$new_status = $_POST['status'];

// Validate allowed status transitions
$allowed_statuses = ['Preparing', 'Out For Delivery'];
if (!in_array($new_status, $allowed_statuses)) {
    $_SESSION['error'] = "Invalid status update";
    header("Location: ../pages/kitchenDashboard.php");
    exit();
}

// Get current order status
$stmt = $con->prepare("SELECT ORDER_Status FROM ORDERS WHERE ORDER_ID = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$current_status = $stmt->get_result()->fetch_assoc()['ORDER_Status'];

// Validate status transition
$valid_transitions = [
    'Pending' => ['Preparing'],
    'Preparing' => ['Out For Delivery'],
    'Out For Delivery' => []
];

if (!in_array($new_status, $valid_transitions[$current_status])) {
    $_SESSION['error'] = "Invalid status transition from $current_status to $new_status";
    header("Location: ../pages/kitchenDashboard.php");
    exit();
}

// Update order status
$update_stmt = $con->prepare("UPDATE ORDERS SET ORDER_Status = ? WHERE ORDER_ID = ?");
$update_stmt->bind_param("si", $new_status, $order_id);

try {
    $update_stmt->execute();

    if ($update_stmt->affected_rows === 1) {
        $_SESSION['success'] = "Order #$order_id status updated to $new_status";

        // If moving to delivery, record timestamp
        if ($new_status === 'out_for_delivery') {
            $timestamp_stmt = $con->prepare("UPDATE ORDERS SET ORDER_DeliveryTime = NOW() WHERE ORDER_ID = ?");
            $timestamp_stmt->bind_param("i", $order_id);
            $timestamp_stmt->execute();
            $timestamp_stmt->close();
        }

    } else {
        $_SESSION['error'] = "No changes made to order #$order_id";
    }
} catch (mysqli_sql_exception $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

$update_stmt->close();
header("Location: ../pages/kitchenDashboard.php");
exit();
?>