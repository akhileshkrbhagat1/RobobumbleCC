<?php
$host = 'sql203.infinityfree.com';
$db = 'if0_37686867_leaderboard_db';  // Update with your database name
$user = 'if0_37686867';          // Update with your database user
$pass = 'Yantra123End';              // Update with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
