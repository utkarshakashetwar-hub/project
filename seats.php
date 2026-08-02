<?php
require('config.php');

if(!isset($_SESSION['user_id']) && !isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if(!isset($_GET['show_id'])) {
    die("Show ID missing!");
}

$show_id = $_GET['show_id'];

$stmt = $pdo->prepare("SELECT s.*, m.title FROM shows s JOIN movies m ON s.movie_id = m.id WHERE s.id = ?");
$stmt->execute([$show_id]);
$show = $stmt->fetch();

if(!$show) {
    die("Show not found!");
}

$booked = [];
$stmt = $pdo->prepare("SELECT seats FROM bookings WHERE show_id = ?");
$stmt->execute([$show_id]);
$bookings = $stmt->fetchAll();
foreach($bookings as $b) {
    $booked = array_merge($booked, explode(',', $b['seats']));
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['seats'])) {
    $seats = $_POST['seats'];
    $seat_count = count(explode(',', $seats));
    $total = $seat_count * $show['price'];
    $user_id = $_SESSION['user_id'] ?? 1;
    
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, show_id, seats, total_amount) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $show_id, $seats, $total]);
    
    header("Location: my_bookings.php?success=1");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Select Seats - <?=$show['title']?></title>
    <style>
        body { margin:0; font-family: Arial; background: #4a6cf7; }
        .header { background:white; padding:15px 30px; }
        .header a { margin-right:20px; text-decoration:none; color:#333; font-weight:bold; }
        .container { max-width:800px; margin:30px auto; background:white; padding:30px; border-radius:10px; text-align:center; }
        .screen { background:#1e293b; color:white; padding:10px; margin-bottom:30px; border-radius:5px; }
        .seats-grid { display:grid; grid-template-columns:repeat(8, 1fr); gap:10px; max-width:500px; margin:0 auto; }
        .seat { width:50px; height:50px; border-radius:5px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-weight:bold; }
        .available { background:#e5e7eb; }
        .selected { background:#10b981; color:white; }
        .booked { background:#ef4444; color:white; cursor:not-allowed; }
        .legend { display:flex; justify-content:center; gap:20px; margin:20px 0; }
        .legend-item { display:flex; align-items:center; gap:5px; }
        .legend-box { width:20px; height:20px; border-radius:3px; }
        button { background:#4a6cf7; color:white; padding:12px 30px; border:none; border-radius:5px; font-size:16px; cursor:pointer; margin-top:20px; }
    </style>
</head>
<body>
    <div class="header">
        <a href="book.php?movie_id=<?=$show['movie_id']?>">Back</a>
        <a href="movies.php">Movies</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="container">
        <h2><?=$show['title']?></h2>
        <p><?=date('d M Y h:i A', strtotime($show['show_time']))?> | ₹<?=$show['price']?> per seat</p>
        
        <div class="screen">SCREEN THIS WAY</div>
        
        <div class="seats-grid" id="seatsGrid">
            <?php
            $rows = ['A','B','C','D','E'];
            foreach($rows as $row) {
                for($i=1; $i<=8; $i++) {
                    $seat = $row.$i;
                    $class = in_array($seat, $booked) ? 'booked' : 'available';
                    echo "<div class='seat $class' data-seat='$seat'>$seat</div>";
                }
            }
            ?>
        </div>

        <div class="legend">
            <div class="legend-item"><div class="legend-box" style="background:#e5e7eb"></div><span>Available</span></div>
            <div class="legend-item"><div class="legend-box" style="background:#10b981"></div><span>Selected</span></div>
            <div class="legend-item"><div class="legend-box" style="background:#ef4444"></div><span>Booked</span></div>
        </div>

        <form method="POST" id="bookingForm" style="text-align:center">
            <input type="hidden" name="seats" id="selectedSeats">
            <button type="submit" id="bookBtn" style="display:none">Confirm Booking (<span id="count">0</span> seats) - ₹<span id="total">0</span></button>
        </form>
    </div>

    <script>
        let selected = [];
        const price = <?=$show['price']?>;
        document.querySelectorAll('.seat.available').forEach(seat => {
            seat.addEventListener('click', function() {
                let seatNum = this.dataset.seat;
                if(this.classList.contains('selected')) {
                    this.classList.remove('selected');
                    selected = selected.filter(s => s != seatNum);
                } else {
                    this.classList.add('selected');
                    selected.push(seatNum);
                }
                document.getElementById('selectedSeats').value = selected.join(',');
                document.getElementById('count').innerText = selected.length;
                document.getElementById('total').innerText = selected.length * price;
                document.getElementById('bookBtn').style.display = selected.length > 0 ? 'inline-block' : 'none';
            });
        });
    </script>
</body>
</html>