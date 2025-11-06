<?php
// customer_dashboard.php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Dashboard - Smart Trial Room</title>
<style>
  body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: url('https://formroom.com/wp-content/uploads/2022/10/Photo-03.jpg') no-repeat center center/cover;
    height: 100vh;
background-size: cover;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
padding: 0;
    width: 100vw;
    font-family: Arial, sans-serif;
  overflow: hidden;
  }

  body::before {
    content: "";
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.45); /* dim overlay */
    z-index: 0;
top: 0;
  left: 0;
  right: 0;
  bottom: 0;

  }

  .dashboard-container {
    position: relative;
    z-index: 1;
    background: rgba(255,255,255,0.85);
    padding: 50px 60px;
    width: 420px;
    border-radius: 14px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.35);
    text-align: center;
  backdrop-filter: blur(6px);
  }

  h2 {
    color: #111;
    margin-bottom: 20px;
  }

  input[type="text"] {
    width: 100%;
    padding: 12px;
    margin: 10px 0 20px 0;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
  }

  button {
    width: 100%;
    padding: 12px;
    border: none;
    border-radius: 8px;
    background: #28a745;
    color: white;
    font-size: 17px;
    cursor: pointer;
  }

  button:hover {
    background: #1f7a31;
  }
a {
            text-decoration: none;
            color: #0078D7;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

</style>
</head>
<body>
  <div class="dashboard-container">
    <h2>Customer Dashboard</h2>
    <form action="customer_register.php" method="POST">
      <input type="text" name="shop_name" placeholder="Enter Shop Name" required>
      <button type="submit">Register</button>
    </form>
<br>
        <a href="index.php">← Back to Home</a>
  </div>
</body>
</html>
