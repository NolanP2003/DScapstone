<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Website with Chatbot</title>
    <link rel="stylesheet" href="chatbot.css">
</head>
<body>

    <button id="chatbot-toggle-button">
        Chat
    </button>

    <div id="chatbot-popup" style="display: none;">
        <div id="chatbot-header">
            <h3>Chat Assistant</h3>
            <div id="chatbot-header-controls">
                <button id="chatbot-minimize-button" title="Minimize">_</button>
                <button id="chatbot-close-button" title="Close">X</button>
            </div>
        </div>
        <div id="chatbot-body">
        <div id="initial-options">
            <button class="option-button" data-mode="general">General Health</button>
            <button class="option-button" data-mode="masonic">Masonic Policies</button>
        </div>

        </div>
        <div id="chatbot-input-area" style="display: none;">
            <textarea id="chatbot-input" placeholder="Type your message..." rows="1"></textarea>
            <button id="send-button">Send</button>
        </div>
    </div>

    <script>
        const chatbotPopup = document.getElementById('chatbot-popup');
        const chatbotToggleButton = document.getElementById('chatbot-toggle-button');
        const chatbotCloseButton = document.getElementById('chatbot-close-button');
        const chatbotMinimizeButton = document.getElementById('chatbot-minimize-button');
        const chatbotBody = document.getElementById('chatbot-body');
        const initialOptions = document.getElementById('initial-options');
        const optionButtons = document.querySelectorAll('.option-button');
        const chatbotInputArea = document.getElementById('chatbot-input-area');
        const chatbotInput = document.getElementById('chatbot-input');
        const sendButton = document.getElementById('send-button');
        const chatbotHeader = document.getElementById('chatbot-header');

        let currentMode = null;
        let isDragging = false;
        let offsetX, offsetY;
        let isCollapsed = false;

        const FLASK_BACKEND_URL = 'http://127.0.0.1:5000/chat';

        chatbotToggleButton.addEventListener('click', () => {
            chatbotPopup.style.display = 'block';
            if (!currentMode) {
                resetChat();
            }
        });

        chatbotCloseButton.addEventListener('click', () => {
            chatbotPopup.style.display = 'none';
        });

        chatbotMinimizeButton.addEventListener('click', () => {
            isCollapsed = !isCollapsed;
            chatbotPopup.classList.toggle('collapsed', isCollapsed);
            chatbotMinimizeButton.textContent = isCollapsed ? '+' : '_';
            chatbotMinimizeButton.title = isCollapsed ? 'Expand' : 'Minimize';
        });

        optionButtons.forEach(button => {
            button.addEventListener('click', () => {
                currentMode = button.getAttribute('data-mode');
                initialOptions.style.display = 'none';
                chatbotInputArea.style.display = 'flex';
                addMessage('bot', `You selected ${currentMode === 'general' ? 'General Health' : 'Masonic Policies'}. Please type your question below.`);
                chatbotInput.focus();
            });
        });

        sendButton.addEventListener('click', sendMessage);
        chatbotInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        });

        chatbotHeader.addEventListener('mousedown', (e) => {
            if (e.target.tagName === 'BUTTON') return;
            isDragging = true;
            offsetX = e.clientX - chatbotPopup.offsetLeft;
            offsetY = e.clientY - chatbotPopup.offsetTop;
            chatbotHeader.style.cursor = 'grabbing';
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        });

        function onMouseMove(e) {
            if (!isDragging) return;
            let newX = e.clientX - offsetX;
            let newY = e.clientY - offsetY;
            const maxX = window.innerWidth - chatbotPopup.offsetWidth;
            const maxY = window.innerHeight - chatbotPopup.offsetHeight;
            newX = Math.max(0, Math.min(newX, maxX));
            newY = Math.max(0, Math.min(newY, maxY));
            chatbotPopup.style.left = `${newX}px`;
            chatbotPopup.style.top = `${newY}px`;
            chatbotPopup.style.bottom = 'auto';
            chatbotPopup.style.right = 'auto';
        }

        function onMouseUp() {
            if (isDragging) {
                isDragging = false;
                chatbotHeader.style.cursor = 'move';
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            }
        }

        function resetChat() {
            chatbotBody.innerHTML = '';
            chatbotBody.appendChild(initialOptions);
            const initialPrompt = document.createElement('div');
            initialPrompt.classList.add('chat-message', 'bot-message');
            initialPrompt.innerHTML = `<div class="message-content">Hello! How can I help you today? Please select an option:</div>`;
            initialOptions.parentNode.insertBefore(initialPrompt, initialOptions);
            initialOptions.style.display = 'block';
            chatbotInputArea.style.display = 'none';
            currentMode = null;
            chatbotInput.value = '';
            isCollapsed = false;
            chatbotPopup.classList.remove('collapsed');
            chatbotMinimizeButton.textContent = '_';
            chatbotMinimizeButton.title = 'Minimize';
        }

        function addMessage(sender, message, isHtml = false) {
            const messageElement = document.createElement('div');
            messageElement.classList.add('chat-message', sender === 'user' ? 'user-message' : 'bot-message');
            const contentElement = document.createElement('div');
            contentElement.classList.add('message-content');
            if (isHtml) {
                contentElement.innerHTML = message;
            } else {
                contentElement.appendChild(document.createTextNode(message));
            }
            messageElement.appendChild(contentElement);
            chatbotBody.appendChild(messageElement);
        }

        function showTypingIndicator() {
            const typingElement = document.createElement('div');
            typingElement.classList.add('chat-message', 'bot-message', 'typing-indicator');
            typingElement.innerHTML = `
                <div class="message-content">
                    <span></span><span></span><span></span>
                </div>`;
            chatbotBody.appendChild(typingElement);
            return typingElement;
        }

        function removeTypingIndicator(indicatorElement) {
            if (indicatorElement && indicatorElement.parentNode === chatbotBody) {
                chatbotBody.removeChild(indicatorElement);
            }
        }

        function sendMessage() {
            const userMessage = chatbotInput.value.trim();
            if (!userMessage || !currentMode) return;
            addMessage('user', userMessage);
            chatbotInput.value = '';
            chatbotInput.style.height = '40px';
            const typingIndicator = showTypingIndicator();
            fetch(FLASK_BACKEND_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: userMessage, mode: currentMode }),
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(data => {
                removeTypingIndicator(typingIndicator);
                let botReply = data.reply;
                if (Array.isArray(botReply) && botReply.length > 0) {
                    botReply = `<ul>${botReply.map(item => `<li>${item}</li>`).join('')}</ul>`;
                    addMessage('bot', botReply, true);
                } else if (typeof botReply === 'string') {
                    addMessage('bot', botReply);
                } else {
                    addMessage('bot', "Sorry, I received an unexpected response format.");
                }
                setTimeout(() => {
                    addMessage('bot', "Do you have another question? (Type your question or close the chat)");
                }, 500);
            })
            .catch(error => {
                removeTypingIndicator(typingIndicator);
                console.error('Error sending message:', error);
                addMessage('bot', `Sorry, I encountered an error. Please try again later. (${error.message})`);
            });
        }

        chatbotInput.addEventListener('input', () => {
            chatbotInput.style.height = 'auto';
            chatbotInput.style.height = `${chatbotInput.scrollHeight}px`;
        });

        chatbotInputArea.style.display = 'none';
    </script>
</body>
</html>
