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
    if(isset($_POST['courseid'])) {
        $course_id = $_POST['courseid'];

        include './connection.php';

        $qrydelete = mysqli_query($con, "DELETE FROM course WHERE course_id='$course_id'");

        if ($qrydelete) {
            echo "Record has been deleted!";
        } else {
            echo "Error " . mysqli_error($con);
        }
    } else {
        echo "Intruder";
    }
?>