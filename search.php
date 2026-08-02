<form method="GET">
    <input type="text" name="q" placeholder="Search movie...">
    <button type="submit">Search</button>
</form>
<?php
if(isset($_GET['q'])) {
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ?");
    $stmt->execute(['%'.$_GET['q'].'%']);
    // results display karo
}
?>