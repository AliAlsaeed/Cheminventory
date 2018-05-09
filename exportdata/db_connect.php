<?php
/* Database connection start */
$servername = "localhost";
$username = "cheminve_ali";
$password = "t00532799?";
$dbname = "cheminve_main";
$conn = mysqli_connect($servername, $username, $password, $dbname) or die("Connection failed: " . mysqli_connect_error());
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
?>