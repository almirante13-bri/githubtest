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
        "INSERT INTO studinfo(First_Name, Last_Name, Date_of_Birth, Gender, Email, Phone_Number, Address, Course, Enrollment_Date)
        VALUES('$txtfirstname', '$txtlastname', '$dob_birth', '$selectgender', '$email', '$phone', '$address', '$selectcourse', '$enrollment_date' )");

        if ($query) {
             echo "Record successfully saved!";
        } else {
            echo "Error";
        }       
    } else {
        echo "Intruder!";
    }
?>