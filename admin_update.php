<?php
// admin_update.php
session_start();
include 'db_connect.php';

// protect route
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_name = trim(mysqli_real_escape_string($conn, $_POST['shop_name']));
    $total_rooms = (int) ($_POST['total_rooms'] ?? 0);
    $occupied_rooms = (int) ($_POST['occupied_rooms'] ?? 0);
    if ($total_rooms < 0) $total_rooms = 0;
    if ($occupied_rooms < 0) $occupied_rooms = 0;
    if ($occupied_rooms > $total_rooms) $occupied_rooms = $total_rooms;

    // Prevent duplicates: upsert into shop_status (check by shop_name)
    $check = $conn->prepare("SELECT shop_name FROM shop_status WHERE shop_name = ?");
    $check->bind_param('s', $shop_name);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        // update
        $stmt = $conn->prepare("UPDATE shop_status SET total_rooms=?, occupied_rooms=?, updated_at = NOW() WHERE shop_name = ?");
        $stmt->bind_param('iis', $total_rooms, $occupied_rooms, $shop_name);
        $ok = $stmt->execute();
        $stmt->close();
        $message = $ok ? "Shop updated successfully." : "Error updating shop.";
    } else {
        // insert
        $stmt = $conn->prepare("INSERT INTO shop_status (shop_name, total_rooms, occupied_rooms) VALUES (?, ?, ?)");
        $stmt->bind_param('sii', $shop_name, $total_rooms, $occupied_rooms);
        $ok = $stmt->execute();
        $stmt->close();
        $message = $ok ? "Shop added successfully." : "Error adding shop.";
    }
    $check->close();

    // Recalculate queue for this shop so customer estimates and availability update
    include 'update_queue.php';
    recalcQueueForShop($conn, $shop_name);

    // Redirect to admin_dashboard to view table
    header("Location: admin_dashboard.php");
    exit();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Admin - Update Shop</title>
<style>
/* Minimal clean container */
body { font-family: Poppins, Arial, sans-serif; margin:0; display:flex; align-items:center; justify-content:center; height:100vh; background: #f3f6f9;background:url('https://www.retailtouchpoints.com/wp-content/uploads/2020/12/changing-room.jpg') }
.box { width:420px; background: #fff; padding:28px; border-radius:12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
h2 { margin-top:0; text-align:center; color:#333; }
input, button { width:100%; padding:12px; margin:10px 0; border-radius:8px; border:1px solid #ddd; font-size:15px; }
button { background:#0078d7; color:#fff; border:none; cursor:pointer; }
.msg { color:green; text-align:center; margin-top:10px; }
.small { font-size:13px; color:#666; text-align:center; margin-top:6px; }
</style>
</head>
<body>
<div class="box">
  <h2>Enter / Update Shop Details</h2>

  <form method="post" action="">
    <input type="text" name="shop_name" placeholder="Shop Name" required>
    <input type="number" name="total_rooms" placeholder="Total Trial Rooms" min="1" required>
    <input type="number" name="occupied_rooms" placeholder="Occupied Rooms" min="0" required>
    <button type="submit">Save / Update</button>
  </form>

  <?php if ($message): ?>
    <div class="msg"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>

  <div class="small">
    <form action="admin_dashboard.php" method="get">
      <button type="submit" style="background:#e74c3c;margin-top:12px;">Back to Dashboard</button>
    </form>
  </div>
</div>
</body>
</html>
