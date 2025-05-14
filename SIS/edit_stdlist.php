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
    if (!isset($_GET['Student_Id'])) {
        echo "Intruder!";
        exit();
    }

    $Student_Id = $_GET['Student_Id'];

    include './connection.php';

    $qry = mysqli_query($con, "SELECT * FROM studinfo WHERE Student_Id = '$Student_Id'");
    $row = mysqli_fetch_array($qry);

    $First_Name_edit = $row['First_Name'];
    $Last_Name_edit = $row['Last_Name'];
    $Date_of_Birth_edit = $row['Date_of_Birth'];
    $Gender_edit = $row['Gender'];
    $Email_edit = $row['Email'];
    $Phone_Number_edit = $row['Phone_Number'];
    $Address_edit = $row['Address'];
    $Course_edit = $row['Course'];
    $Enrollment_Date_edit = $row['Enrollment_Date'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Information</title>
    <link href="dashtyle.css" rel="stylesheet">
    <link href="addformstyle.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <ul class="nav-list">
        <li><a href="list_of_students.php">Student</a></li>
        <li><a href="list_of_course.php">Course</a></li>
        <li><a href="list_of_department.php">Department</a></li>
        <li><a href="setcontent.php">Settings</a></li>
        </ul>
        <div class="admin-info">
        <a href="dashboard.php">
            <img src="image/admin.png" alt="Admin Icon" class="admin-icon">
        </a>
        <span class="admin-label">Admin</span>
        </div>
    </nav>

    <div class="form-container">
        <h1>Edit Student Information</h1>

        <div class="form-group">
            <label for="txtfirstname">Enter Firstname</label>
            <input 
                type="text" 
                name="txtfirstname"
                id="txtfirstname"
                readonly
                value="<?php echo $First_Name_edit; ?>"
            >

        <div class="form-group">
        <label for="txtlastname">Enter Lastname</label>
        <input type="text" name="txtlastname" id="txtlastname" placeholder="Enter your Lastname" >

        
        <div class="form-group">
        <label for="dob_birth">Date of Birth:</label>
        <input type="date" id="dob_birth" name="dob_birth">

        <div class="form-group">
        <label for="selectgender">selectgender</label>
            <select id="selectgender">
                <option value="">Select Gender</option>
                <option value="Male" <?php echo $Gender_edit === "1" ? "selected" : "" ?> >Male</option>
                <option value="Female" <?php echo $Gender_edit === "2" ? "selected" : "" ?> >Female</option>
            </select>

        
        <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your Email">

        <div class="form-group">
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" placeholder="Enter your Phone">

        <div class="form-group">
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" placeholder="Enter your Address">

        <div class="form-group">
        <label for="selectcourse">selectcourse</label>
        <select id="selectcourse">
            <option value="">Select Course</option>
            <option value="BSIT" <?php echo $Course_edit === "1" ? "selected" : "" ?>>BSIT</option>
            <option value="BSHM" <?php echo $Course_edit === "2" ? "selected" : "" ?>>BSHM</option>
            <option value="BSBA" <?php echo $Course_edit === "3" ? "selected" : "" ?>>BSBA</option>
            <option value="BSCS" <?php echo $Course_edit === "4" ? "selected" : "" ?>>BSCS</option>
        </select>

        <div class="form-group">
            <label for="enrollment_date">Enrollment Date:</label>
            <input type="date" id="enrollment_date" name="enrollment_date">
        </div>

        <button id="btnsubmit" editStudent_Id="<?php echo $Student_Id; ?>">Update</button>
    </div>
    <script src="./jquery.js"></script>

    <script>
        $(document).on('click', '#btnsubmit', function () {
        let editStudent_Id = $(this).attr('editStudent_Id');

        let txtfirstname = $("#txtfirstname").val();
        let txtlastname = $("#txtlastname").val();
        let dob_birth = $("#dob_birth").val(); 
        let selectgender = $("#selectgender").val();
        let email = $("#email").val();
        let phone = $("#phone").val();
        let address = $("#address").val();
        let selectcourse = $("#selectcourse").val();
        let enrollment_date = $("#enrollment_date").val();
        
        if (txtfirstname === "") {	
            alert("First Name is required!");
        }else if (txtlastname === "") {
            alert("Last Name is required!");
		}else if (dob_birth === "") {
            alert("Date of Birth is required!");
        }else if (selectgender === "") {
            alert("Please select a gender!");
        }else if (email === "") {
            alert("Email is required!");
        }else if (phone === "") {
            alert("Phone number is required!");
        }else if (address === "") {
            alert("Address is required!");
        }else if (selectcourse === "") {
            alert("Please select a course!");
        }else if (enrollment_date === "") {
            alert("Enrollment Date is required!");
		}else {

            $.ajax({
                url: './update_stdinfo.php',
                method: 'POST',
                data: {

                    editStudent_Id: editStudent_Id,
                    txtfirstname: txtfirstname,
					txtlastname: txtlastname,
					dob_birth: dob_birth,
					selectgender: selectgender,
					email: email,
					phone: phone,
					address: address,
					selectcourse: selectcourse,
					enrollment_date: enrollment_date
                },
                success: function(response) {
                    alert(response);
                    location.reload();
                }
            });
        }
    });
    </script>
</body>
</html>