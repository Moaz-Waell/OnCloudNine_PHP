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

    // Get order details
    $stmt = $con->prepare("SELECT * FROM ORDER_DETAILS WHERE ORDER_ID = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // Insert into cart
    foreach ($details as $item) {
        $check = $con->prepare("SELECT * FROM CART 
                              WHERE USERS_ID = ? 
                              AND MEAL_ID = ?");
        $check->bind_param("ii", $user_id, $item['MEAL_ID']);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $con->query("UPDATE CART 
                        SET QUANTITY = QUANTITY + {$item['M_Quantity']} 
                        WHERE USERS_ID = $user_id 
                        AND MEAL_ID = {$item['MEAL_ID']}");
        } else {
            $insert = $con->prepare("INSERT INTO CART 
                                   (USERS_ID, MEAL_ID, QUANTITY, NOTE) 
                                   VALUES (?, ?, ?, ?)");
            $insert->bind_param(
                "iiis",
                $user_id,
                $item['MEAL_ID'],
                $item['M_Quantity'],
                $item['NOTE']
            );
            $insert->execute();
        }
    }

    header("Location: ../pages/user/cart.php");
    exit();
}