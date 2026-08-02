<?php include 'config.php';
if(!isLoggedIn()){ header("Location: login.php"); exit(); }

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $show_id = $_POST['show_id'];
    $movie_id = $_POST['movie_id'];
    $seats = $_POST['seats'];
    
    if(empty($seats)) {
        die("Please select at least one seat!");
    }
    
    // Get show price
    $stmt = $pdo->prepare("SELECT price FROM shows WHERE id=?");
    $stmt->execute([$show_id]);
    $show = $stmt->fetch();
    
    $seat_count = count(explode(',', $seats));
    $total = $seat_count * $show['price'];
    
    // Insert booking
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, show_id, seats, total_amount) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $show_id, $seats, $total]);
    
    $booking_id = $pdo->lastInsertId();
    
    echo "<!DOCTYPE html><html><head><title>Booking Confirmed</title>
    <style>
    body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;display:flex;justify-content:center;align-items:center;font-family:'Segoe UI',sans-serif}
    .box{background:white;padding:40px;border-radius:15px;text-align:center;max-width:500px}
    h2{color:#28a745;margin-bottom:20px}
    .details{background:#f8f9fa;padding:20px;border-radius:10px;margin:20px 0;text-align:left}
    a{background:#667eea;color:white;padding:12px 30px;border-radius:8px;text-decoration:none;display:inline-block;margin-top:20px}
    </style></head><body>
    <div class='box'>
        <h2>✓ Booking Confirmed!</h2>
        <div class='details'>
            <p><strong>Booking ID:</strong> #$booking_id</p>
            <p><strong>Seats:</strong> $seats</p>
            <p><strong>Total Amount:</strong> ₹$total</p>
        </div>
        <a href='movies.php'>Back to Movies</a>
    </div>
    </body></html>";
}
?>