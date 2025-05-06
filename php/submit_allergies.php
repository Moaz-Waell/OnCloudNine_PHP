<?php
session_start();
include('config.php');

if (!isset($_COOKIE['user_id'])) {
    die("Unauthorized.");
}

$user_id = $_COOKIE['user_id'];

// Delete existing allergies
// $con->query("DELETE FROM USER_ALLERGIES WHERE USERS_ID = $user_id");

if (isset($_POST['submit'])) {
    if (!empty($_POST['allergies'])) {
        foreach ($_POST['allergies'] as $allergy_id) {
            $stmt = $con->prepare("INSERT INTO USER_ALLERGIES (USERS_ID, ALLERGY_ID, Has_Allergy) VALUES (?, ?, 'Yes')");
            $stmt->bind_param("ii", $user_id, $allergy_id);
            $stmt->execute();
            $stmt->close();
        }
    }
} elseif (isset($_POST['no_allergies'])) {
    // Add 'No Allergies' entry
    $result = $con->query("SELECT ALLERGY_ID FROM ALLERGY WHERE ALLERGY_Name = 'No Allergies'");
    if ($result->num_rows == 0) {
        $con->query("INSERT INTO ALLERGY (ALLERGY_Name) VALUES ('No Allergies')");
        $noneId = $con->insert_id;
    } else {
        $noneId = $result->fetch_assoc()['ALLERGY_ID'];
    }

    $stmt = $con->prepare("INSERT INTO USER_ALLERGIES (USERS_ID, ALLERGY_ID, Has_Allergy) VALUES (?, ?, 'No')");
    $stmt->bind_param("ii", $user_id, $noneId);
    $stmt->execute();
    $stmt->close();
}

header("Location: ../pages/user/home.php");
exit();
?>