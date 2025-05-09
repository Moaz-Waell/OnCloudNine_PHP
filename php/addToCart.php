<?php
session_start();
include('config.php');

// Validate user and input
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../pages/aast/uniUserLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$meal_id = filter_input(INPUT_POST, 'meal_id', FILTER_VALIDATE_INT);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT) ?: 1;
$submitted_ingredients = $_POST['ingredients'] ?? [];

// Validate meal exists
$stmt = $con->prepare("SELECT MEAL_ID FROM MEAL WHERE MEAL_ID = ?");
$stmt->bind_param("i", $meal_id);
$stmt->execute();
if ($stmt->get_result()->num_rows !== 1) {
    header("Location: home.php");
    exit();
}
$stmt->close();

// Get all meal ingredients
$stmt = $con->prepare("SELECT I.INGREDIENT_NAME 
                      FROM MEAL_INGREDIENTS MI
                      JOIN INGREDIENTS I ON MI.INGREDIENT_ID = I.INGREDIENT_ID
                      WHERE MI.MEAL_ID = ?");
$stmt->bind_param("i", $meal_id);
$stmt->execute();
$result = $stmt->get_result();

$all_ingredients = [];
while ($row = $result->fetch_assoc()) {
    $all_ingredients[] = $row['INGREDIENT_NAME'];
}
$stmt->close();

// Determine excluded ingredients
$excluded = array_diff($all_ingredients, $submitted_ingredients);
$note = !empty($excluded) ? "Exclude: " . implode(', ', $excluded) : '';

// Check existing cart item
$stmt = $con->prepare("SELECT CART_ID, QUANTITY FROM CART 
                      WHERE USERS_ID = ? 
                      AND MEAL_ID = ? 
                      AND NOTE = ?");
$stmt->bind_param("iis", $user_id, $meal_id, $note);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update existing entry
    $row = $result->fetch_assoc();
    $new_quantity = $row['QUANTITY'] + $quantity;

    $update = $con->prepare("UPDATE CART SET QUANTITY = ? WHERE CART_ID = ?");
    $update->bind_param("ii", $new_quantity, $row['CART_ID']);
    $update->execute();
    $update->close();
} else {
    // Create new entry
    $insert = $con->prepare("INSERT INTO CART (USERS_ID, MEAL_ID, NOTE, QUANTITY) 
                            VALUES (?, ?, ?, ?)");
    $insert->bind_param("iisi", $user_id, $meal_id, $note, $quantity);
    $insert->execute();
    $insert->close();
}

// After processing cart update:
if (isset($_POST['return_url']) && filter_var($_POST['return_url'], FILTER_VALIDATE_URL)) {
    // Validate the return URL is from your domain
    $return_url = $_POST['return_url'];
    $parsed = parse_url($return_url);

    if ($parsed['host'] === $_SERVER['HTTP_HOST']) {
        header("Location: " . $return_url);
        exit();
    }
}
header("Location: ../pages/user/home.php");
exit();
?>