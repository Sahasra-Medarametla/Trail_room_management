<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Trial Room Availability and Queue Management System</title>
    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-image: url("https://images.pexels.com/photos/3965545/pexels-photo-3965545.jpeg");
            background-size: cover;
            background-position: center;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            background: rgba(0,0,0,0.5);
        }

        .container {
            position: relative;
            width: 90%;
            max-width: 900px;
            height: 400px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 2;
        }

        .box {
            width: 45%;
            height: 250px;
            background: rgba(255, 255, 255, 0.85);
            border-radius: 15px;
            text-align: center;
            padding: 40px 20px;
            box-shadow: 0px 0px 20px rgba(0,0,0,0.3);
            transition: transform 0.3s ease;
        }

        .box:hover {
            transform: scale(1.05);
        }

        h1 {
            position: absolute;
            top: 50px;
            text-align: center;
            width: 100%;
            color: #fff;
            font-size: 30px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.5);
            z-index: 2;
        }

        h2 {
            color: #222;
            font-size: 22px;
        }

        p {
            color: #444;
            margin-bottom: 25px;
        }

        .btn {
            text-decoration: none;
            background-color: #0078D7;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background-color: #005EA3;
        }

        footer {
            position: absolute;
            bottom: 20px;
            text-align: center;
            width: 100%;
            color: #fff;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
    </style>
</head>
<body>

    <div class="overlay"></div>
    <h1>Smart Trial Room Availability and Queue Management System</h1>

    <div class="container">
        <!-- Customer Login Box (Left Side) -->
        <div class="box">
            <h2>Customer Login</h2>
            <p>Check trial room availability, join the queue, and get real-time notifications.</p>
            <a href="customer_login.php" class="btn">Login as Customer</a>
        </div>

        <!-- Admin Login Box (Right Side) -->
        <div class="box">
            <h2>Admin Login</h2>
            <p>Manage trial room availability, monitor queue status, and update records.</p>
            <a href="admin_login.php" class="btn">Login as Admin</a>
        </div>
    </div>

    <footer>
        © 2025 Smart Trial Room System 
    </footer>

</body>
</html>
