<?php
include 'config.php';

// Admin login check
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// Agar URL me id aayi hai to
if(isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    
    try {
        // Booking delete kar de
        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        
        // Wapas Dashboard pe bhej de success message ke saath
        header("Location: Dashboard.php?msg=Booking Deleted Successfully");
        exit();
        
    } catch (PDOException $e) {
        header("Location: Dashboard.php?error=Delete Failed");
        exit();
    }
} else {
    // Agar id nahi hai to seedha dashboard pe bhej de
    header("Location: Dashboard.php");
    exit();
}
?>