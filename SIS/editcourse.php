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
    if (!isset($_GET['course_id'])) {
        echo "Intruder!";
        exit();
    }

    // get the id
    $course_id = $_GET['course_id'];

    // connect to the database and query the user
    include './connection.php';

    $qry = mysqli_query($con, "SELECT * FROM course WHERE course_id = '$course_id'");
    $row = mysqli_fetch_array($qry);

    // assign the values to variables
    $course_name_edit = $row['course_name'];
    $credit_edit = $row['credit'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User Account</title>
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
            <label for="txtcoursename">Enter Course</label>
            <input type="text" 
                name="txtcoursename" 
                id="txtcoursename"
                value="<?php echo $course_name_edit; ?>">
        </div>
    
        <div class="form-group">
            <label for="txtcredit">Enter Course Credit</label>
            <input type="text" name="txtcredit" id="txtcredit">
        </div>

        <!-- 13 Mar 2025 -->
        <button id="btnsubmit" editcourseid="<?php echo $course_id; ?>">Update</button>
    </div>

    <script src="./jquery.js"></script>

    <script>
        $(document).on('click', '#btnsubmit', function () {
        /* 13 Mar 2025 */
        let editcourseid = $(this).attr('editcourseid');

        let txtcoursename = $("#txtcoursename").val();
        let txtcredit = $("#txtcredit").val();

        if (txtcoursename === "") {
            alert("Course is required!");
        } else if (txtcredit === "") {
            alert("Course Credit is required!");
        } else {
            /* 13 Mar 2025 */

            $.ajax({
                url: './update_course.php',
                method: 'POST',
                data: {

                    editcourseid: editcourseid,
                    txtcoursename: txtcoursename,
                    txtcredit: txtcredit
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