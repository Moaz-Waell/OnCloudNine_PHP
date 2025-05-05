<?php
session_start();
include('config.php'); // config.php is in the same "php" directory

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['ID'];
    $pincode = $_POST['password'];

    // Prepare and execute the query
    $stmt = $con->prepare("SELECT USERS_Name FROM users WHERE USERS_ID = ? AND USERS_Pincode = ?");
    $stmt->bind_param("is", $id, $pincode); // i: integer (U_ID), s: string (U_Pincode)
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Login successful
        $_SESSION['username'] = $row['USERS_Name'];
        header("Location: ../pages/user/home.php"); // Redirect to pages/home.php
        exit();
    } else {
        // Login failed
        $_SESSION['error'] = "Invalid ID or Pincode";
        header("Location: ../pages/user_login.php"); // Redirect back to login page
        exit();
    }
}
?>