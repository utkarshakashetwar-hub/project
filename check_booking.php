<?php
include 'config.php';
session_start();

echo "Logged in User ID: " . $_SESSION['user_id'] . "<br><br>";

$stmt = $pdo->query("SELECT * FROM bookings");
$all = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Total bookings in DB: " . count($all) . "<br><br>";

foreach($all as $b) {
    echo "Booking ID: {$b['booking_id']} | User ID: {$b['user_id']} | Show ID: {$b['show_id']} | Status: {$b['status']}<br>";
}
?>