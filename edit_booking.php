<?php
include 'config.php';

if(isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $title = $_POST['title'];
    $seats = $_POST['seats'];
    $total_amount = $_POST['total_amount'];
    
    $stmt = $pdo->prepare("UPDATE bookings SET seats=?, total_amount=? WHERE id=?");
    $stmt->execute([$seats, $total_amount, $id]);
    
    header("Location: Dashboard.php?msg=Updated");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT b.id, u.name, m.title, b.seats, b.total_amount 
                       FROM bookings b 
                       JOIN users u ON b.user_id = u.id 
                       JOIN shows s ON b.show_id = s.id 
                       JOIN movies m ON s.movie_id = m.id 
                       WHERE b.id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Booking</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f4f4f4; }
        .form-box { background: white; padding: 20px; max-width: 400px; margin: auto; border-radius: 8px; }
        input { width: 100%; padding: 8px; margin: 8px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #4CAF50; color: white; padding: 10px; border: none; width: 100%; border-radius: 4px; cursor: pointer; }
        label { font-weight: bold; }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Edit Booking #<?php echo $row['id']; ?></h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            
            <label>User Name:</label>
            <input type="text" name="name" value="<?php echo $row['name']; ?>" readonly>
            
            <label>Movie:</label>
            <input type="text" name="title" value="<?php echo $row['title']; ?>" readonly>
            
            <label>Seats:</label>
            <input type="number" name="seats" value="<?php echo $row['seats']; ?>" required>
            
            <label>Amount:</label>
            <input type="number" name="total_amount" value="<?php echo $row['total_amount']; ?>" required>
            
            <button type="submit" name="update">Update Booking</button>
        </form>
        <br>
        <a href="Dashboard.php">← Back to Dashboard</a>
    </div>
</body>
</html>