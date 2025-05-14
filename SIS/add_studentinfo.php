<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['gatepass'])) {
        header("Location: ./index.php");
        exit();
    }
?>

<!DOCTYPE  html>
<html>
	<head>
		<title>Add Student Information</title>
		<link href="dashtyle.css" rel="stylesheet">
		<link href="addformstyle.css" rel="stylesheet">
	</head>
	<body>
		<nav class="navbar">
			<ul class="nav-list">
				<li><a href="list_of_students.php">Student</a></li>
				<li><a href="student.php">Course</a></li>
				<li><a href="student.php">Department</a></li>
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
			<h1>Add Student Information</h1>
			
			<div class="form-group">
				<label for="txtfirstname">Enter Firstname</label>
				<input type="text" name="txtfirstname" id="txtfirstname" placeholder="Enter your Firstname" required>
			</div>
				
			<div class="form-group">
				<label for="txtlastname">Enter Lastname</label>
				<input type="text" name="txtlastname" id="txtlastname" placeholder="Enter your Lastname" required>
			</div>

			<div class="form-group">
				<label for="dob_birth">Date of Birth:</label>
				<input type="date" id="dob_birth" name="dob_birth" required>
			</div>

			<div class="form-group">
				<label for="selectgender">selectgender</label>
				<select id="selectgender">
					<option value="">Select Gender</option>
					<option value="Male">Male</option>
					<option value="Female">Female</option>
				</select>
			</div>
			
			<div class="form-group">
				<label for="email">Email:</label>
				<input type="email" id="email" name="email" placeholder="Enter your Email" required>	
			</div>

			<div class="form-group">
				<label for="phone">Phone Number:</label>
				<input type="text" id="phone" name="phone" placeholder="Enter your Phone Number" required>
			</div>

			<div class="form-group">
				<label for="address">Address:</label>
				<input type="text" id="address" name="address" placeholder="Enter your address" required>
			</div>

			<div class="form-group">
				<label for="selectcourse">selectcourse</label>
				<select id="selectcourse">
					<option value="">Select Course</option>
					<option value="BSIT">BSIT</option>
					<option value="BSHM">BSHM</option>
					<option value="BSBA">BSBA</option>
					<option value="BSCS">BSCS</option>
				</select>
			</div>

			<div class="form-group">
				<label for="enrollment_date">Enrollment Date:</label>
				<input type="date" id="enrollment_date" name="enrollment_date" required>
			</div>

			<button id="btnsubmit">Submit</button>

		</div>
	<script src="./jquery.js"></script>
    <script>
		//algorithms
		// 1. get inputs (jquery)
		// 2. validate inputs (jquery)
		// 3. send data to server (ajax)
		// 4. receive response from server (ajax)

		// 1. get inputs (jquery)
        $('#btnsubmit').on('click', function () {
            let txtfirstname = $("#txtfirstname").val();
                let txtlastname = $("#txtlastname").val();
                let dob_birth = $("#dob_birth").val(); 
                let selectgender = $("#selectgender").val();
                let email = $("#email").val();
                let phone = $("#phone").val();
                let address = $("#address").val();
                let selectcourse = $("#selectcourse").val();
                let enrollment_date = $("#enrollment_date").val();

                console.log("First Name: " + txtfirstname);
                console.log("Last Name: " + txtlastname);
                console.log("Date of Birth: " + dob_birth);
                console.log("Gender: " + selectgender);
                console.log("Email: " + email);
                console.log("Phone: " + phone);
                console.log("Address: " + address);
                console.log("Course: " + selectcourse);
                console.log("Enrollment Date: " + enrollment_date);

		// 2. validate inputs (jquery)
		// 2.1 no emty inputs 

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
				// 3. send data to server 
				$.ajax({
					url:'./save_information.php',
					method: 'POST',
					data: {
						// receive : data that will be send
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
					success: function (response) {
						alert(response);
					}
				});
			}
        });
    </script>

</body>
</html>