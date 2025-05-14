<?php
if (isset($_POST['txtusername']) && isset($_POST['txtpassword'])) {
    $txtusername = $_POST['txtusername'];
    $txtpassword = md5($_POST['txtpassword']);

    include './connection.php';

    $query = mysqli_query($con,
    "SELECT * FROM users WHERE username = '$txtusername' 
    AND password = '$txtpassword'");

    if (mysqli_num_rows($query) > 0) {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['gatepass'] = "123";

        echo "success";
    } else {
        echo "Invalid username and password!";
    }
} else {
    echo "Intruder!";
}
?>