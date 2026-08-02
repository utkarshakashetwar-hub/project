<?php include 'config.php';
if(!isLoggedIn()){ header("Location: login.php"); exit(); }

$bookings = $pdo->prepare("SELECT b.*, m.title, m.poster, s.show_time 
                          FROM bookings b 
                          JOIN shows s ON b.show_id=s.id 
                          JOIN movies m ON s.movie_id=m.id 
                          WHERE b.user_id=? ORDER BY b.id DESC");
$bookings->execute([$_SESSION['user_id']]);
$bookings = $bookings->fetchAll();
?>
<!DOCTYPE html><html><head><title>My Bookings</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh}
.navbar{position:fixed;top:0;left:0;right:0;background:white;padding:12px 30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);display:flex;justify-content:space-between;align-items:center;z-index:100}
.navbar h3{color:#667eea;margin:0;font-size:20px}
.navbar a{text-decoration:none;color:#333;font-weight:500}
.container{max-width:1200px;margin:80px auto 20px;padding:0 20px}
h2{color:white;margin-bottom:20px}
.table-box{background:white;border-radius:15px;padding:20px;overflow-x:auto}
table{width:100%;border-collapse:collapse}
th,td{padding:12px;text-align:left;border-bottom:1px solid #ddd}
th{background:#667eea;color:white}
.btn{padding:6px 12px;border-radius:5px;text-decoration:none;color:white;font-size:14px;margin-right:5px}
.download{background:#28a745}
.cancel{background:#dc3545}
</style>
</head>
<body>
<div class="navbar">
    <h3>🎬 My Bookings</h3>
    <a href="movies.php">← Back to Movies</a>
</div>
<div class="container">
    <h2>Your Bookings</h2>
    <div class="table-box">
        <table>
            <tr>
                <th>Movie</th>
                <th>Show Time</th>
                <th>Seats</th>
                <th>Amount</th>
                <th>Action</th>
            </tr>
            <?php foreach($bookings as $b): ?>
            <tr>
                <td><?=$b['title']?></td>
                <td><?=$b['show_time']?></td>
                <td><?=$b['seats']?></td>
                <td>₹<?=$b['total_amount']?></td>
                <td>
                    <a href='download_ticket.php?id=<?=$b['id']?>' class='btn download'>Download</a>
                    <a href='cancel_booking.php?id=<?=$b['id']?>' class='btn cancel' onclick='return confirm("Cancel booking?")'>Cancel</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
</body>
</html>