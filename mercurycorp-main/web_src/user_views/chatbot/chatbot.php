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
        </div>
    </nav>
    
    <div class="chat-container">
        <h3 class="text-center">Chat with our Medical Assistant</h3>
        <div id="chat-box">
            <div class='bot-message text-start'>
                <strong>Bot:</strong> Hello! What do you need assistance with today? 😊 <br>
                Choose an option below or type your question.
            </div>
            <div class="quick-options">
                <button class="btn btn-outline-primary" onclick="selectCategory('General Health')">General Health</button>
                <button class="btn btn-outline-primary" onclick="selectCategory('Masonic Policies')">Masonic Policies</button>
            </div>
        </div>

        <!-- Masonic Policies Selection (Hidden initially) -->
        <div id="policy-selection" style="display: none; margin-top: 15px;">
            <label for="policy-type">Select a Masonic Policy:</label>
            <select id="policy-type" class="form-control">
                <option value="Falls Management">Falls Management</option>
                <option value="Personal Belongings">Personal Belongings</option>
                <option value="Neurological Checks">Neurological Checks</option>
            </select>
        </div>

        <div class="input-group mt-3">
            <input type="text" id="user-message" class="form-control" placeholder="Ask a question..." onkeypress="handleKeyPress(event)">
            <button class="btn btn-primary" onclick="sendQuery()">Send</button>
        </div>
    </div>

    <script>
        let selectedCategory = "";
        let selectedPolicy = "";

        function selectCategory(category) {
            selectedCategory = category;
            document.getElementById("chat-box").innerHTML += `<div class='bot-message text-start'>Bot: You selected ${category}. Please proceed.</div>`;

            if (category === "Masonic Policies") {
                document.getElementById("policy-selection").style.display = "block";
            } else {
                document.getElementById("policy-selection").style.display = "none";
            }
        }

        function sendQuery() {
            selectedPolicy = document.getElementById("policy-type").value;
            const userMessageElement = document.getElementById("user-message");
            const query = userMessageElement.value.trim();

            if (query === '') {
                alert("Please enter a query.");
                return;
            }

            const chatBox = document.getElementById("chat-box");
            chatBox.innerHTML += `<div class='user-message text-end'>You: ${query}</div>`;
            userMessageElement.value = '';

            fetch('http://localhost:5000/get_protocol', {
                method: 'POST',
                body: JSON.stringify({ query: query, category: selectedCategory, policy: selectedPolicy }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                chatBox.innerHTML += `<div class='bot-message text-start'>Bot: ${data.answer}</div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                chatBox.innerHTML += `<div class='bot-message text-start'>Bot: An error occurred. Please try again.</div>`;
            });
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                sendQuery();
            }
        }
    </script>

</body>
</html>
