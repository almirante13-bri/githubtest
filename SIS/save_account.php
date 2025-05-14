<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['gatepass'])) {
        header("Location: ./index.php");
        exit();
    }
?>


<?php
    if (
        isset($_POST['txtusername']) &&
        isset($_POST['txtpassword']) &&
        isset($_POST['selectrole']) 
    ) {
        $txtusername = $_POST['txtusername'];
        $txtpassword = md5($_POST['txtpassword']);
        $selectrole = $_POST['selectrole'];

        include './connection.php';

        $query = mysqli_query($con,
        "INSERT INTO users(username, password, role_id)
        VALUES('$txtusername', '$txtpassword', '$selectrole')");

        if ($query) {
             echo "Record successfully saved!";
        } else {
            echo "Error";
        }       
    } else {
        echo "Intruder!";
    }
?>