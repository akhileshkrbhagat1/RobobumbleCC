<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login_form.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Protected Page</title>
</head>
<body>
    <h1>Welcome to the Protected Page</h1>
    <p>This page requires admin approval for access.</p>

    <script>
        function checkApprovalStatus() {
            fetch('check_approval.php')
                .then(response => response.text())
                .then(status => {
                    if (status.trim() === "logout") {
                        window.location.href = 'login_form.html';  // Redirect to login page without message
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Check approval status every 5 seconds (short interval for testing)
        setInterval(checkApprovalStatus, 5000);
    </script>
</body>
</html>
