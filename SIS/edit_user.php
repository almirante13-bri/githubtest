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
    if (!isset($_GET['id'])) {
        echo "Intruder!";
        exit();
    }

    // get the id
    $id = $_GET['id'];

    // connect to the database and query the user
    include './connection.php';

    $qry = mysqli_query($con, "SELECT * FROM users WHERE id = '$id'");
    $row = mysqli_fetch_array($qry);

    // assign the values to variables
    $username_edit = $row['username'];
    $password_edit = $row['password'];
    $role_id_edit = $row['role_id'];
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
    <h1>Edit User Account</h1>

    <div class="form-group">
        <label for="txtusername">Enter Username</label>
        <input 
            type="text" 
            name="txtusername" 
            id="txtusername"
            readonly
            value="<?php echo $username_edit; ?>"
        >
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
        <label for="selectrole">Select Role</label>
        <select name="selectrole" id="selectrole">
            <option value="">- Select Role -</option>
            <option value="1" <?php echo $role_id_edit === "1" ? "selected" : "" ?> >Super</option>
            <option value="2" <?php echo $role_id_edit === "2" ? "selected" : "" ?> >Admin</option>
            <option value="3" <?php echo $role_id_edit === "3" ? "selected" : "" ?> >Registrar</option>
            <option value="4" <?php echo $role_id_edit === "4" ? "selected" : "" ?> >Teacher</option>
            <option value="5" <?php echo $role_id_edit === "5" ? "selected" : "" ?> >Principal</option>
        </select>
    </div>

    <!-- 13 Mar 2025 -->
    <button id="btnsubmit" editid="<?php echo $id; ?>">Update</button>

    <script src="./jquery.js"></script>

    <script>
        $(document).on('click', '#btnsubmit', function () {
        /* 13 Mar 2025 */
        let editid = $(this).attr('editid');

        let txtusername = $("#txtusername").val();
        let txtpassword = $("#txtpassword").val();
        let txtconfirmpassword = $("#txtconfirmpassword").val();
        let selectrole = $("#selectrole").val();

        if (txtusername === "") {
            alert("Username is required!");
        } else if (txtpassword === "") {
            alert("Password is required!");
        } else if (txtconfirmpassword === "") {
            alert("Confirm password is required!");
        } else if (selectrole === "") {
            alert("Select a role!");
        } else if (txtpassword.length < 6) {
            alert("Password must be 6 characters and above!");
        } else if (txtpassword !== txtconfirmpassword) {
            alert("Password mismatched!");
        } else {
            /* 13 Mar 2025 */

            $.ajax({
                url: './update_account.php',
                method: 'POST',
                data: {

                    editid: editid,
                    txtpassword: txtpassword,
                    selectrole: selectrole
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