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
        isset($_POST['txtdepartment'])
    ) {
        $txtdepartment = $_POST['txtdepartment'];

        include './connection.php';

        $query = mysqli_query($con,
        "INSERT INTO department(department_name)
        VALUES('$txtdepartment')");

        if ($query) {
             echo "Record successfully saved!";
        } else {
            echo "Error";
        }       
    } else {
        echo "Intruder!";
    }
?>