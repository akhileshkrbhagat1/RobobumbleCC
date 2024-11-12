// Toggle card maximization
function toggleMaximize(cardId) {
    const card = document.getElementById(cardId);
    const arrowContainer = document.querySelector('.arrow-container');

    // Toggle the maximized class on the card
    card.classList.toggle('maximized');

    if (card.classList.contains('maximized')) {
        // When maximized, adjust the z-index of the card and size
        card.style.zIndex = '999';  // Ensure the card is above other content but below arrows
        card.style.position = 'fixed';  // Make card fixed within the viewport
        card.style.width = '75vw';     // Adjust width when maximized
        card.style.height = '90vh';    // Adjust height when maximized
    } else {
        // Reset the card's styles when minimized
        card.style.zIndex = '1';  // Reset z-index of the card to default
        card.style.position = ''; // Remove any position style
        card.style.width = '';    // Reset width
        card.style.height = '';   // Reset height
    }
}

// Extract player value from URL parameter
const urlParams = new URLSearchParams(window.location.search);
const player = parseInt(urlParams.get('player'), 10);  // Ensure player is parsed as an integer

// Log the extracted player value for debugging
console.log("Extracted player value from URL:", player);

// Validate player value and log errors if invalid
let movementTopic;
if (player >= 1 && player <= 4) {
    movementTopic = `robot/movementplayer${player}`;  // Fixed template literal
    console.log("Using movement topic:", movementTopic); // This should log the correct topic
} else {
    console.error("Invalid or missing player value. Using default topic.");
    movementTopic = "robot/movement";  // Fallback in case of invalid player
}

// MQTT connection setup
const client = mqtt.connect("wss://test.mosquitto.org:8081");

client.on("connect", () => {
    console.log("Connected to MQTT broker");

    // Subscribe to the movement topic
    client.subscribe(movementTopic, (err) => {
        if (!err) {
            console.log(`Subscribed to ${movementTopic}`);  // Fixed template literal
        } else {
            console.error(`Failed to subscribe to ${movementTopic}`, err);  // Fixed template literal
        }
    });

    // Subscribe to the chat topic
    client.subscribe("robot/chat", (err) => {
        if (!err) {
            console.log("Subscribed to robot/chat");
        } else {
            console.error("Failed to subscribe to robot/chat", err);
        }
    });
});

// Handle incoming messages from the MQTT broker
client.on("message", (topic, message) => {
    if (topic === movementTopic) {
        console.log("Received movement command on topic:", topic, "Message:", message.toString());
    } else if (topic === "robot/chat") {
        displayReceivedMessage(message.toString());
    }
});

// Function to send commands to the dynamic topic
function sendCommand(command) {
    console.log(`Sending command to ${movementTopic}:`, command);  // Fixed template literal
    client.publish(movementTopic, command);
    console.log("Sent command:", command);
    resetArrowGlow();  // Reset glow effect after command is sent
}

// Function to highlight an arrow (add glow effect)
function highlightArrow(direction) {
    const arrow = document.querySelector(`.arrow.${direction}`);
    if (arrow) {
        arrow.classList.add('glow');  // Add the glow class to the arrow
    }
}

// Function to reset the glow effect on all arrows
function resetArrowGlow() {
    const arrows = document.querySelectorAll('.arrow');
    arrows.forEach(arrow => {
        arrow.classList.remove('glow');  // Remove the glow class from all arrows
    });
}

// Keydown event listener for controlling robot movement
let isKeyPressed = {};  // Keep track of the state of each key

document.addEventListener("keydown", (event) => {
    const key = event.key.toLowerCase();

    if (isKeyPressed[key]) return;  // Prevent sending duplicate commands if the key is already pressed

    switch (key) {
        case "w":
            sendCommand("0");  // Forward
            highlightArrow('up');
            break;
        case "a":
            sendCommand("1");  // Left
            highlightArrow('left');
            break;
        case "d":
            sendCommand("3");  // Right
            highlightArrow('right');
            break;
        case "s":
            sendCommand("4");  // Backward
            highlightArrow('down');
            break;
        default:
            return;
    }

    isKeyPressed[key] = true;  // Mark the key as pressed
});

// Stop the bot and reset glow when any movement key is released
document.addEventListener("keyup", (event) => {
    const key = event.key.toLowerCase();

    if (["w", "a", "s", "d"].includes(key)) {
        sendCommand("2");  // Stop
        resetArrowGlow();  // Remove glow effect when the key is released
        isKeyPressed[key] = false;  // Mark the key as released
    }
});

// Function to send the message
// Function to send the message
function sendMessage() {
    const input = document.getElementById("chat-input");
    const message = input.value.trim();

    if (message) {
        const formattedMessage = `User ${player}: ${message}`;  // Dynamically add player number to the message
        // Publish the message to the MQTT chat topic
        client.publish("robot/chat", formattedMessage);
        console.log("Sent chat message:", formattedMessage);

        // Display the sent message in the chat box with pink color
        displaySentMessage(formattedMessage);

        // Clear the input box after sending the message
        input.value = "";  // Clear the field
        input.placeholder = "Type a message...";  // Reset the placeholder
    }
}


// Function to display sent message in the chat box with pink color
function displaySentMessage(message) {
    const chatBox = document.getElementById("chat-box");
    const sentMsg = document.createElement("p");
    sentMsg.classList.add("sent");
    sentMsg.textContent = message;
    sentMsg.style.color = "pink";  // Set the color of the text to pink
    chatBox.appendChild(sentMsg);
    chatBox.scrollTop = chatBox.scrollHeight;  // Scroll to the bottom
}

// Event listener for pressing "Enter" to send the message
document.getElementById("chat-input").addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        event.preventDefault();  // Prevent default action of "Enter" key (like form submission)
        sendMessage();  // Call sendMessage when "Enter" is pressed
    }
});

// Function to display received message in the chat box
function displayReceivedMessage(message) {
    const chatBox = document.getElementById("chat-box");
    const receivedMsg = document.createElement("p");
    receivedMsg.classList.add("received");
    receivedMsg.textContent = message;
    chatBox.appendChild(receivedMsg);
    chatBox.scrollTop = chatBox.scrollHeight;  // Scroll to the bottom
}


// Preset messages for quick commands with player number
const presetMessages = {
    "1": `User ${player}: Hello`,
    "2": `User ${player}: Noob`,
    "3": `User ${player}: KO`,
    "4": `User ${player}: LoL`,
    "5": `User ${player}: I am there`
};

// Event listener for preset messages based on number keys
document.addEventListener("keydown", (event) => {
    const key = event.key;

    if (presetMessages[key]) {
        sendCommandMessage(presetMessages[key]);  // Send preset message if key is 1-5
    }
});


// Event listener for preset messages based on number keys
document.addEventListener("keydown", (event) => {
    const key = event.key;

    if (presetMessages[key]) {
        sendCommandMessage(presetMessages[key]);  // Send preset message if key is 1-5
    }
});

// Function to send a preset command message
function sendCommandMessage(message) {
    // Publish the preset message to the MQTT topic
    client.publish("robot/chat", message);
    console.log("Sent preset message:", message);

    // Display the sent message in the chat box
    displaySentMessage(message);
}

// Handle typing state to disable controls while typing
let isTyping = false;

document.getElementById("chat-input").addEventListener("focus", () => {
    isTyping = true;  // User is typing, disable car controls
});

document.getElementById("chat-input").addEventListener("blur", () => {
    isTyping = false;  // User has stopped typing, enable car controls
});
