<?php
include('db_config.php');

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM leaderboard WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
