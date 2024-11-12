<?php
include 'admin/db_config.php'; // Database connection

// Fetch leaderboard data with roll number and course details
$stmt = $pdo->query("SELECT * FROM leaderboard ORDER BY score DESC");
$leaderboard = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_player'])) {
    // Add a new player
    $player_name = $_POST['player_name'];
    $score = $_POST['score'];
    $roll_number = $_POST['roll_number'];
    $course = $_POST['course'];

    $stmt = $pdo->prepare("INSERT INTO leaderboard (player_name, score, roll_number, course) VALUES (?, ?, ?, ?)");
    $stmt->execute([$player_name, $score, $roll_number, $course]);

    header("Location: admin/admin.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    // Delete a player
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM leaderboard WHERE id = ?");
    $stmt->execute([$delete_id]);

    header("Location: admin/admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Leaderboard</title>
    <style>
        /* Dark Theme Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #1a1a1a;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #ff00ff; /* Magenta color for title */
            font-size: 2.5em;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        /* Table Styling */
        table {
            width: 70%;
            margin: auto;
            border-collapse: collapse;
            background-color: #333;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        th, td {
            padding: 15px;
            text-align: center;
            border-bottom: 1px solid #444;
        }

        th {
            background-color: #ff00ff; /* Magenta header */
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 1.1em;
        }

        tr:hover {
            background-color: #2a2a2a; /* Row hover effect */
        }

        /* Highlighting Top 3 Players */
        .top-3 {
            background-color: #660066; /* Dark magenta for top 3 players */
            color: #ffccff; /* Light magenta text */
        }
    </style>
</head>
<body>
    <h1>Leaderboard</h1>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Player Name</th>
                <th>Score</th>
                <th>Roll Number</th>
                <th>Course</th>
            </tr>
        </thead>
        <tbody id="leaderboard">
            <!-- Rows will be dynamically populated by JavaScript -->
        </tbody>
    </table>

    <script>
        let lastData = []; // Store previous data to detect changes

        // Fetch and display the leaderboard data
        function fetchLeaderboard() {
            fetch('get_leaderboard.php')
                .then(response => response.json())
                .then(data => {
                    const leaderboardTable = document.getElementById('leaderboard');
                    leaderboardTable.innerHTML = '';

                    data.forEach((entry, index) => {
                        const row = document.createElement('tr');
                        row.id = `player-${entry.id}`;
                        row.classList.add('table-row');

                        if (index < 3) {
                            row.classList.add('top-3');
                        }

                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${entry.player_name}</td>
                            <td>${entry.score}</td>
                            <td>${entry.roll_number}</td>
                            <td>${entry.course}</td>
                        `;
                        
                        leaderboardTable.appendChild(row);
                    });

                    lastData = data;
                })
                .catch(error => console.error('Error fetching leaderboard:', error));
        }

        // Auto-update leaderboard every 5 seconds
        setInterval(fetchLeaderboard, 1000);

        // Initial fetch when the page loads
        window.onload = fetchLeaderboard;
    </script>
</body>
</html>
