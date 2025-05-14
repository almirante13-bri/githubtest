<?php // 29 Mar 2025
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    session_destroy();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="style.css" rel="stylesheet">
</head>

<body>
    <div class="loginbox">
        <h1>loginform</h1>
        <label for="txtusername">Username</label>
        <input type="text" name="" id="txtusername">
        <br><br>
        <label for="txtpassword">Password</label>
        <input type="password" name="" id="txtpassword">
        <br><br>
        <div class="button-container">
        <button id="btnlogin">Login</button>
        </div>
    </div>

    <script src="./jquery.js"></script>
    <script>
        $(document).on('click', '#btnlogin', function() {
            // 1. get inputs
            // 2. validation
            // 3. verification
            // 4. response > redirect

            // 1. get inputs
            let txtusername = $("#txtusername").val();
            let txtpassword = $("#txtpassword").val();

            // 2. validation
            if (txtusername === "" || txtpassword === "") {
                alert("Required fields!");
                return;
            }

            $.ajax({
                url: './login.php',
                method: 'POST',
                data: {
                    txtusername: txtusername,
                    txtpassword: txtpassword
                },
                success: function(response) {
                    if (response == "success") {
                        alert("Access granted!")
                        window.location.href = "./dashboard.php";
                    } else {
                        alert("Hindi sumakses!");
                    }
                }
            })
        })
    </script>
</body>
</html>