<?php
session_start();
require('config.php');

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if(isset($_POST['add'])) {
    $movie_id = $_POST['movie_id'];
    $show_time = $_POST['show_time'];
    $price = $_POST['price'];
    $theatre_id = $_POST['theatre_id'];  // Line 14

    $stmt = $pdo->prepare("INSERT INTO shows (movie_id, theatre_id, show_time, price) VALUES (?, ?, ?, ?)"); // Line 16 - theatre_id add kiya
    $stmt->execute([$movie_id, $theatre_id, $show_time, $price]); // Line 17 - $theatre_id add kiya
    $msg = "Show added successfully!";
}

$movies = $pdo->query("SELECT * FROM movies ORDER BY id DESC")->fetchAll();
$theatres = $pdo->query("SELECT * FROM theatres")->fetchAll(); // Line 22 - ye add kar
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Show</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Arial; }
        body { background:#f5f7fa; }
        .sidebar { width:250px; background:#1e293b; height:100vh; position:fixed; padding:20px; color:white; }
        .sidebar h2 { margin-bottom:30px; color:#fbbf24; }
        .sidebar a { display:block; padding:12px; color:#cbd5e1; text-decoration:none; margin:5px 0; border-radius:5px; }
        .sidebar a:hover, .sidebar a.active { background:#334155; color:white; }
        .main { margin-left:250px; padding:30px; }
        .form-box { background:white; padding:30px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); max-width:600px; }
        input, select { width:100%; padding:12px; margin:10px 0; border:1px solid #ddd; border-radius:5px; }
        button { background:#10b981; color:white; padding:12px 30px; border:none; border-radius:5px; cursor:pointer; font-size:16px; }
        .msg { background:#d1fae5; color:#065f46; padding:10px; border-radius:5px; margin-bottom:20px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🎬 ADMIN PANEL</h2>
        <a href="Dashboard.php">📊 Dashboard</a>
        <a href="add_movie.php">🎥 Add Movie</a>
        <a href="add_show.php" class="active">🕐 Add Show</a>
        <a href="reports.php">📈 Reports</a>
        <a href="movies.php">👤 User View</a>
        <a href="logout.php">🚪 Logout</a>
    </div>

    <div class="main">
        <h1>Add New Show</h1>
        <?php if(isset($msg)) echo "<div class='msg'>$msg</div>"; ?>
        <div class="form-box">
            <form method="POST">
                <select name="movie_id" required>
                    <option value="">Select Movie</option>
                    <?php foreach($movies as $m): ?>
                        <option value="<?=$m['id']?>"><?=$m['title']?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>
<label>Theatre:</label><br>
<select name="theatre_id" required>
    <option value="">Select Theatre</option>
    <?php foreach($theatres as $t): ?>
        <option value="<?= $t['id'] ?>"><?= $t['name'] ?> - <?= $t['city'] ?></option>
    <?php endforeach; ?>
</select><br><br>
                <input type="datetime-local" name="show_time" required>
                <input type="number" name="price" placeholder="Price (Rs)" step="0.01" required>
                <button type="submit" name="add">Add Show</button>
            </form>
        </div>
    </div>
</body>
</html>