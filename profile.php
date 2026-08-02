<?php
include 'config.php';
if(!isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Update profile
if(isset($_POST['update'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    $stmt = $pdo->prepare("UPDATE users SET name=?, email=? WHERE id=?");
    $stmt->execute([$name, $email, $user_id]);
    $success = "Profile updated!";
}

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; }
        input { width: 100%; padding: 10px; margin: 10px 0; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Profile</h2>
        <?php if(isset($success)) echo "<p style='color:green'>$success</p>"; ?>
        <form method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
            
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
            
            <button type="submit" name="update">Update Profile</button>
        </form>
        <br>
        <a href="dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>