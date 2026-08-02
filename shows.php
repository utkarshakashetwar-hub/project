<?php include 'config.php'; if(!isLoggedIn()) header("Location: index.php");
$stmt=$pdo->prepare("SELECT s.id, t.name, t.location, s.show_time, s.price, m.title
                     FROM shows s
                     JOIN theatres t ON s.theatre_id=t.id
                     JOIN movies m ON s.movie_id=m.id
                     WHERE s.movie_id=?");
$stmt->execute([$_GET['movie_id']]);
$shows=$stmt->fetchAll();
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="css/style.css"><title>Shows</title></head><body>
<div class="navbar"><a href="movies.php" style="color:white">← Back</a></div>
<div class="container"><h2>Select Show - <?= $shows[0]['title']?? 'Movie'?></h2>
<?php foreach($shows as $s):?>
<div style="border:1px solid #e0e0e0; padding:15px; margin:10px 0; border-radius:8px">
    <b style="font-size:18px"><?= $s['name']?></b> <span style="color:#5f6368">(<?= $s['location']?>)</span><br>
    <p style="margin:8px 0">Time: <b><?= $s['show_time']?></b> | Price: <b style="color:#1a73e8">₹<?= $s['price']?></b></p>
    <a href="seats.php?show_id=<?= $s['id']?>"><button style="width:auto;padding:10px 20px">Select Seats</button></a>
</div>
<?php endforeach;?>
</div></body></html>