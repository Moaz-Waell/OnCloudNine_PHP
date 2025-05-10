<?php
header('Content-Type: application/json');
include('config.php');

if (isset($_GET['search'])) {
    $search_term = '%' . $_GET['search'] . '%'; // No need for real_escape_string

    $stmt = $con->prepare("
        SELECT m.*, c.CATEGORY_ID 
        FROM MEAL m
        JOIN CATEGORY c ON m.CATEGORY_ID = c.CATEGORY_ID
        WHERE m.MEAL_Name LIKE ?
        ORDER BY m.MEAL_Name ASC
    ");
    $stmt->bind_param("s", $search_term);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $meals = [];

        while ($row = $result->fetch_assoc()) {
            $meals[] = [
                'MEAL_ID' => $row['MEAL_ID'],
                'MEAL_Name' => htmlspecialchars($row['MEAL_Name']),
                'MEAL_Description' => htmlspecialchars($row['MEAL_Description']),
                'MEAL_Price' => $row['MEAL_Price'],
                'CATEGORY_ID' => $row['CATEGORY_ID'],
                'MEAL_Icon' => htmlspecialchars($row['MEAL_Icon'])
            ];
        }
        echo json_encode($meals);
    } else {
        echo json_encode(['error' => 'Database query failed']);
    }
    exit();
}

echo json_encode([]);
?>