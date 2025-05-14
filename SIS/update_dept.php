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
        isset($_POST['editdept']) &&
        isset($_POST['txtdepartment'])
    ) {
        $editdept = $_POST['editdept'];
        $txtdepartment = $_POST['txtdepartment'];

        include './connection.php';

        $query = mysqli_query($con,
        "UPDATE department 
        SET department_name = '$txtdepartment'
        WHERE department_id = '$editdept'");

        if ($query) {
            echo "Record successfully updated!";
        } else {
            echo "Error";
        }
    } else {
        echo "Intruder!";
    }
?>

