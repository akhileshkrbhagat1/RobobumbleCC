<?php
include 'db.php';

// Handle user approval update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $is_approved = $_POST['is_approved'];
    $player = $_POST['player']; // New column

    $stmt = $conn->prepare("UPDATE users SET is_approved = ?, player = ? WHERE id = ?");
    $stmt->bind_param("iii", $is_approved, $player, $user_id);

    if ($stmt->execute()) {
        echo "<div class='notification success'>User access updated.</div>";
    } else {
        echo "<div class='notification error'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Handle search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT id, username, is_approved, player FROM users";
if ($search) {
    $sql .= " WHERE username LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_term = "%$search%";
    $stmt->bind_param("s", $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Approval</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            max-width: 600px;
            width: 100%;
            margin-top: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .search-box {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        .search-box input[type="text"] {
            padding: 5px;
            width: 70%;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-right: 10px;
        }
        .search-box button {
            padding: 5px 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-box button:hover {
            background-color: #45a049;
        }
        .user-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }
        .user-card:last-child {
            border-bottom: none;
        }
        .username {
            font-weight: bold;
            color: #333;
        }
        .notification {
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .approval-select, .player-select {
            margin-right: 10px;
            padding: 5px;
        }
        .update-btn {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .update-btn:hover {
            background-color: #45a049;
        }
    </style>
    <script>
        function showNotification(message, isSuccess) {
            const notification = document.createElement('div');
            notification.className = 'notification ' + (isSuccess ? 'success' : 'error');
            notification.textContent = message;
            document.body.appendChild(notification);
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>User Approval Management</h2>
        
        <!-- Search Form -->
        <form method="GET" class="search-box">
            <input type="text" name="search" placeholder="Search by username" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="user-card">
                <span class="username"><?php echo htmlspecialchars($row['username']); ?></span>
                <form method="POST" onsubmit="return updateUserApproval(event)">
                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                    
                    <!-- Approval Dropdown -->
                    <select name="is_approved" class="approval-select">
                        <option value="1" <?php echo $row['is_approved'] ? "selected" : ""; ?>>Yes</option>
                        <option value="0" <?php echo !$row['is_approved'] ? "selected" : ""; ?>>No</option>
                    </select>
                    
                    <!-- Player Dropdown -->
                    <select name="player" class="player-select">
                        <option value="1" <?php echo $row['player'] == 1 ? "selected" : ""; ?>>1</option>
                        <option value="2" <?php echo $row['player'] == 2 ? "selected" : ""; ?>>2</option>
                        <option value="3" <?php echo $row['player'] == 3 ? "selected" : ""; ?>>3</option>
                        <option value="4" <?php echo $row['player'] == 4 ? "selected" : ""; ?>>4</option>
                    </select>

                    <button type="submit" class="update-btn">Update</button>
                </form>
            </div>
        <?php } ?>
    </div>
    <script>
        function updateUserApproval(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.includes('User access updated.')) {
                    showNotification('User access updated successfully.', true);
                } else {
                    showNotification('Error updating user access.', false);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', false);
            });
        }
    </script>
</body>
</html>
