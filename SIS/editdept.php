<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['gatepass'])) {
        header("Location: ./index.php");
        exit();
    }
?>

<?php // 11 Mar 2025
    // to check if there is a value in the id
    if (!isset($_GET['department_id'])) {
        echo "Intruder!";
        exit();
    }

    // get the id
    $department_id = $_GET['department_id'];

    // connect to the database and query the user
    include './connection.php';

    $qry = mysqli_query($con, "SELECT * FROM department WHERE department_id = '$department_id'");
    $row = mysqli_fetch_array($qry);

    // assign the values to variables
    $department_name_edit = $row['department_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Department Information</title>
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
        <h1>Edit Course</h1>

        <div class="form-group">
            <label for="txtdepartment">Enter Course</label>
            <input type="text" 
                name="txtdepartment" 
                id="txtdepartment"
                value="<?php echo $department_name_edit; ?>">
        </div>

        <button id="btnsubmit" editdept="<?php echo $department_id; ?>">Update</button>

    <script src="./jquery.js"></script>

    <script>
        $(document).on('click', '#btsubmit', function () {
        /* 13 Mar 2025 */
        let editdept = $(this).attr('editdept');

        let txtdepartment = $("#txtdepartment").val();

        if (txtdepartment === "") {
            alert("Course is required!");
        } else {
            /* 13 Mar 2025 */

            $.ajax({
                url: './update_dept.php',
                method: 'POST',
                data: {

                    editdept: editdept,
                    txtdepartment: txtdepartment,
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