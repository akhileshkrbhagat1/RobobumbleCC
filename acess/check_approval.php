<?php
session_start();
include 'db.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT is_approved FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($is_approved);
    $stmt->fetch();

    if (!$is_approved) {
        session_destroy();
        echo "logout";
    } else {
        echo "approved";
    }

    $stmt->close();
} else {
    echo "logout";  // If no session exists, force logout
}
?>
