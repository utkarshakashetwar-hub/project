<?php
require('config.php');

if(!isset($_SESSION['user_id']) && !isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if(!isset($_GET['movie_id']) || empty($_GET['movie_id'])) {
    die("Movie ID missing!");
}

$movie_id = $_GET['movie_id'];

$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

if(!$movie) {
    die("Movie not found!");
}

$stmt = $pdo->prepare("SELECT * FROM shows WHERE movie_id = ?");
$stmt->execute([$movie_id]);
$shows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book - <?=$movie['title']?></title>
    <style>
        body { margin:0; font-family: Arial; background: #4a6cf7; }
        .header { background:white; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; }
        .header a { margin-left:20px; text-decoration:none; color:#333; font-weight:bold; }
        .container { max-width:800px; margin:30px auto; background:white; padding:30px; border-radius:10px; }
        .show { background:#f3f4f6; padding:15px; margin:10px 0; border-radius:5px; display:flex; justify-content:space-between; align-items:center; }
        .btn { background:#4a6cf7; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>🎬 <?=$movie['title']?></h2>
        <div>
            <a href="movies.php">Back</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Select Show</h2>
        <?php if($shows): ?>
            <?php foreach($shows as $s): ?>
            <div class="show">
                <div>
                    <strong><?=date('d M Y h:i A', strtotime($s['show_time']))?></strong><br>
                    Price: ₹<?=$s['price']?> | Seats: <?= $s['available_seats'] ?? $s['seats'] ?? $s['total_seats'] ?? 'N/A' ?>
                </div>
                <a href="seats.php?show_id=<?=$s['id']?>" class="btn">Select Seats</a>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No shows available for this movie.</p>
        <?php endif; ?>
    </div>
</body>
</html>