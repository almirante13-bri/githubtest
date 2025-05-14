<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['gatepass'])) {
        header("Location: ./index.php");
        exit();
    }
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="dashtyle.css" rel="stylesheet">
    
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

    <h1>Main Menu</h1>
    <div class="card-grid">

        <div class="card student">
            <h2>Student</h2>
            <p>Manage student information</p>
            <img src="image/student.png" alt="Student Icon">
            <a href="list_of_students.php">Student Info</a>
        </div>

        <div class="card users">
            <h2>Users</h2>
            <p>Manage user accounts</p>
            <img src="image/users.png" alt="User Icon">
            <a href="list_of_user.php">User Info</a>
        </div>

        <div class="card add-student">
            <h2>Add Student</h2>
            <p>Insert new student record</p>
            <img src="image/addst.png" alt="Add Student Icon">
            <a href="add_studentinfo.php">Add Student</a>
        </div>

        <div class="card add-user">
            <h2>Add User</h2>
            <p>Create a new user/admin</p>
            <img src="image/addus.png" alt="Add User Icon">
            <a href="add_user_account.php">Add User</a>
        </div>

    </div>



    
</body>
</html>

