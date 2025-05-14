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
        isset($_POST['editcourseid']) &&
        isset($_POST['txtcoursename']) &&
        isset($_POST['txtcredit'])
    ) {
        $editcourseid = $_POST['editcourseid'];
        $txtcoursename = $_POST['txtcoursename'];
        $txtcredit = $_POST['txtcredit'];

        include './connection.php';

        $query = mysqli_query($con,
        "UPDATE course 
        SET course_name = '$txtcoursename', credit='$txtcredit'
        WHERE course_id = '$editcourseid'");

        if ($query) {
            echo "Record successfully updated!";
        } else {
            echo "Error";
        }
    } else {
        echo "Intruder!";
    }
?>

