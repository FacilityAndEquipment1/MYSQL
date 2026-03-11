<?php
session_start();
include 'db.php';
if (isset($_POST['login_btn'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = $_POST['password'];
    $r = $_POST['role'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE username='$u' AND password='$p' AND role='$r'");
    if (mysqli_num_rows($res) > 0) {
        $_SESSION['username'] = $u;
        $_SESSION['role'] = $r;
        header("Location: " . ($r == 'admin' ? 'admin.php' : 'user.php'));
    } else { echo "<script>alert('Incorrect details!');</script>"; }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="card">
        <h2> ICT Hekp Desk Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <select name="role">
                <option value="user">User/Student</option>
                <option value="admin">Administrator</option>
            </select>
            <button type="submit" name="login_btn" class="btn-login">LOGIN</button>
        </form>
    </div>
</body>
</html>