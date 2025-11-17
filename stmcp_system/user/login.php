<?php
require_once '../connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Fetch user from DB
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Plain password check
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            header("Location: merchandise.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<style>
body { font-family: Arial; background:#f2f2f2; padding:40px; }
.login-box {
    width: 300px; margin:auto; background:white; padding:20px;
    border-radius:8px; box-shadow:0px 0px 10px rgba(0,0,0,0.1);
}
input { width:100%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:5px; }
button { width:100%; padding:10px; background:#2196F3; color:white; border:none; border-radius:5px; font-weight:bold; cursor:pointer; }
button:hover { background:#1976d2; }
.error { color:red; text-align:center; }
</style>
</head>
<body>

<div class="login-box">
    <h2>Login</h2>

    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
