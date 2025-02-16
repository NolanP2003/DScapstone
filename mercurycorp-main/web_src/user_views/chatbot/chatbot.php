<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Chatbot</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="chatbot.css">
</head>
<body>
    
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Medical Chatbot</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="employee_dash.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="nurse_dash.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="chat-container">
        <h3 class="text-center">Chat with our Medical Assistant</h3>
        <div id="chat-box"></div>
        <div class="input-group mt-3">
            <input type="text" id="user-message" class="form-control" placeholder="Ask a question..." onkeypress="handleKeyPress(event)">
            <button class="btn btn-primary" onclick="sendQuery()">Send</button>
        </div>
    </div>
    
    <div class="footer">
        <p>&copy; 2025 Medical Chatbot. All rights reserved.</p>
    </div>
    
    <!-- Chatbot Script -->
    <script>
        function sendQuery() {
            const userMessageElement = document.getElementById('user-message');
            const query = userMessageElement.value.trim();

            if (query === '') {
                alert("Please enter a query.");
                return;
            }

            const chatBox = document.getElementById('chat-box');
            
            // Display user message
            const userMessageHTML = `<div class='user-message text-end'>You: ${query}</div>`;
            chatBox.innerHTML += userMessageHTML;
            userMessageElement.value = '';

            // Call Flask API
            fetch('http://localhost:5000/get_protocol', {
                method: 'POST',
                body: JSON.stringify({ query: query }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                let botResponse = data.status === 'success' 
                    ? data.answer 
                    : 'An error occurred. Please try again.';
                
                // Display bot response
                const botMessageHTML = `<div class='bot-message text-start'>Bot: ${botResponse}</div>`;
                chatBox.innerHTML += botMessageHTML;
                
                // Auto-scroll to latest message
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                chatBox.innerHTML += `<div class='bot-message text-start'>Bot: An error occurred. Please try again.</div>`;
            });
        }

        // Enable sending message with Enter key
        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendQuery();
            }
        }
    </script>
    
</body>
</html>
