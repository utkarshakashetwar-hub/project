<?php include 'config.php';
$msg = '';
if($_SERVER['REQUEST_METHOD']=='POST'){
    $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    try{
        $stmt = $pdo->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
        $stmt->execute([$_POST['name'], $_POST['email'], $hash]);
        $msg = "success";
    } catch(Exception $e){ 
        $msg = "error"; 
    }
}
?>
<!DOCTYPE html><html><head><link rel="stylesheet" href="css/style.css"><title>Register</title>
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
<?php if($msg=='success') echo "<p class='success'>Registration Successful! <a href='login.php'>Login Now</a></p>";?>
<?php if($msg=='error') echo "<p class='error'>Email already exists!</p>";?>
<form method="POST">
<input type="text" name="name" placeholder="Full Name" required>
<input type="email" name="email" placeholder="Email Address" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Create Account</button>
</form>
<p>Already have account? <a href="login.php">Login Here</a></p>
</div></body></html>