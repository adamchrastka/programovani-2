<?php
$servername = "localhost";
$username = "root";
$password = ""; // default in XAMPP
$dbname = "autoskola";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>  