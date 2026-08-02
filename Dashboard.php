<?php
require('config.php');

// Sirf admin ko hi andar aane de
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit;
}

// Stats (PostgreSQL syntax)
$total_movies = $pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings")->fetchColumn();
$today_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(booking_time) = CURRENT_DATE")->fetchColumn();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Cinema</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Arial; }
        body { background:#f5f7fa; }
        .sidebar { width:250px; background:#1e293b; height:100vh; position:fixed; padding:20px; color:white; }
        .sidebar h2 { margin-bottom:30px; color:#fbbf24; }
        .sidebar a { display:block; padding:12px; color:#cbd5e1; text-decoration:none; margin:5px 0; border-radius:5px; }
        .sidebar a:hover, .sidebar a.active { background:#334155; color:white; }
        .main { margin-left:250px; padding:30px; }
        .cards { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:30px; }
        .card { background:white; padding:25px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
        .card h3 { color:#64748b; font-size:14px; margin-bottom:10px; }
        .card .value { font-size:32px; font-weight:bold; color:#1e293b; }
        .card .icon { float:right; font-size:40px; opacity:0.2; }
        table { width:100%; background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.1); border-collapse:collapse; }
        th { background:#1e293b; color:white; padding:15px; text-align:left; }
        td { padding:15px; border-bottom:1px solid #e2e8f0; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🎬 ADMIN PANEL</h2>
        <a href="Dashboard.php" class="active">📊 Dashboard</a>
        <a href="add_movie.php">🎥 Add Movie</a>
        <a href="add_show.php">🕐 Add Show</a>
        <a href="reports.php">📈 Reports</a>
        <a href="analytics.php" style="background:#00ff88; color:black; padding:10px 20px; text-decoration:none; border-radius:5px; font-weight:bold;">📊 View Analytics</a>
        <a href="movies.php">👤 User View</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main">
        <h1>Dashboard Overview</h1>
        
        <div class="cards">
            <div class="card">
                <div class="icon">🎥</div>
                <h3>Total Movies</h3>
                <div class="value"><?=$total_movies?></div>
            </div>
            <div class="card">
                <div class="icon">🎫</div>
                <h3>Total Bookings</h3>
                <div class="value"><?=$total_bookings?></div>
            </div>
            <div class="card">
                <div class="icon">💰</div>
                <h3>Total Revenue</h3>
                <div class="value">₹<?=$total_revenue?></div>
            </div>
            <div class="card">
                <div class="icon">📅</div>
                <h3>Today Bookings</h3>
                <div class="value"><?=$today_bookings?></div>
            </div>
        </div>

        <h2>Recent Bookings</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Movie</th>
                <th>Seats</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
            <?php
            $stmt = $pdo->query("SELECT b.id, u.name, m.title, b.seats, b.total_amount, b.booking_time 
                                FROM bookings b 
                                JOIN users u ON b.user_id = u.id 
                                JOIN shows s ON b.show_id = s.id 
                                JOIN movies m ON s.movie_id = m.id 
                                ORDER BY b.id DESC LIMIT 10");
            while($row = $stmt->fetch()) {
                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['seats']}</td>
                    <td>₹{$row['total_amount']}</td>
                    <td>".date('d M Y h:i A', strtotime($row['booking_time']))."</td>
                =<td><a href='edit_booking.php?id={$row['id']}' style='background:#4CAF50; color:white; padding:5px 10px; text-decoration:none; border-radius:3px; margin-right:5px;'>Edit</a> <a href='delete_booking.php?id={$row['id']}' onclick=\"return confirm('Delete?')\" style='background:#ff4444; color:white; padding:5px 10px; text-decoration:none; border-radius:3px;'>Delete</a></td>
                </tr>";
            }
            ?>
        </table>
    </div>
</body>
</html>