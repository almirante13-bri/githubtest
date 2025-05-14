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
		<title>Add User Account</title>
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
		<h1>Add User Account</h1>

		<div class="form-group">
			<label for="txtusername">Enter Username</label>
			<input type="text" name="txtusername" id="txtusername">
		</div>
		
		<div class="form-group">
			<label for="txtpassword">Enter Password</label>
			<input type="password" name="txtpassword" id="txtpassword">
		</div>

		<div class="form-group">	
			<label for="txtconfirmpassword">Confirm Password</label>
			<input type="password" name="txtconfirmpassword" id="txtconfirmpassword">
		</div>
			

		<div class="form-group">
			<label for="selectrole">selectrole</label>
			<select id="selectrole">
				<option value="">Select Role</option>
				<option value="1">Super</option>
				<option value="2">Admin</option>
				<option value="2">Teacher</option>
				<option value="2">Registrar</option>
			</select>
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
            let txtusername = $("#txtusername").val();
            let txtpassword = $("#txtpassword").val();
            let txtconfirmpassword = $("#txtconfirmpassword").val();
            let selectrole = $("#selectrole").val();
			
                console.log("Username :" + txtusername);
                console.log("Password :" + txtpassword);
                console.log("txtconfirmpassword:" +txtconfirmpassword);
                console.log("Role :" + selectrole);

		// 2. validate inputs (jquery)
		// 2.1 no emty inputs 

		    if (txtusername === "") {
				alert("Username is required!");
			}else if (txtpassword === ""){
				alert("Password is required!");
			}else if (txtconfirmpassword === ""){
				alert("txtconfirmpassword is required!");
			}else if (selectrole === ""){
				alert("select a role!");
			}else if (txtpassword.length <6){
				alert("Password must be 6 characters and above!");
			}else if (txtpassword !== txtconfirmpassword) {
				alert ("Password mismatched")
			}else {
				// 3. send data to server 
				$.ajax({
					url:'./save_account.php',
					method: 'POST',
					data: {
						// receive : data that will be send
						txtusername: txtusername,
						txtpassword: txtpassword,
						selectrole: selectrole
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