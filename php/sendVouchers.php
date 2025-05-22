<?php
session_start();
include ('config.php');


if (!isset($_SESSION['admin_id'])) {
    header("Location: ../pages/admin/admin_login.php");
    exit();
}

try {
    // Get all users with attendance
    $users_stmt = $con->prepare("SELECT * FROM USERS WHERE USERS_Attendance > 0");
    $users_stmt->execute();
    $users = $users_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($users as $user) {
        $attendance = $user['USERS_Attendance'];
        $voucher_type = null;

        if ($attendance >= 90) {
            $voucher_type = 1; // 30% off
        } elseif ($attendance >= 80) {
            $voucher_type = 2; // 15% off
        } elseif ($attendance >= 70) {
            $voucher_type = 3; // 5% off
        }

        if ($voucher_type) {
            // Create voucher
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime('+5 days'));

            $insert_stmt = $con->prepare("INSERT INTO USER_VOUCHERS 
                                        (USERS_ID, VOUCHER_ID, VOUCHER_StartDate, VOUCHER_EndDate)
                                        VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param(
                "iiss",
                $user['USERS_ID'],
                $voucher_type,
                $start_date,
                $end_date
            );
            $insert_stmt->execute();
        }
    }

    $_SESSION['success'] = "Vouchers sent successfully";
} catch (Exception $e) {
    $_SESSION['error'] = "Error sending vouchers: " . $e->getMessage();
}

header("Location: ../pages/admin/admin_landing.php");
exit();
?>