<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "sis";

$con = mysqli_connect($host, $username, $password, $database);

if ($con) {
     // echo "CONNECTED";
 }else {
     echo "ERROR";
 }

?>