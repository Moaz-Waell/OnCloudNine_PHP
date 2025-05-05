<?php
session_start();
include('config.php'); // config.php is in the same "php" directory

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['ID'];
    $pincode = $_POST['password'];

    // Prepare and execute the query
    $stmt = $con->prepare("SELECT USERS_Name, USERS_ID FROM Uni_users WHERE USERS_ID = ? AND USERS_Pincode = ?");
    $stmt->bind_param("is", $id, $pincode); // i: integer (U_ID), s: string (U_Pincode)
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
        // Login successful
        $_SESSION['id'] = $row['USERS_ID'];
        header("Location: ../pages/aast/uniUserPortal.php"); // Redirect to student portal
        exit();
    } else {
        $_SESSION['error'] = "Invalid ID or Pincode";
        header("Location: ../pages/aast/uniUserLogin.php"); // Redirect back to the login page
        exit();
    }
}
?>