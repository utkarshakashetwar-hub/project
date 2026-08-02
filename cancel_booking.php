<?php
include 'config.php';
if(!isLoggedIn()) exit();

$booking_id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM bookings WHERE id=? AND user_id=?");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
header("Location: my_bookings.php");
?>