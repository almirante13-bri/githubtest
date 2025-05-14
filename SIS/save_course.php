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
        isset($_POST['txtcoursename']) &&
        isset($_POST['txtcredit'])
    ) {
        $txtcoursename = $_POST['txtcoursename'];
        $txtcredit = $_POST['txtcredit'];

        include './connection.php';

        $query = mysqli_query($con,
        "INSERT INTO course(course_name, credit)
        VALUES('$txtcoursename', '$txtcredit')");

        if ($query) {
             echo "Record successfully saved!";
        } else {
            echo "Error";
        }       
    } else {
        echo "Intruder!";
    }
?>