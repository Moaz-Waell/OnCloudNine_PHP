<?php
// Connection to Database Code
$Host = 'localhost';
$Username = 'root';
$Password = '';
$DB = 'OCN';

//$con = mysqli_connect($Host, $Username, $Password, $DB);
//    if($con == true)
// 	   echo 'Connected Successfully';
//    else
// 	   die ('Error in Connection'.mysqli_error($con));
   
//$con = mysqli_connect($Host, $Username, $Password, $DB) or die('Error ' . mysqli_error($con));
$con = mysqli_connect($Host, $Username, $Password, $DB);

if (!$con) {
    die('Error: ' . mysqli_connect_error());
}
?>

