<?php include 'config.php';
if(!isLoggedIn() || $_SESSION['role']!='admin'){ header("Location: login.php"); exit(); }

if(isset($_GET['del_movie'])){
    $pdo->prepare("DELETE FROM movies WHERE id=?")->execute([$_GET['del_movie']]);
    header("Location: admin.php");
}
if(isset($_GET['del_show'])){
    $pdo->prepare("DELETE FROM shows WHERE id=?")->execute([$_GET['del_show']]);
    header("Location: admin.php");
}

$movies = $pdo->query("SELECT * FROM movies ORDER BY id DESC")->fetchAll();
$shows = $pdo->query("SELECT s.*, m.title FROM shows s JOIN movies m ON s.movie_id=m.id ORDER BY s.id DESC")->fetchAll();
?>
<!DOCTYPE html><html><head><title>Admin Panel</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif}
body{background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh}
.navbar{position:fixed;top:0;left:0;right:0;background:white;padding:12px 30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);display:flex;justify-content:space-between;align-items:center;z-index:1000}
.navbar h3{color:#667eea;margin:0}
.navbar a{text-decoration:none;color:#333;font-weight:500;margin-left:20px}
.container{max-width:1200px;margin:80px auto 20px;padding:0 20px}
h2{color:white;margin:20px 0 15px}
.btn{background:#667eea;color:white;padding:12px 25px;border-radius:8px;text-decoration:none;font-weight:600;display:inline-block;margin-right:10px}
.btn:hover{background:#5568d3}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:20px;margin-top:20px}
.card{background:white;border-radius:12px;overflow:hidden;box-shadow:0 5px 20px rgba(0,0,0,0.1)}
.card img{width:100%;height:300px;object-fit:cover;display:block}
.card h3{padding:12px 12px 5px;font-size:16px}
.card p{padding:0 12px 12px;color:#666;font-size:13px}
.card a{display:block;background:#dc3545;color:white;text-align:center;padding:10px;text-decoration:none;font-weight:600}
table{width:100%;background:white;border-radius:12px;overflow:hidden;margin-top:20px;border-collapse:collapse}
table th{background:#667eea;color:white;padding:15px;text-align:left}
table td{padding:15px;border-bottom:1px solid #eee}
table a{color:#dc3545;text-decoration:none;font-weight:600}
</style>
</head>
<body>
<div class="navbar">
    <h3>🎬 Admin Panel</h3>
    <div><a href="movies.php">View Site</a> <a href="logout.php">Logout</a></div>
</div>
<div class="container">
    <a href="add_movie.php" class="btn">+ Add New Movie</a>
    <a href="add_show.php" class="btn">+ Add New Show</a>

    <h2>Movies List</h2>
    <div class="grid">
        <?php foreach($movies as $m): ?>
        <div class="card">
            <img src="<?=$m['poster']?>" alt="<?=$m['title']?>">
            <h3><?=$m['title']?></h3>
            <p><?=$m['duration']?> min</p>
            <a href="?del_movie=<?=$m['id']?>" onclick="return confirm('Delete?')">Delete</a>
        </div>
        <?php endforeach; ?>
    </div>

    <h2>Shows List</h2>
    <table>
        <tr>
            <th>Movie</th>
            <th>Show Time</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php foreach($shows as $s): ?>
        <tr>
            <td><?=$s['title']?></td>
            <td><?=$s['show_time']?></td>
            <td>₹<?=$s['price']?></td>
            <td><a href="?del_show=<?=$s['id']?>" onclick="return confirm('Delete?')">Delete</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body></html>