<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "stmcp_db"; // palitan mo ng actual name ng database niyo

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
