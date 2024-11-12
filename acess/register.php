<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if email (username) already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // If email exists, prompt user to log in
        echo '<div class="message error">This email is already registered. Please <a href="../login_form.html">log in</a>.</div>';
    } else {
        // Insert new user into the database
        $stmt->close();  // Close the previous statement
        $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            header("Location: ../login_form.html");
            exit();  // Ensure no further code is executed after the redirect
        } else {
            echo '<div class="message error">Error: ' . htmlspecialchars($stmt->error) . '</div>';
        }
    }
    $stmt->close();
}
?>

<style>
    body {
        background: linear-gradient(45deg, #000000, #db00b7);
        font-family: Arial, sans-serif;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    }

    /* Style for messages */
    .message {
        padding: 15px;
        border-radius: 5px;
        margin: 20px;
        text-align: center;
        font-size: 16px;
        max-width: 400px;
    }

    .message.error {
        background-color: rgba(0, 0, 0, 0.7);
        color: #fff;
        border: 1px solid #f5c6cb;
    }

    .message a {
        color: #db00b7;
        text-decoration: underline;
    }
</style>
