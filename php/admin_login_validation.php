<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_POST['ID'];
    $admin_pin = $_POST['password'];

    // Validate credentials
    $stmt = $con->prepare("SELECT * FROM ADMINS WHERE ADMIN_ID = ? AND ADMIN_Pin = ?");
    $stmt->bind_param("ii", $admin_id, $admin_pin);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

        // Set admin session variables
        $_SESSION['admin_id'] = $admin['ADMIN_ID'];
        $_SESSION['admin_name'] = $admin['ADMIN_Name'];

        header("Location: ../pages/admin/admin_landing.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid ID or PIN Code";
        header("Location: ../pages/admin/admin_login.php");
        exit();
    }
}
?>