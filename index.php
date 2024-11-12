
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Robo Rumble Cloud-Connect</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://unpkg.com/mqtt/dist/mqtt.min.js"></script>
    <link rel="stylesheet" href="files2/styles.css">
</head>
<body>
<div class="logo-container">
        <img src="img/logo.png" alt="Yantra IITM Logo"> <!-- Replace with your logo path -->
    </div>
    <div class="logo">
        <img src="img/image.png" alt="Yantra IITM Logo"> <!-- Replace with your logo path -->
    </div>
    <div class="container">
        <div class="card" id="card1">
            <div class="iframe-container">
                <iframe src="https://vdo.ninja/?view=EuxWab6" frameborder="0" class="video" allowfullscreen></iframe>
            </div>
            <div class="card-text">
                <p>This is card 1</p>
            </div>
            <button class="maximize-btn" onclick="toggleMaximize('card1')">
                <i class="fa fa-expand"></i> <!-- Maximize icon -->
            </button>
            <div class="card-footer">
                <h3>Left Camera</h3>
            </div>
        </div>

        <div class="card" id="card2">
            <div class="iframe-container">
                 <iframe src="https://vdo.ninja/?view=EuxWab6" frameborder="0" class="video" allowfullscreen></iframe>
            </div>
            <div class="card-text">
                <p>Right Camera</p>
            </div>
            <button class="maximize-btn" onclick="toggleMaximize('card2')">
                <i class="fa fa-expand"></i> <!-- Maximize icon -->
            </button>
            <div class="card-footer">
                <h3>Right Camera</h3>
            </div>
        </div>

        <div class="card" id="card3">
            <div class="iframe-container">
                 <iframe src="https://vdo.ninja/?view=EuxWab6" frameborder="0" class="video" allowfullscreen></iframe>
            </div>
            <div class="card-text">
                <p>This is card 3</p>
            </div>
            <button class="maximize-btn" onclick="toggleMaximize('card3')">
                <i class="fa fa-expand"></i> <!-- Maximize icon -->
            </button>
            <div class="card-footer">
                <h3>Car Camera</h3>
            </div>
        </div>
       
        
        <!-- Right Sidebar Container -->
        <div class="right-sidebar">
            <!-- Arrow Buttons in Sidebar -->
            <div class="arrow-container">
                <div class="arrows">
                    <div class="arrow up" onclick="sendCommand('0')">▲</div>
                    <div class="arrow left" onclick="sendCommand('1')">◀</div>
                    <div class="arrow right" onclick="sendCommand('3')">▶</div>
                    <div class="arrow down" onclick="sendCommand('4')">▼</div>
                </div>
            </div>
            <div class="sidebar-text">
                <h1>Yantra IITM BS Robo Club</h1>
                
    

<!-- Preset messages display -->
<div class="card-l" id="card4">
            <div class="iframe-container">
                <iframe src="leaderboard.php" frameborder="0" style="width: 100%; height: 100%;"></iframe>
            </div>
            <div class="card-text">
                
            </div>
            <button class="maximize-btn" onclick="toggleMaximize('card4')">
                <i class="fa fa-expand"></i> <!-- Maximize icon -->
            </button>
            <div class="card-footer">
                <h6>Leaderboard</h6>
            </div>
        </div>
</div>

<!-- Preset messages display -->

            <div class="center-right-text">
              
            </div>

<div class="chat-container">
    <div class="chat-box" id="chat-box">
        
    </div>
    <div class="input-box">
    <input type="text" id="chat-input" placeholder="Type your message..." onkeypress="if(event.key === 'Enter') sendMessage()">
    <button onclick="sendMessage()">
        <i class="fa fa-paper-plane"></i> <!-- Send Icon -->
    </button>
</div>

    </div>
</div>

        </div>
    </div>
<style>
/* Center the logo at the top */
.logo-container {
    position: fixed; /* Fixes the logo at the top of the page */
    bottom: 2%; /* Adjusts the distance from the top */
    left: 5%; /* Centers the logo horizontally */
    transform: translateX(-50%); /* Ensures perfect centering */
    z-index: 99999; /* Ensures it appears above other elements */
    padding: 10px;
}

.logo-container img {
    width: 150px; /* Set a maximum width for the logo */
    max-width: 100%; /* Ensure it scales down for smaller screens */
    height: auto; /* Maintain aspect ratio */
}

/* Optional: Ensure the main content appears below the fixed logo */
.container {
    margin-top: -5%; /* Adjust based on logo size */
}

.logo {
    position: fixed; /* Fixes the logo at the top of the page */
    bottom: 2%; /* Adjusts the distance from the top */
    right: 18%; /* Centers the logo horizontally */
    transform: translateX(-50%); /* Ensures perfect centering */
    z-index: 99999; /* Ensures it appears above other elements */
    padding: 10px;
}

.logo img {
    width: 120px; /* Set a maximum width for the logo */
    max-width: 100%; /* Ensure it scales down for smaller screens */
    height: auto; /* Maintain aspect ratio */
}
/* Basic card styles */
.card-l {
    position: relative;  /* To control its positioning */
    width: 100%;        /* Default width */
    height: 400px;       /* Default height */
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    overflow: hidden;
    transition: all 0.3s ease; /* Smooth transition for maximize effect */
    z-index: 1;           /* Default z-index to maintain stacking context */
}

/* Card container with iframe */
.iframe-container {
    width: 100%;
    height: 100%;
}

/* Button to maximize the card */


.maximize-btn:hover {
    color: #b600ad;
}

/* Maximize effect when the card is in maximized state */
.card-l.maximized {
    position: fixed;   /* Fix card in the viewport */
    top: 3%;
    left: 3%;
    width: 75vw;        /* Max width (75% of viewport) */
    height: 90vh;       /* Max height (90% of viewport) */
    z-index: 999;       /* Bring to the front */
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.3); /* Apply more prominent shadow when maximized */
}

/* Card footer styles */

/* Card text container */
.card-text {
    padding: 15px;
    text-align: center;
}

</style>

    
    <script>



        function checkApprovalStatus() {
            fetch('acess/check_approval.php')
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

    <script src="files2/script.js"></script>
</body>
</html>
