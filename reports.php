<?php 
require('config.php'); 

if(!isset($_SESSION['user_id']) && !isset($_SESSION['user'])) { 
    header('Location: login.php'); 
    exit; 
} 

$from = $_GET['from'] ?? date('Y-m-01'); 
$to = $_GET['to'] ?? date('Y-m-d'); 

$stmt = $pdo->prepare("SELECT b.id, u.name, m.title, b.seats, b.total_amount, b.booking_time 
                       FROM bookings b 
                       JOIN users u ON b.user_id = u.id 
                       JOIN shows s ON b.show_id = s.id 
                       JOIN movies m ON s.movie_id = m.id 
                       WHERE DATE(b.booking_time) BETWEEN ? AND ? 
                       ORDER BY b.id DESC"); 
$stmt->execute([$from, $to]); 
$bookings = $stmt->fetchAll(); 

$total = array_sum(array_column($bookings, 'total_amount')); 
?> 
<!DOCTYPE html> 
<html> 
<head> 
<title>Reports</title> 
<style> 
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Arial; } 
body { background:#f5f7fa; } 
.sidebar { width:250px; background:#1e293b; height:100vh; position:fixed; padding:20px; color:white; } 
.sidebar h2 { margin-bottom:30px; color:#fbbf24; } 
.sidebar a { display:block; padding:12px; color:#cbd5e1; text-decoration:none; margin:5px 0; border-radius:5px; } 
.sidebar a:hover, .sidebar a.active { background:#334155; color:white; } 
.main { margin-left:250px; padding:30px; } 
table { width:100%; background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.1); border-collapse:collapse; margin-top:20px; } 
th { background:#1e293b; color:white; padding:15px; text-align:left; } 
td { padding:15px; border-bottom:1px solid #e2e8f0; } 
.filter { background:white; padding:20px; border-radius:10px; margin-bottom:20px; } 
input, button { padding:10px; margin:5px; border:1px solid #ddd; border-radius:5px; } 
button { background:#10b981; color:white; border:none; cursor:pointer; } 
.total { font-size:24px; color:#10b981; font-weight:bold; margin:20px 0; } 
</style> 
</head> 
<body> 
<div class="sidebar"> 
<h2>🎬 ADMIN PANEL</h2> 
<a href="Dashboard.php">📊 Dashboard</a> 
<a href="add_movie.php">🎥 Add Movie</a> 
<a href="add_show.php">🕐 Add Show</a> 
<a href="reports.php" class="active">📈 Reports</a> 
<a href="movies.php">👤 User View</a> 
<a href="logout.php">🚪 Logout</a> 
</div> 
<div class="main"> 
<h1>Booking Reports</h1> 
<div class="filter"> 
<form method="GET"> 
<input type="date" name="from" value="<?=$from?>"> 
<input type="date" name="to" value="<?=$to?>"> 
<button type="submit">Filter</button> 
</form> 
</div> 
<div class="total">Total Revenue: ₹<?=$total?></div> 

<?php if(empty($bookings)): ?>
    <div style="background:white; padding:40px; border-radius:10px; text-align:center; margin-top:20px;">
        <h3 style="color:#64748b;">😕 Is date range me koi booking nahi mili</h3>
        <p style="color:#94a3b8; margin-top:10px;">Dusri date select karke try karo</p>
    </div>
<?php else: ?>
    <table> 
        <tr><th>ID</th><th>User</th><th>Movie</th><th>Seats</th><th>Amount</th><th>Date</th></tr> 
        <?php foreach($bookings as $b): ?> 
        <tr> 
            <td><?=$b['id']?></td> 
            <td><?=$b['name']?></td> 
            <td><?=$b['title']?></td> 
            <td><?=$b['seats']?></td> 
            <td>₹<?=$b['total_amount']?></td> 
            <td><?=date('d M Y h:i A', strtotime($b['booking_time']))?></td> 
        </tr> 
        <?php endforeach; ?> 
    </table>
<?php endif; ?>

</div> 
</body> 
</html>