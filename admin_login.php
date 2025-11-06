<?php
// admin_login.php
session_start();
require 'db_connect.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter username and password.';
    } else {
        $sql = "SELECT * FROM admins WHERE username='$username' AND password='$password' LIMIT 1";
        $res = mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) === 1) {
            $_SESSION['admin_email'] = $username;
            header('Location: admin_update.php'); exit;
        } else {
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin Login</title>
<style>
body{font-family:Arial;margin:0;height:100vh;padding-top:120px;background-size:cover;overflow: hidden;background-attachment: fixed;background:url('https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1400&q=80') center/cover no-repeat}
body::before{content:"";position:fixed;inset:0;background:rgba(0,0,0,0.45)}
.form{position:relative;z-index:1;width:420px;margin:90px auto;padding:36px;background:rgba(255,255,255,0.95);border-radius:12px;box-shadow:0 12px 30px rgba(0,0,0,.2);text-align:center}
input{width:100%;padding:12px;margin:8px 0;border:1px solid #ccc;border-radius:8px}
button{width:100%;padding:12px;background:#0078D7;color:#fff;border:none;border-radius:8px;cursor:pointer}
.error{color:#b00020;margin-top:8px}
a{display:block;margin-top:10px;color:#0078D7;text-decoration:none}
</style>
</head>
<body>
<div class="form">
  <h2>Admin Login</h2>
  <form method="post">
    <input name="username" placeholder="Username" autofocus>
    <input name="password" type="password" placeholder="Password">
    <button type="submit">Login</button>
  </form>
  <?php if ($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
  <a href="index.php">← Back</a>
</div>
</body>
</html>
