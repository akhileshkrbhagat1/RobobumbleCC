<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to retrieve user details, including the player column
    $stmt = $conn->prepare("SELECT id, password_hash, is_approved, player FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $password_hash, $is_approved, $player);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $password_hash)) {
        if ($is_approved) {
            // Store user session
            $_SESSION['user_id'] = $id;

            // Redirect based on the player value
            header("Location: ../index.php?player=$player");
            exit();
        } else {
            // Account pending approval message with auto-refresh
            echo '<div class="message error">Your account is pending approval.</div>';
            echo '<script>setTimeout(() => { location.reload(); }, 5000);</script>';
        }
    } else {
        // Invalid credentials message with auto-refresh
        echo '<div class="message error">Invalid username or password.</div>';
        echo '<script>setTimeout(() => { location.reload(); }, 5000);</script>';
    }

    $stmt->close();
}
?>

<style>
body {
    background: linear-gradient(45deg, #000000, #db00b7);
}
    /* Add custom CSS styling */
    .message {
        padding: 10px;
        border-radius: 5px;
        margin: 20px;
        text-align: center;
        font-family: Arial, sans-serif;
    }
    .message.error {
        background-color: rgba(0, 0, 0, 0.7);
        color: #fff;
        border: 1px solid #f5c6cb;
    }
</style>
