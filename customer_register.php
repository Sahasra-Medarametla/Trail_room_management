<?php
// customer_register.php
session_start();
include 'db_connect.php';

// ensure customer is logged in
if (!isset($_SESSION['email'])) {
    header("Location: customer_login.php");
    exit();
}
$email = $_SESSION['email'];

// accept input from previous form (shop_name)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shop_name = trim(mysqli_real_escape_string($conn, $_POST['shop_name']));

    // check shop exists
    $stmt = $conn->prepare("SELECT total_rooms, occupied_rooms FROM shop_status WHERE shop_name = ?");
    $stmt->bind_param('s', $shop_name);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        $error = "Shop not found. Please check the name.";
    } else {
        $shop = $res->fetch_assoc();
        $T = (int)$shop['total_rooms'];
        $O = (int)$shop['occupied_rooms'];
        $available = $T - $O;
        if ($available < 0) $available = 0;

        // Prevent duplicate waiting entry for same shop (if they already have a waiting entry)
        $check = $conn->prepare("SELECT * FROM queue WHERE customer_email = ? AND shop_name = ? AND status IN ('Waiting','Available')");
        $check->bind_param('ss', $email, $shop_name);
        $check->execute();
        $checkRes = $check->get_result();

        if ($checkRes->num_rows > 0) {
            // they already in queue — fetch latest row for display
            $row = $checkRes->fetch_assoc();
            $status = $row['status'];
            $estimated_time = $row['estimated_time'];
            $position = $row['position_in_queue'];
        } else {
            // count waiting customers for this shop
            $countQ = $conn->prepare("SELECT COUNT(*) as cnt FROM queue WHERE shop_name = ? AND status IN ('Waiting','Available')");
            $countQ->bind_param('s', $shop_name);
            $countQ->execute();
            $cntRes = $countQ->get_result();
            $cntRow = $cntRes->fetch_assoc();
            $waiting = (int)$cntRow['cnt'];

            $position = $waiting + 1;

            // compute estimated time
            $AVG_MINUTES = 5;
            if ($position <= $available && $available > 0) {
                $status = 'Available';
                $estimated_time = '0 minutes';
            } else {
                $numAhead = $position - $available;
                if ($numAhead < 1) $numAhead = 1;
                if ($T > 0) {
                    $rounds = (int) ceil($numAhead / $T);
                } else {
                    $rounds = 9999;
                }
                $minutes = $rounds * $AVG_MINUTES;
                $status = 'Waiting';
                $estimated_time = $minutes . ' minutes';
            }

            // insert into queue
            $ins = $conn->prepare("INSERT INTO queue (customer_email, shop_name, position_in_queue, status, estimated_time) VALUES (?, ?, ?, ?, ?)");
            $ins->bind_param('ssiss', $email, $shop_name, $position, $status, $estimated_time);
            $ins->execute();
            $ins->close();

            // Recalculate queue for this shop to update positions & estimated times
            include 'update_queue.php';
            recalcQueueForShop($conn, $shop_name);

            // fetch the freshly updated row for this customer
            $fetch = $conn->prepare("SELECT * FROM queue WHERE customer_email = ? AND shop_name = ? ORDER BY created_at DESC LIMIT 1");
            $fetch->bind_param('ss', $email, $shop_name);
            $fetch->execute();
            $fr = $fetch->get_result();
            $row = $fr->fetch_assoc();
            $status = $row['status'];
            $estimated_time = $row['estimated_time'];
            $position = $row['position_in_queue'];
            $fetch->close();
        }
        $check->close();
    }
    $stmt->close();
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Customer - Trial Room Status</title>
<style>
body { font-family:Poppins, Arial, sans-serif; margin:0; padding:0; display:flex; align-items:center; justify-content:center; min-height:100vh; background:#f4f6f9 url('https://img.businessoffashion.com/resizer/v2/QGUT5LMPCNCKPPBT5CZ5ZZUQMY.jpg?auth=396fd32bea4fd21175b5464064aed38758f2fc5f316d08b51100081df1fc3ebb&width=1440') no-repeat center center/cover;; }
.card { width:420px; background:#fff; padding:28px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.06); }
h2 { margin:0 0 10px; color:#333; text-align:center; }
.info { margin-top:16px; line-height:1.6; color:#444; }
.meta { font-weight:600; color:#111; }
.btn { display:inline-block; margin-top:18px; padding:10px 16px; border-radius:8px; background:#0078d7; color:#fff; text-decoration:none; }
.logout { background:#e74c3c; margin-left:8px; }
.error { color:#b00020; margin-top:12px; text-align:center; }
</style>
</head>
<body>
<div class="card">
  <h2>Trial Room Status</h2>

  <?php if (!empty($error)): ?>
    <div class="error"><?php echo htmlspecialchars($error); ?></div>
  <?php else: ?>
    <div class="info">
      <div><span class="meta">Shop:</span> <?php echo htmlspecialchars($shop_name ?? ''); ?></div>
      <div><span class="meta">Your Position:</span> <?php echo htmlspecialchars($position ?? '-'); ?></div>
      <div><span class="meta">Status:</span> <?php echo htmlspecialchars($status ?? '-'); ?></div>
      <div><span class="meta">Estimated Time:</span> <?php echo htmlspecialchars($estimated_time ?? '-'); ?></div>
    </div>

    <div style="text-align:center;">
      <a class="btn" href="customer_dashboard.php">← Change Shop / Back</a>
      <a class="btn logout" href="logout.php">Logout</a>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
