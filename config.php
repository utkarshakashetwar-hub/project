<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = "localhost";
$db = "movie_db";
$user = "postgres";
$pass = "123456"; // APNA PASSWORD DALO
$port = "5432";

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("DB Error: ". $e->getMessage());
}

function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin() { return isset($_SESSION['role']) && $_SESSION['role']=='admin'; }
$isLoggedIn = isLoggedIn();
$isAdmin = isAdmin();
?> 
