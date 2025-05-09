<?php
session_start();
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['attendance']);
unset($_SESSION['phone']);
session_destroy();
header("Location: ../pages/aast/uniUserLogin.php");
exit();
?>