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
}
?>
<script>
function checkApprovalStatus() {
    fetch('check_approval.php')
        .then(response => response.text())
        .then(status => {
            if (status === "logout") {
                alert("Access revoked by admin.");
                window.location.href = 'login.php';
            }
        })
        .catch(error => console.error('Error:', error));
}

// Check every 30 seconds (30000 milliseconds)
setInterval(checkApprovalStatus, 30000);
</script>
