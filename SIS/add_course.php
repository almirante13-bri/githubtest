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
		<title>Add Course</title>
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
		<h1>Add Course</h1>
		
		<div class="form-group">
			<label for="txtcoursename">Enter Course</label>
			<input type="text" name="txtcoursename" id="txtcoursename">
		</div>
		
		<div class="form-group">
			<label for="txtcredit">Enter Credit</label>
			<input type="text" name="txtcredit" id="txtcredit">
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
            let txtcoursename = $("#txtcoursename").val();
            let txtcredit = $("#txtcredit").val();
			
                console.log("course_name :" + txtcoursename);
                console.log("credit :" + txtcredit);

		// 2. validate inputs (jquery)
		// 2.1 no emty inputs 

		    if (txtcoursename === "") {
				alert("Username is required!");
			}else if (txtcredit === ""){
				alert("Password is required!");
			}else {
				// 3. send data to server 
				$.ajax({
					url:'./save_course.php',
					method: 'POST',
					data: {
						// receive : data that will be send
						txtcoursename: txtcoursename,
						txtcredit: txtcredit,
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