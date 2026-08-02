<?php
require('config.php');
if(isset($_POST['city'])) {
    $_SESSION['city'] = $_POST['city'];
    header("Location: movies.php");
    exit;
}

$selectedCity = $_SESSION['city'] ?? 'Pune';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Admin hai ya nahi check karne ke liye
$isAdmin = $_SESSION['role'] == 'admin' ?? false;
$search = $_GET['search'] ?? '';
if($search) {
    $stmt = $pdo->prepare("SELECT 
        m.id, 
        m.title, 
        m.poster, 
        t.name as theatre_name,
        CONCAT(t.location, ', ', t.city) as location,
        STRING_AGG(DISTINCT s.show_time, ', ' ORDER BY s.show_time) as show_times
        FROM movies m
        JOIN shows s ON m.id = s.movie_id AND s.show_date >= CURRENT_DATE
        JOIN theatres t ON s.theatre_id = t.id
        WHERE t.city = ? AND m.title LIKE ?
        GROUP BY m.id, m.title, m.poster, t.name, t.location, t.city
        ORDER BY m.id DESC");
    $stmt->execute([$selectedCity, "%$search%"]);
} else {
    $stmt = $pdo->prepare("SELECT 
        m.id, 
        m.title, 
        m.poster, 
        t.name as theatre_name,
        CONCAT(t.location, ', ', t.city) as location,
        STRING_AGG(DISTINCT s.show_time, ', ' ORDER BY s.show_time) as show_times
        FROM movies m
        JOIN shows s ON m.id = s.movie_id AND s.show_date >= CURRENT_DATE
        JOIN theatres t ON s.theatre_id = t.id
        WHERE t.city = ?
        GROUP BY m.id, m.title, m.poster, t.name, t.location, t.city
        ORDER BY m.id DESC");
    $stmt->execute([$selectedCity]);
}

$movies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Movies</title>
    <style>
        body { margin:0; font-family: Arial; background: #4a6cf7; }
        .header { background:white; padding:15px 30px; display:flex; justify-content:space-between; align-items:center; }
        .header h2 { margin:0; color:#4a6cf7; }
        .header a { margin-left:20px; text-decoration:none; color:#333; font-weight:bold; }
        .container { padding:30px; }
        h1 { color:white; text-align:center; margin:20px 0; }
        .city-form { text-align:center; margin-bottom:10px; }
        .city-form select { padding:8px; border-radius:5px; border:none; }
        .search { text-align:center; margin:20px 0; }
        .search input { padding:10px; width:300px; border:none; border-radius:5px; }
        .search button { padding:10px 20px; background:white; border:none; border-radius:5px; cursor:pointer; }
        .movie-grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:20px; }
        .movie-card { background:white; padding:15px; border-radius:10px; text-align:center; }
        .movie-card img { max-width:100%; height:280px; object-fit:cover; border-radius:5px; }
        .movie-card h3 { margin:10px 0; }
        .movie-card p { color:#555; font-size:13px; margin:8px 0; }
        .movie-card a { display:block; margin-top:10px; padding:8px; background:#4a6cf7; color:white; text-decoration:none; border-radius:5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Movie Booking</h2>
        <div>
            <a href="my_bookings.php">My Bookings</a>
            <a href="contact.php">Contact</a>
            <?php if($isAdmin): ?>
                <a href="admin.php">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <h1>Available Movies in <?php echo htmlspecialchars($selectedCity); ?></h1>
        
        <form method="post" class="city-form">
            <select name="city" onchange="this.form.submit()">
                <option value="Pune" <?php echo $selectedCity=='Pune'?'selected':''; ?>>Pune</option>
                <option value="Mumbai" <?php echo $selectedCity=='Mumbai'?'selected':''; ?>>Mumbai</option>
            </select>
        </form>
        
        <div class="search">
            <form method="get">
                <input type="text" name="search" placeholder="Search movies..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Search</button>
            </form>
        </div>
        
        <div class="movie-grid">
            <?php foreach($movies as $m): ?>
                <div class="movie-card">
                    <img src="<?php echo htmlspecialchars($m['poster']); ?>" alt="<?php echo htmlspecialchars($m['title']); ?>">
                    <h3><?php echo htmlspecialchars($m['title']); ?></h3>
                    <p>
                         🎬 <?php echo htmlspecialchars($m['theatre_name']); ?><br>
    📍 <?php echo htmlspecialchars($m['location']); ?><br>
    ⏰ <?php echo htmlspecialchars($m['show_times'] ?? 'No Shows'); ?>
            </p>
                    <a href="book.php?movie_id=<?php echo $m['id']; ?>">Book Tickets</a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>