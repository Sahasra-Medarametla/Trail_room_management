<?php
include('db_connect.php');
session_start();

$email = $_SESSION['customer_email']; // ensure you store email in session at login

$query = "SELECT status FROM queue WHERE customer_email='$email' ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

$response = array('status' => $row['status']);
echo json_encode($response);
?>
