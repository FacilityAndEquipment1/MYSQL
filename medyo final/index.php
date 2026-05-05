<?php
session_start();
include 'db.php';

if (isset($_POST['login_btn'])) {
    $u = mysqli_real_escape_string($conn, $_POST['username']);
    $p = mysqli_real_escape_string($conn, $_POST['password']);
    $r = $_POST['role'];


    $sql = "SELECT * FROM users WHERE username='$u' AND password='$p' AND role='$r'";
    $res = mysqli_query($conn, $sql);

    if (mysqli_num_rows($res) > 0) {
        $row = mysqli_fetch_assoc($res);
        
       
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($row['role'] == 'admin') {
            header("Location: admin.php");
            exit();
        } elseif ($row['role'] == 'manager') {
            header("Location: manager.php");
            exit();
        } else {
            header("Location: user.php");
            exit();
        }
    } else {
        echo "<script>alert('Invalid Account! Check your details.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ICT Help Desk - Login</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="card">
        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="color: #f5ecec; margin-bottom: 5px;">ICT Help Desk</h2>
            <p style="color: #64748b; font-size: 14px;">Sign in to your account</p>
        </div>

        <form method="POST">
            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            
            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="form-group">
                <label style="font-size: 13px; color: #475569;">Login as:</label>
                <select name="role" required>
                    <option value="user">User</option>
                    <option value="admin">Administrator</option>
                    <option value="manager">Manager</option>
                </select>
            </div>

            <button type="submit" name="login_btn" class="btn-login">LOGIN</button>
        </form>
    </div>
</body>
</html>