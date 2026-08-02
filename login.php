<?php include 'config.php';
$error = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
    $stmt->execute([$_POST['email']]);
    $user = $stmt->fetch();
    if($user && password_verify($_POST['password'], $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        header("Location: movies.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="css/style.css"><title>Login</title>
<style>
body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
.login-box { max-width: 400px; width: 90%; background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
.login-box h2 { text-align: center; margin-bottom: 20px; }
.login-box input { background: #f0f4ff; border: none; padding: 14px; margin: 10px 0; border-radius: 8px; width: 100%; box-sizing: border-box; }
.login-box button { background: #4285f4; padding: 14px; border-radius: 8px; font-size: 16px; width: 100%; }
.login-box p { text-align: center; margin-top: 15px; color: #666; font-size: 14px; }
.login-box a { color: #4285f4; text-decoration: none; font-weight: 600; }
</style>
</head>
<body><div class="login-box">
<h2 style="color:black">🎬 Movie Booking System</h2>
<?php if($error) echo "<p class='error'>$error</p>";?>
<form method="POST">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
<p>Don't have an account? <a href="register.php">Register here</a></p>
<p style="font-size:12px;color:#999">Admin: admin@movie.com / admin123</p>
</div></body></html>