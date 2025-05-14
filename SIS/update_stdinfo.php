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
        isset($_POST['editStudent_Id']) &&
        isset($_POST['txtfirstname']) &&
        isset($_POST['txtlastname']) &&
        isset($_POST['dob_birth']) &&
        isset($_POST['selectgender']) &&
        isset($_POST['email']) &&
        isset($_POST['phone']) &&
        isset($_POST['address']) &&
        isset($_POST['selectcourse']) &&
        isset($_POST['enrollment_date']) 
    ) {
        $editStudent_Id = $_POST['editStudent_Id'];
        $txtfirstname = $_POST['txtfirstname'];
        $txtlastname = $_POST['txtlastname'];
        $dob_birth = $_POST['dob_birth'];
        $selectgender = $_POST['selectgender'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $selectcourse = $_POST['selectcourse'];
        $enrollment_date = $_POST['enrollment_date'];

        include './connection.php';

        $query = mysqli_query($con,
        "UPDATE studinfo
        SET First_Name = '$txtfirstname', Last_Name = '$txtlastname', Date_of_Birth = '$dob_birth', Gender = '$selectgender',
        Email = '$email', Phone_Number = '$phone', Address = '$address', Course = '$selectcourse',
        Enrollment_Date = '$enrollment_date'
        WHERE Student_Id = '$editStudent_Id'");

        if ($query) {
            echo "Record successfully updated!";
        } else {
            echo "Error";
        }
    } else {
        echo "Intruder!";
    }
?>

