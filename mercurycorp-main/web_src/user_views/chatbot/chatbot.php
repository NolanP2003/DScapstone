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

        <div id="policy-selection" style="display: none; margin-top: 15px;">
            <label for="policy-type">Select a Masonic Policy:</label>
            <div class="quick-options">
                <button class="btn btn-outline-primary" onclick="selectProcedure('Falls Procedure')">Falls Procedure</button>
                <button class="btn btn-outline-primary" onclick="selectProcedure('Personal Belongings Procedure')">Personal Belongings Procedure</button>
                <button class="btn btn-outline-primary" onclick="selectProcedure('Neurological Check Procedure')">Neurological Check Procedure</button>
            </div>
        </div>

        <div id="keyword-selection" style="display: none; margin-top: 15px;">
            <p>Do you need a keyword defined?</p>
            <button class="btn btn-outline-primary" onclick="askForKeyword()">Yes</button>
            <button class="btn btn-outline-primary" onclick="askForProcedure()">No</button>
        </div>

        <div id="keyword-input" style="display: none; margin-top: 15px;">
            <label for="keyword-query">Type the keyword that needs defined:</label>
            <input type="text" id="keyword-query" class="form-control" placeholder="Type keyword...">
            <button class="btn btn-primary mt-2" onclick="sendKeywordQuery()">Get Definition</button>
        </div>

        <div id="procedure-name-input" style="display: none; margin-top: 15px;">
            <label for="procedure-name">Enter the name of the procedure:</label>
            <input type="text" id="procedure-name" class="form-control" placeholder="Enter procedure name...">
            <button class="btn btn-primary mt-2" onclick="sendFallPolicy()">Get Policy</button>
        </div>

        <div class="input-group mt-3" id="question-input" style="display: none;">
            <input type="text" id="user-message" class="form-control" placeholder="Enter your medical query..." onkeypress="handleKeyPress(event)">
            <button class="btn btn-primary" onclick="sendQuery()">Send</button>
        </div>


    </div>

    <script>
        function sendFallsQuery() {
            const procedure = document.getElementById("falls-procedure").value.trim();
            if (procedure === '') {
                alert("Please enter a fall procedure name.");
                return;
            }

            fetch('http://localhost:5000/get_falls_policy', {
                method: 'POST',
                body: JSON.stringify({ procedure: procedure }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                let chatBox = document.getElementById("chat-box");

                if (data.Definition && data.Definition !== "No definition found for the keyword.") {
                    chatBox.innerHTML += `<div class='bot-message text-start'>Bot: ${data.Definition}</div>`;
                } else {
                    chatBox.innerHTML += `<div class='bot-message text-start'>Bot: No definition found for that procedure.</div>`;
                }

                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById("chat-box").innerHTML += `<div class='bot-message text-start'>Bot: An error occurred. Please try again.</div>`;
            });
        }


        function selectProcedure(procedure) {
            selectedProcedure = procedure;
            document.getElementById("chat-box").innerHTML += `<div class='bot-message text-start'>Bot: You selected ${procedure}. Please proceed.</div>`;

            // Show keyword selection only for Falls Procedure
            document.getElementById("keyword-selection").style.display = procedure === "Falls Procedure" ? "block" : "none";

            // Hide keyword input and question input initially
            document.getElementById("keyword-input").style.display = "none";
            document.getElementById("question-input").style.display = procedure !== "Falls Procedure" ? "block" : "none";

            // Reset procedure name input
            document.getElementById("procedure-name-input").style.display = "none";
            document.getElementById("procedure-name").value = "";
        }

        function askForProcedureName() {
            document.getElementById("keyword-input").style.display = "none";
            document.getElementById("question-input").style.display = "none";
            document.getElementById("procedure-name-input").style.display = "block";
        }
    </script>

    <script>
        let selectedCategory = "";
        let selectedProcedure = "";
        let keywordMode = "";

        function selectCategory(category) {
            selectedCategory = category;
            document.getElementById("chat-box").innerHTML += `<div class='bot-message text-start'>Bot: You selected ${category}. Please proceed.</div>`;

            if (category === "General Health") {
                // clear previous things
                document.getElementById("policy-selection").style.display = "none";
                document.getElementById("keyword-selection").style.display = "none";
                document.getElementById("keyword-input").style.display = "none";
                document.getElementById("procedure-name-input").style.display = "none";
                // show a unique input field that is only for this occurance
                document.getElementById("question-input").style.display = "block";


            } else if (category === "Masonic Policies") {
                document.getElementById("policy-selection").style.display = "block";
                document.getElementById("question-input").style.display = "none"; 
            }
        }



        function selectProcedure(procedure) {
            selectedProcedure = procedure;
            document.getElementById("chat-box").innerHTML += `<div class='bot-message text-start'>Bot: You selected ${procedure}. Please proceed.</div>`;

            document.getElementById("question-input").style.display = "none";
            document.getElementById("procedure-name-input").style.display = "none";

            if (procedure === "Falls Procedure") {
                document.getElementById("keyword-selection").style.display = "block";
                document.getElementById("question-input").style.display = "none";
            } else {
                document.getElementById("keyword-selection").style.display = "none";
                document.getElementById("question-input").style.display = "block";
            }
        }


        function askForKeyword() {
            keywordMode = "define";
            document.getElementById("keyword-input").style.display = "block";
            document.getElementById("question-input").style.display = "none";
        }

        function askForProcedure() {
            keywordMode = "procedure";
            document.getElementById("keyword-input").style.display = "none";
            document.getElementById("question-input").style.display = "none";
            document.getElementById("procedure-name-input").style.display = "block";
        }

        function askForQuestion() {
            keywordMode = "ask";
            document.getElementById("keyword-input").style.display = "none";
            document.getElementById("question-input").style.display = "block";
        }


        function sendKeywordQuery() {
            const keyword = document.getElementById("keyword-query").value.trim();
            if (keyword === '') {
                alert("Please enter a keyword.");
                return;
            }

            fetch('http://localhost:5000/keyword_definition', {
                method: 'POST',
                body: JSON.stringify({ keyword: keyword }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                let chatBox = document.getElementById("chat-box");
                
                // Ensure we output the correct definition or error message
                if (data.Definition && data.Definition !== "No definition found for the keyword.") {
                    chatBox.innerHTML += `<div class='bot-message text-start'>Bot: ${data.Definition}</div>`;
                } else {
                    chatBox.innerHTML += `<div class='bot-message text-start'>Bot: No definition found.</div>`;
                }
                
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById("chat-box").innerHTML += `<div class='bot-message text-start'>Bot: An error occurred. Please try again.</div>`;
            });
        }


        function sendFallPolicy() {
            const procedure = document.getElementById("procedure-name").value.trim();
            if (procedure === '') {
                alert("Please enter a procedure name.");
                return;
            }

            fetch('http://localhost:5000/get_falls_policy', {
                method: 'POST',
                body: JSON.stringify({ procedure: procedure }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                let chatBox = document.getElementById("chat-box");

                if (data.Definition && data.Definition !== "No definition found for the keyword.") {
                    chatBox.innerHTML += `<div class='bot-message text-start'>Bot: ${data.Definition}</div>`;
                } else {
                    chatBox.innerHTML += `<div class='bot-message text-start'>Bot: No definition found for that procedure.</div>`;
                }

                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById("chat-box").innerHTML += `<div class='bot-message text-start'>Bot: An error occurred. Please try again.</div>`;
            });
        }

        function showTypingIndicator() {
            let chatBox = document.getElementById("chat-box");
            let typingIndicator = document.createElement("div");
            typingIndicator.classList.add("bot-message", "typing-indicator");
            typingIndicator.innerHTML = `<span></span><span></span><span></span>`;
            chatBox.appendChild(typingIndicator);
            chatBox.scrollTop = chatBox.scrollHeight;
            return typingIndicator;
        }

        function removeTypingIndicator(typingIndicator) {
            typingIndicator.remove();
        }

        function sendQuery() {
            const query = document.getElementById("user-message").value.trim();
            if (query === '') {
                alert("Please enter a query.");
                return;
            }

            let chatBox = document.getElementById("chat-box");
            chatBox.innerHTML += `<div class='user-message text-end'>You: ${query}</div>`;
            document.getElementById("user-message").value = "";

            let typingIndicator = showTypingIndicator();

            fetch('http://localhost:5000/get_protocol', {
                method: 'POST',
                body: JSON.stringify({ query: query, procedure: selectedProcedure }),
                headers: { 'Content-Type': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                removeTypingIndicator(typingIndicator);
                chatBox.innerHTML += `<div class='bot-message text-start'>Bot: ${data.answer}</div>`;
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => {
                console.error('Error:', error);
                removeTypingIndicator(typingIndicator);
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
