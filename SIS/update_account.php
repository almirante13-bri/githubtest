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
        isset($_POST['editid']) &&
        isset($_POST['txtpassword']) &&
        isset($_POST['selectrole'])
    ) {
        $editid = $_POST['editid'];
        $txtpassword = md5($_POST['txtpassword']);
        $selectrole = $_POST['selectrole'];

        include './connection.php';

        $query = mysqli_query($con,
        "UPDATE users
        SET password = '$txtpassword', role_id='$selectrole'
        WHERE id = '$editid'");

        if ($query) {
            echo "Record successfully updated!";
        } else {
            echo "Error";
        }
    } else {
        echo "Intruder!";
    }
?>