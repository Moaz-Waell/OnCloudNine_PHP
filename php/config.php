<?php
//config.php
// Connection to Database Code
$Host = 'localhost';
$Username = 'root';
$Password = '';
$DB = 'ocn';

$con = mysqli_connect($Host, $Username, $Password, $DB);

if (!$con) {
    die('Error: ' . mysqli_connect_error());
}
?>

