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
    if(isset($_POST['stdid'])) {
        $Student_Id = $_POST['stdid'];

        include './connection.php';

        $qrydelete = mysqli_query($con, "DELETE FROM studinfo WHERE Student_Id='$Student_Id'");

        if ($qrydelete) {
            echo "Record has been deleted!";
        } else {
            echo "Error " . mysqli_error($con);
        }
    } else {
        echo "Intruder";
    }
?>