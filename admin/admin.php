<?php
include('db_config.php');

// Fetch leaderboard data
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

    header("Location: admin.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    // Delete a player
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM leaderboard WHERE id = ?");
    $stmt->execute([$delete_id]);

    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Leaderboard</title>
    <style>
       /* General Styling */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background-color: #f5f5f5;
    color: #333;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Title */
h1, h2 {
    text-align: center;
    color: #333;
}

/* Form Styling */
form {
    width: 100%;
    max-width: 500px;
    background-color: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

label {
    display: block;
    margin: 10px 0 5px;
}

input[type="text"], input[type="number"], input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin: 5px 0;
    border: 1px solid #ddd;
    border-radius: 4px;
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}

/* Table Styling */
table {
    width: 100%;
    max-width: 700px;
    margin-top: 20px;
    border-collapse: collapse;
    background-color: #fff;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

th, td {
    padding: 10px;
    text-align: center;
    border: 1px solid #ddd;
}

th {
    background-color: #f4f4f4;
}

td input[type="text"], td input[type="number"] {
    width: 80%;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

/* Responsive Adjustments */
@media (max-width: 600px) {
    form, table {
        width: 90%;
    }

    th, td {
        font-size: 0.9em;
        padding: 8px;
    }

    td input[type="text"], td input[type="number"] {
        width: 100%;
    }
}

    </style>
</head>
<body>
    <h1>Leaderboard Management</h1>
    
    <!-- Form to add new player -->
    <h2>Add New Player</h2>
    <form method="POST">
        <label for="player_name">Player Name:</label><br>
        <input type="text" id="player_name" name="player_name" required><br><br>
        
        <label for="score">Score:</label><br>
        <input type="number" id="score" name="score" required><br><br>
        
        <label for="roll_number">Roll Number:</label><br>
        <input type="text" id="roll_number" name="roll_number"><br><br>
        
        <label for="course">Course:</label><br>
        <input type="text" id="course" name="course"><br><br>
        
        <input type="submit" name="add_player" value="Add Player">
    </form>

    <h2>Leaderboard</h2>
    <table id="leaderboard-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Score</th>
                <th>Roll Number</th>
                <th>Course</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data rows will be inserted dynamically here -->
        </tbody>
    </table>

    <script>
        // Function to load leaderboard from the server
        function loadLeaderboard() {
            fetch('get_leaderboard.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.querySelector('#leaderboard-table tbody');
                    tbody.innerHTML = ''; // Clear existing rows

                    data.forEach(entry => {
                        const row = document.createElement('tr');
                        row.setAttribute('data-id', entry.id);
                        row.innerHTML = `
                            <td>${entry.id}</td>
                            <td><input type="text" value="${entry.player_name}" class="editable" data-column="player_name" data-id="${entry.id}" /></td>
                            <td><input type="number" value="${entry.score}" class="editable" data-column="score" data-id="${entry.id}" /></td>
                            <td><input type="text" value="${entry.roll_number}" class="editable" data-column="roll_number" data-id="${entry.id}" /></td>
                            <td><input type="text" value="${entry.course}" class="editable" data-column="course" data-id="${entry.id}" /></td>
                            <td>
                                <button onclick="deletePlayer(${entry.id})">Delete</button>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });

                    // Add event listeners to editable fields
                    document.querySelectorAll('.editable').forEach(input => {
                        input.addEventListener('change', updateField);
                    });
                })
                .catch(error => console.error('Error loading leaderboard:', error));
        }

        // Function to update a specific field of a player
        function updateField(event) {
            const field = event.target.getAttribute('data-column');
            const id = event.target.getAttribute('data-id');
            const value = event.target.value;

            fetch('update_player.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id=${id}&field=${field}&value=${value}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Update successful');
                } else {
                    alert('Failed to update field');
                }
            })
            .catch(error => console.error('Error updating field:', error));
        }

        // Function to delete a player
        function deletePlayer(id) {
            if (confirm('Are you sure you want to delete this player?')) {
                fetch(`delete_player.php?id=${id}`, { method: 'GET' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            loadLeaderboard();
                        } else {
                            alert('Failed to delete player');
                        }
                    })
                    .catch(error => console.error('Error deleting player:', error));
            }
        }
// Function to load leaderboard from the server and arrange players by position
function loadLeaderboard() {
    fetch('get_leaderboard.php')
        .then(response => response.json())
        .then(data => {
            // Sort data by score in descending order
            data.sort((a, b) => b.score - a.score);

            const tbody = document.querySelector('#leaderboard-table tbody');
            tbody.innerHTML = ''; // Clear existing rows

            data.forEach((entry, index) => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', entry.id);

                row.innerHTML = `
                    <td>${index + 1}</td> <!-- Display rank based on sorted position -->
                    <td><input type="text" value="${entry.player_name}" class="editable" data-column="player_name" data-id="${entry.id}" /></td>
                    <td><input type="number" value="${entry.score}" class="editable" data-column="score" data-id="${entry.id}" /></td>
                    <td><input type="text" value="${entry.roll_number}" class="editable" data-column="roll_number" data-id="${entry.id}" /></td>
                    <td><input type="text" value="${entry.course}" class="editable" data-column="course" data-id="${entry.id}" /></td>
                    <td>
                        <button onclick="deletePlayer(${entry.id})">Delete</button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            // Add event listeners to editable fields
            document.querySelectorAll('.editable').forEach(input => {
                input.addEventListener('change', updateField);
            });
        })
        .catch(error => console.error('Error loading leaderboard:', error));
}

        // Load leaderboard data on page load
        window.onload = loadLeaderboard;
    </script>
</body>
</html>
