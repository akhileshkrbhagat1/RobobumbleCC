<?php
include('db_config.php');

// Fetch all leaderboard data
$stmt = $pdo->query("SELECT * FROM leaderboard ORDER BY score DESC");
$leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the leaderboard data as a JSON response
echo json_encode($leaderboard);
?>
