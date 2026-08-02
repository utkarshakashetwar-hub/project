<?php
require 'config.php';

$message = '';

if(isset($_POST['send'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $user_message = $_POST['user_message'];
    
    if(!empty($name) && !empty($email) && !empty($user_message)) {
        $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $user_message]);
        $message = "<p style='color:green; font-weight:bold;'>Message bhej diya! Hum 24 ghante me reply karenge.</p>";
    } else {
        $message = "<p style='color:red;'>Saare box bharo bhai</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
</head>
<body>
    <div style="background:#007bff; color:white; padding:15px;">
        <a href="movies.php" style="color:white;">← Back to Movies</a>
        <div style="float:right;">
            <a href="movies.php" style="color:white;">Home</a> | 
            <a href="my_bookings.php" style="color:white;">My Bookings</a> | 
            <a href="contact.php" style="color:white;">Contact Us</a> | 
            <a href="logout.php" style="color:white;">Logout</a>
        </div>
    </div>

    <div style="padding:20px; max-width:600px; margin:auto;">
        <h2>Contact Us / Customer Care</h2>
        
        <?= $message ?>
        
        <form method="POST">
            <label>Aapka Naam:</label><br>
            <input type="text" name="name" style="width:100%; padding:8px;" required><br><br>
            
            <label>Email:</label><br>
            <input type="email" name="email" style="width:100%; padding:8px;" required><br><br>
            
            <label>Subject:</label><br>
            <input type="text" name="subject" placeholder="Booking Issue, Refund, etc" style="width:100%; padding:8px;"><br><br>
            
            <label>Message:</label><br>
            <textarea name="user_message" rows="5" style="width:100%; padding:8px;" required></textarea><br><br>
            
            <button type="submit" name="send" style="background:#007bff; color:white; padding:10px 20px; border:none;">Send Message</button>
        </form>
        
        <hr style="margin:30px 0;">
        <h3>Direct Contact</h3>
        <p><b>Email:</b> support@moviebook.com</p>
        <p><b>Phone:</b> +91 7798231193</p>
        <p><b>Time:</b> Mon-Sat, 10 AM to 7 PM</p>
    </div>
</body>
</html>