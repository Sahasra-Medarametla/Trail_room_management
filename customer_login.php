<?php
// customer_login.php
session_start();
require 'db_connect.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $error = 'Please enter email and password.';
    } else {
        $sql = "SELECT * FROM customers WHERE email='$email' AND password='$password' LIMIT 1";
        $res = mysqli_query($conn, $sql);
        if ($res && mysqli_num_rows($res) === 1) {
            $_SESSION['email'] = $email;

            // Prevent duplicate waiting entries
            $chk = "SELECT * FROM queue WHERE customer_email='$email' AND status='Waiting' LIMIT 1";
            $chkres = mysqli_query($conn, $chk);
            if (!$chkres || mysqli_num_rows($chkres) === 0) {
                // default shop_name can be chosen later by customer; for now use placeholder
                $shop_name = 'TrendyWear';

                // compute next position for that shop
                $posq = "SELECT COUNT(*) as total FROM queue WHERE shop_name='$shop_name' AND status='Waiting'";
                $posr = mysqli_query($conn, $posq);
                $posrow = mysqli_fetch_assoc($posr);
                $position = (int)$posrow['total'] + 1;

                $estimated = (($position - 1) * 5) . " minutes"; // 5 min per person
                $ins = "INSERT INTO queue (customer_email, shop_name, position_in_queue, status, estimated_time) 
                        VALUES ('$email', '$shop_name', $position, 'Waiting', '$estimated')";
                mysqli_query($conn, $ins);
            }

            header('Location: customer_dashboard.php'); exit;
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
<title>Customer Login</title>
<style>
body{font-family:Arial;margin:0;height:100vh;padding-top:120px;background-size:cover;overflow: hidden;background-attachment: fixed;background:url('https://images.unsplash.com/photo-1522199710521-72d69614c702') center/cover no-repeat}
body::before{content:"";position:fixed;inset:0;background:rgba(0,0,0,0.45)}
.form{position:relative;z-index:1;width:420px;margin:90px auto;padding:36px;background:rgba(255,255,255,0.95);border-radius:12px;box-shadow:0 12px 30px rgba(0,0,0,.2);text-align:center}
input{width:100%;padding:12px;margin:8px 0;border:1px solid #ccc;border-radius:8px}
button{width:100%;padding:12px;background:#28a745;color:#fff;border:none;border-radius:8px;cursor:pointer}
.error{color:#b00020;margin-top:8px}
a{display:block;margin-top:10px;color:#0078D7;text-decoration:none}
</style>
</head>
<body>
<div class="form">
  <h2>Customer Login</h2>
  <form method="post">
    <input name="email" type="email" placeholder="Email" required>
    <input name="password" type="password" placeholder="Password" required>
    <button type="submit">Login</button>
  </form>
  <?php if ($error): ?><div class="error"><?=htmlspecialchars($error)?></div><?php endif; ?>
  <a href="index.php">← Back</a>
</div>
</body>
</html>
