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

/* Hide scrollbar on the entire page */
/* Hide the scrollbar but allow scrolling */
html, body {
    overflow: hidden; /* Hide scrollbars */
    height: 100%;     /* Ensure the body and html elements take up full height */
}

/* Allow scroll functionality for the body */
body {
    overflow: auto; /* Allow scrolling */
    -ms-overflow-style: none;  /* Hide scrollbar for IE and Edge */
    scrollbar-width: none;     /* Hide scrollbar for Firefox */
}

h1 {
    text-align: center;
    color: #ff00ff; /* Magenta color for title */
    font-size: 2em;
    margin-bottom: 10px;
    text-transform: uppercase;
    position: relative;
}

/* "LIVE" indicator with blinking effect */
.live {
    color: red;
    font-size: 0.5em; /* Adjusted for better visibility */
    font-weight: bold;
    position: absolute; /* Position it centrally on all screens */
    top: -20px;
    left: 50%;   /* Center horizontally */
    transform: translateX(-50%); /* Adjust to center the "LIVE" text */
    animation: blink 1s linear infinite;
    z-index: 1000;
}

/* Blinking animation */
@keyframes blink {
    50% {
        opacity: 0;
    }
}

/* Table Styling */
table {
    width: 100%; /* Ensure the table uses the full width */
    max-width: 600px; /* Maximum width to prevent overflow */
    border-collapse: collapse;
    background-color: #333;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    font-size: 0.9em; /* Slightly smaller text size */
}

/* Table headers and rows */
th, td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #444;
}

th {
    background-color: #ff00ff;
    color: #ffffff;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.9em;
}

tr:hover {
    background-color: #2a2a2a;
}

/* Highlighting Top 3 Players */
.top-3 {
    background-color: #660066;
    color: #ffccff;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    h1 {
        font-size: 0.8em;
    }

    table {
        font-size: 0.85em;
        width: 100%;
    }

    th, td {
        padding: 8px;
    }
}

@media (max-width: 480px) {
    h1 {
        font-size: 1em;
    }

    table {
        font-size: 0.75em;
        width: 100%;
    }

    th, td {
        padding: 6px;
    }

    .live {
        font-size: 0.7em; /* Adjusted for mobile screens */
    }
}

    </style>
</head>
<body>
    
    <h1>Robo-Rumble:Cloud Connect<span class="live">LIVE</span></h1>
    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Player Name</th>
                <th>Score</th>
               <!-- <th>Roll Number</th>-->
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
            fetch('admin/get_leaderboard.php')
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
                           
                            <td>${entry.course}</td>
                        `;
                       // row.innerHTML = `
                        //    <td>${index + 1}</td>
                        //    <td>${entry.player_name}</td>
                        //    <td>${entry.score}</td>
                        //    <td>${entry.roll_number}</td>
                        //    <td>${entry.course}</td>
                        //`;
                        leaderboardTable.appendChild(row);
                    });

                    lastData = data;
                })
                .catch(error => console.error('Error fetching leaderboard:', error));
        }

        // Auto-update leaderboard every 5 seconds
        setInterval(fetchLeaderboard, 5000);

        // Initial fetch when the page loads
        window.onload = fetchLeaderboard;
    </script>
</body>
</html>
