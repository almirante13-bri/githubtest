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
    if(isset($_POST['deptid'])) {
        $department_id = $_POST['deptid'];

        include './connection.php';

        $qrydelete = mysqli_query($con, "DELETE FROM department WHERE department_id='$department_id'");

        if ($qrydelete) {
            echo "Record has been deleted!";
        } else {
            echo "Error " . mysqli_error($con);
        }
    } else {
        echo "Intruder";
    }
?>