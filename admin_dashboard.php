<?php
session_start();
include('db_connect.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_email'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch the latest shop updated by this admin
$shop_query = "SELECT shop_name FROM shop_status ORDER BY updated_at DESC LIMIT 1";
$shop_result = mysqli_query($conn, $shop_query);
$shop_row = mysqli_fetch_assoc($shop_result);
$current_shop = $shop_row ? $shop_row['shop_name'] : null;

// Fetch all customers for this shop
if ($current_shop) {
    $query = $conn->prepare("SELECT * FROM queue WHERE shop_name = ? ORDER BY position_in_queue ASC");
    $query->bind_param("s", $current_shop);
    $query->execute();
    $result = $query->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - <?php echo htmlspecialchars($current_shop ?? 'No Shop'); ?></title>
    <style>
        body {
            background: url('https://i0.wp.com/infinite.styletheory.co/wp-content/uploads/2018/08/1024px-Theory_clothing_retailer_Dressing_Room_Westport_CT_06880_USA_-_Mar_2013-1.jpg?fit=1024%2C765&ssl=1') no-repeat center center/cover;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            backdrop-filter: brightness(0.6);
            color: #fff;
        }

        .dashboard {
            width: 85%;
            margin: 80px auto;
            background: rgba(0, 0, 0, 0.65);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0px 8px 25px rgba(0, 0, 0, 0.3);
        }

        h2 {
            text-align: center;
            font-size: 28px;
            letter-spacing: 1px;
            color: #ffffff;
        }

        .shop-info {
            text-align: center;
            margin-bottom: 25px;
            font-size: 18px;
            color: #f0f0f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 14px 12px;
            text-align: center;
            color: #333;
        }

        th {
            background: #007bff;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: rgba(240, 240, 240, 0.8);
        }

        tr:hover {
            background-color: rgba(220, 220, 255, 0.6);
        }

        .no-data {
            text-align: center;
            color: #ccc;
            padding: 25px;
            font-size: 18px;
        }

        .btn-container {
            text-align: center;
            margin-top: 30px;
        }

        button {
            background-color: #ff4b5c;
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            font-size: 16px;
        }

        button:hover {
            background-color: #d63447;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            color: #ddd;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="dashboard">
        <h2>Admin Dashboard</h2>
        <div class="shop-info">
            <?php if ($current_shop): ?>
                <strong>Shop Name:</strong> <?php echo htmlspecialchars($current_shop); ?>
            <?php else: ?>
                <strong>No Shop Details Found</strong>
            <?php endif; ?>
        </div>

        <?php if (isset($result) && $result->num_rows > 0): ?>
            <table>
                <tr>
                    <th>Queue ID</th>
                    <th>Customer Email</th>
                    <th>Position</th>
                    <th>Status</th>
                    <th>Estimated Time</th>
                    <th>Joined At</th>
                </tr>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['customer_email']); ?></td>
                        <td><?php echo $row['position_in_queue']; ?></td>
                        <td><?php echo $row['status']; ?></td>
                        <td><?php echo $row['estimated_time']; ?></td>
                        <td><?php echo $row['created_at']; ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php else: ?>
            <div class="no-data">No customers currently in queue for this shop.</div>
        <?php endif; ?>

        <div class="btn-container">
            <form action="logout.php">
                <button type="submit">Logout</button>
            </form>
        </div>

        
    </div>
</body>
</html>
