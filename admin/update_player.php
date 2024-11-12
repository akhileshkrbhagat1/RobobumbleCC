<?php
include('db_config.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    // Update the specified field of the leaderboard entry
    $stmt = $pdo->prepare("UPDATE leaderboard SET $field = ? WHERE id = ?");
    $stmt->execute([$value, $id]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
