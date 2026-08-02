<?php 
session_start(); 
require('config.php'); 

if(!isset($_SESSION['user_id'])) { 
    header('Location: login.php'); 
    exit; 
} 

if(isset($_POST['add'])) { 
    $title = $_POST['title']; 
    $description = $_POST['description']; 
    
    // Duration hours + minutes se total minutes banaya
    $duration = ($_POST['duration_hours'] * 60) + $_POST['duration_mins']; 
    
    $language = $_POST['language']; 
    $genre = $_POST['genre']; 
    $release_date = $_POST['release_date']; 
    $poster = ''; 
    
    if(isset($_FILES['poster']) && $_FILES['poster']['error'] == 0) { 
        $poster = 'posters/' . time() . '_' . $_FILES['poster']['name']; 
        move_uploaded_file($_FILES['poster']['tmp_name'], $poster); 
    } 
    
    $stmt = $pdo->prepare("INSERT INTO movies (title, description, duration, language, genre, release_date, poster) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $duration, $language, $genre, $release_date, $poster]); 
    $msg = "Movie added successfully!"; 
} 
?> 
<!DOCTYPE html> 
<html> 
<head> 
<title>Add Movie</title> 
<style> 
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', Arial; } 
body { background:#f5f7fa; } 
.sidebar { width:250px; background:#1e293b; height:100vh; position:fixed; padding:20px; color:white; } 
.sidebar h2 { margin-bottom:30px; color:#fbbf24; } 
.sidebar a { display:block; padding:12px; color:#cbd5e1; text-decoration:none; margin:5px 0; border-radius:5px; } 
.sidebar a:hover, .sidebar a.active { background:#334155; color:white; } 
.main { margin-left:250px; padding:30px; } 
.form-box { background:white; padding:30px; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,0.1); max-width:600px; } 
input, textarea, select { width:100%; padding:12px; margin:10px 0; border:1px solid #ddd; border-radius:5px; } 
button { background:#10b981; color:white; padding:12px 30px; border:none; border-radius:5px; cursor:pointer; font-size:16px; } 
.msg { background:#d1fae5; color:#065f46; padding:10px; border-radius:5px; margin-bottom:20px; } 
.duration-box { display:flex; gap:10px; margin:10px 0; }
.duration-box input { margin:0; }
</style> 
</head> 
<body> 
<div class="sidebar"> 
<h2>🎬 ADMIN PANEL</h2> 
<a href="Dashboard.php">📊 Dashboard</a> 
<a href="add_movie.php" class="active">🎥 Add Movie</a> 
<a href="add_show.php">🕐 Add Show</a> 
<a href="reports.php">📈 Reports</a> 
<a href="analytics.php" style="background:#00ff88; color:black; padding:10px 20px; text-decoration:none; border-radius:5px; font-weight:bold;">📊 View Analytics</a> 
<a href="movies.php">👤 User View</a> 
<a href="logout.php">🚪 Logout</a> 
</div> 
<div class="main"> 
<h1>Add New Movie</h1> 
<?php if(isset($msg)) echo "<div class='msg'>$msg</div>"; ?> 
<div class="form-box"> 
<form method="POST" enctype="multipart/form-data"> 
<input type="text" name="title" placeholder="Movie Title" required> 
<textarea name="description" placeholder="Description" rows="4" required></textarea> 

<label style="display:block; margin-top:10px; color:#64748b;">Duration:</label>
<div class="duration-box">
    <input type="number" name="duration_hours" placeholder="Hours" min="0" max="5" required>
    <input type="number" name="duration_mins" placeholder="Minutes" min="0" max="59" required>
</div>

<select name="language" required>
    <option value="">Select Language</option>
    <option value="Hindi">Hindi</option>
    <option value="Marathi">Marathi</option>
    <option value="English">English</option>
</select>
<input type="text" name="genre" placeholder="Genre (e.g., Action, Comedy)" required>
<input type="date" name="release_date" required>
<input type="file" name="poster" accept="image/*" required> 
<button type="submit" name="add">Add Movie</button> 
</form> 
</div> 
</body> 
</html>
