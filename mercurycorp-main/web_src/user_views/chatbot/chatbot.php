<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical Website with Chatbot</title>
    <link rel="stylesheet" href="chatbot.css">
</head>
<body>

    <button id="chatbot-toggle-button" title="Open Chat">
        <!-- SVG Chat Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
            <path d="M20 2H4C2.9 2 2 2.9 2 4v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H6l-2 2V4h16v12z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg>
    </button>

    <div id="chatbot-popup">
        <div id="chatbot-header">
            <h3>Chat Assistant</h3>
            <div id="chatbot-header-controls">
                <button id="chatbot-minimize-button" title="Minimize">
                    <!-- SVG Minimize Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 13H5v-2h14v2z"/>
                        <path d="M0 0h24v24H0z" fill="none"/>
                    </svg>
                </button>
                <button id="chatbot-close-button" title="Close">
                    <!-- SVG Close Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12 19 6.41z"/>
                        <path d="M0 0h24v24H0z" fill="none"/>
                    </svg>
                </button>
            </div>
        </div>
        <div id="chatbot-body">
            <!-- Initial options and messages will be populated here by JS -->
            <div id="initial-options">
                <button class="option-button" data-mode="general">General Health</button>
                <button class="option-button" data-mode="masonic">Medical Policies</button>
            </div>
        </div>
        <div id="chatbot-input-area">
            <textarea id="chatbot-input" placeholder="Type your message..." rows="1"></textarea>
            <button id="send-button" title="Send Message">
                <!-- SVG Send Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>
                    <path d="M0 0h24v24H0z" fill="none"/>
                </svg>
            </button>
        </div>
    </div>

    <script>
        const chatbotPopup = document.getElementById('chatbot-popup');
        const chatbotToggleButton = document.getElementById('chatbot-toggle-button');
        const chatbotCloseButton = document.getElementById('chatbot-close-button');
        const chatbotMinimizeButton = document.getElementById('chatbot-minimize-button');
        const chatbotBody = document.getElementById('chatbot-body');
        const initialOptions = document.getElementById('initial-options');
        const optionButtons = document.querySelectorAll('#initial-options .option-button');
        const chatbotInputArea = document.getElementById('chatbot-input-area');
        const chatbotInput = document.getElementById('chatbot-input');
        const sendButton = document.getElementById('send-button');
        const chatbotHeader = document.getElementById('chatbot-header');

        // SVGs for Minimize/Expand icons
        const minimizeIconSVG = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                <path d="M19 13H5v-2h14v2z"/>
                <path d="M0 0h24v24H0z" fill="none"/>
            </svg>`;
        const expandIconSVG = `
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                 <path d="M0 0h24v24H0z" fill="none"/>
                 <path d="M4 20h16v2H4zM4 2h16v2H4zm9 6l-4 4h3v6h2v-6h3l-4-4z"/>
            </svg>`;

        let currentMode = null;
        let isDragging = false;
        let offsetX, offsetY;
        let isCollapsed = false;

        const FLASK_BACKEND_URL = 'http://127.0.0.1:5000/chat';

        function initializeChat() {
            resetChat();
            chatbotInputArea.style.display = 'none';
            chatbotPopup.style.opacity = '0';
            chatbotPopup.style.display = 'none';
        }

        chatbotToggleButton.addEventListener('click', () => {
            const isVisible = chatbotPopup.style.display === 'flex';
            if (!isVisible) {
                chatbotPopup.style.display = 'flex';
                void chatbotPopup.offsetWidth;
                chatbotPopup.style.opacity = '1';
                chatbotToggleButton.title = "Close Chat";
                // Only reset fully if it was completely closed AND no mode was active
                // Or if opening for the very first time.
                const isFirstOpen = chatbotBody.children.length <= 1; // Check if only initial-options div is present
                if (!currentMode || isFirstOpen) {
                     resetChat();
                }
                if (isCollapsed) {
                    toggleMinimize();
                }
                // If a mode is selected, ensure input is visible
                if(currentMode) {
                    chatbotInputArea.style.display = 'flex';
                    chatbotInput.focus();
                }
            } else {
                 chatbotPopup.style.opacity = '0';
                 setTimeout(() => {
                    chatbotPopup.style.display = 'none';
                    chatbotToggleButton.title = "Open Chat";
                 }, 300);
            }
        });

        chatbotCloseButton.addEventListener('click', () => {
            chatbotPopup.style.opacity = '0';
            setTimeout(() => {
                 chatbotPopup.style.display = 'none';
                 chatbotToggleButton.title = "Open Chat";
            }, 300);
        });

        function toggleMinimize() {
             isCollapsed = !isCollapsed;
             chatbotPopup.classList.toggle('collapsed', isCollapsed);
             chatbotMinimizeButton.innerHTML = isCollapsed ? expandIconSVG : minimizeIconSVG;
             chatbotMinimizeButton.title = isCollapsed ? 'Expand' : 'Minimize';
        }

        chatbotMinimizeButton.addEventListener('click', toggleMinimize);

        // Listener for INITIAL option buttons ONLY
        optionButtons.forEach(button => {
            button.addEventListener('click', () => {
                currentMode = button.getAttribute('data-mode');
                initialOptions.style.display = 'none'; // Hide initial options container
                 const initialPrompt = chatbotBody.querySelector('.initial-prompt');
                 if (initialPrompt) {
                     initialPrompt.remove();
                 }
                chatbotInputArea.style.display = 'flex';
                addMessage('bot', `You selected ${currentMode === 'general' ? 'General Health' : 'Medical Policies'}. How can I assist you with this topic?`);
                chatbotInput.focus();
                 scrollToBottom();
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
            if (e.target.closest('button')) return;
            isDragging = true;
            const rect = chatbotPopup.getBoundingClientRect();
            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;
            chatbotHeader.style.cursor = 'grabbing';
            document.body.style.userSelect = 'none';
            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp, { once: true });
        });

        function onMouseMove(e) {
            if (!isDragging) return;
            e.preventDefault();
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
                document.body.style.userSelect = '';
                document.removeEventListener('mousemove', onMouseMove);
            }
        }

        function resetChat() {
            chatbotBody.innerHTML = ''; // Clear everything
            chatbotBody.appendChild(initialOptions); // Add the container back

            // Add the initial greeting message
             const initialPrompt = document.createElement('div');
             initialPrompt.classList.add('chat-message', 'bot-message', 'initial-prompt');
             initialPrompt.innerHTML = `<div class="message-content">Hello! How can I help you today? Please select an option:</div>`;
            chatbotBody.insertBefore(initialPrompt, initialOptions); // Insert greeting before options

            initialOptions.style.display = 'block'; // Show initial options
            chatbotInputArea.style.display = 'none'; // Hide input area
            currentMode = null;
            chatbotInput.value = '';
            chatbotInput.style.height = '40px';

            if (isCollapsed) {
                 toggleMinimize();
             } else {
                chatbotMinimizeButton.innerHTML = minimizeIconSVG;
                chatbotMinimizeButton.title = 'Minimize';
             }

             chatbotPopup.style.left = 'auto';
             chatbotPopup.style.top = 'auto';
             chatbotPopup.style.bottom = '100px';
             chatbotPopup.style.right = '25px';
        }

        function addMessage(sender, message, isHtml = false) {
            // Remove "next action" buttons if they exist when adding any new message
            const existingNextActionOptions = chatbotBody.querySelector('.next-action-options');
            if (existingNextActionOptions) {
                 existingNextActionOptions.remove();
            }

            const messageWrapper = document.createElement('div');
            messageWrapper.classList.add('chat-message', sender === 'user' ? 'user-message' : 'bot-message');
            const contentElement = document.createElement('div');
            contentElement.classList.add('message-content');

            if (isHtml) {
                const cleanHtml = message.replace(/<script.*?>.*?<\/script>/gi, '');
                contentElement.innerHTML = cleanHtml;
            } else {
                contentElement.textContent = message;
            }

            messageWrapper.appendChild(contentElement);
            chatbotBody.appendChild(messageWrapper);
            scrollToBottom();
        }

        function showTypingIndicator() {
            const existingIndicator = chatbotBody.querySelector('.typing-indicator');
            if (existingIndicator) existingIndicator.remove();
            // Remove "next action" buttons when showing typing indicator
            const existingNextActionOptions = chatbotBody.querySelector('.next-action-options');
            if (existingNextActionOptions) existingNextActionOptions.remove();


            const typingElement = document.createElement('div');
            typingElement.classList.add('chat-message', 'bot-message', 'typing-indicator');
            typingElement.innerHTML = `
                <div class="message-content">
                    <span></span><span></span><span></span>
                </div>`;
            chatbotBody.appendChild(typingElement);
            scrollToBottom();
            return typingElement;
        }

        function removeTypingIndicator() {
             const indicator = chatbotBody.querySelector('.typing-indicator');
             if (indicator) indicator.remove();
        }

         function scrollToBottom() {
            setTimeout(() => {
                chatbotBody.scrollTop = chatbotBody.scrollHeight;
            }, 50); // Small delay can help ensure render is complete
         }

        function promptForNextAction() {
             // Add the prompt message
             const promptMessage = document.createElement('div');
             promptMessage.classList.add('chat-message', 'bot-message');
             promptMessage.innerHTML = `<div class="message-content">Is there anything else I can help you with? Feel free to ask another question, or select a topic below to switch.</div>`;
             chatbotBody.appendChild(promptMessage);

             // Create container for the buttons
             const optionsContainer = document.createElement('div');
             optionsContainer.classList.add('next-action-options');

             // Create buttons
             const btnGeneral = document.createElement('button');
             btnGeneral.classList.add('next-option-button');
             btnGeneral.setAttribute('data-mode', 'general');
             btnGeneral.textContent = 'General Health';

             const btnMasonic = document.createElement('button');
             btnMasonic.classList.add('next-option-button');
             btnMasonic.setAttribute('data-mode', 'masonic');
             btnMasonic.textContent = 'Medical Policies';

             // Add listeners to THESE buttons
             [btnGeneral, btnMasonic].forEach(button => {
                 button.addEventListener('click', () => {
                     const newMode = button.getAttribute('data-mode');
                     currentMode = newMode;
                     optionsContainer.remove();
     
                     if (promptMessage.parentNode === chatbotBody) {
                        promptMessage.remove();
                     }

                     addMessage('bot', `Okay, switching to ${newMode === 'general' ? 'General Health' : 'Medical Policies'}. What is your question?`);

                     chatbotInputArea.style.display = 'flex';
                     chatbotInput.focus();
                     scrollToBottom();
                 });
             });

             optionsContainer.appendChild(btnGeneral);
             optionsContainer.appendChild(btnMasonic);
             chatbotBody.appendChild(optionsContainer);

             scrollToBottom();
        }


        function sendMessage() {
            // If user types instead of clicking button, remove the next action buttons first
            const existingNextActionOptions = chatbotBody.querySelector('.next-action-options');
            if (existingNextActionOptions) {
                 const previousMessage = existingNextActionOptions.previousElementSibling;
                 if (previousMessage && previousMessage.classList.contains('bot-message')) {
                    previousMessage.remove();
                 }
                 existingNextActionOptions.remove();
            }

            const userMessage = chatbotInput.value.trim();
            if (!userMessage || !currentMode) return;

            addMessage('user', userMessage);
            chatbotInput.value = '';
            chatbotInput.style.height = '40px';
            chatbotInput.focus();

            const typingIndicator = showTypingIndicator();

            fetch(FLASK_BACKEND_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                     'Accept': 'application/json'
                },
                body: JSON.stringify({ message: userMessage, mode: currentMode }),
            })
            .then(response => {
                if (!response.ok) {
                     return response.text().then(text => {
                        throw new Error(`HTTP error! status: ${response.status}, message: ${text || 'Server error'}`);
                     });
                }
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.indexOf("application/json") !== -1) {
                     return response.json();
                } else {
                    return response.text().then(text => {
                        throw new Error(`Expected JSON response, but received: ${contentType}. Content: ${text}`);
                    });
                }
            })
            .then(data => {
                removeTypingIndicator();
                let botReply = data.reply;

                if (Array.isArray(botReply) && botReply.length > 0) {
                     botReply = `<ul>${botReply.map(item => `<li>${item}</li>`).join('')}</ul>`;
                     addMessage('bot', botReply, true);
                } else if (typeof botReply === 'string' && botReply.trim() !== '') {
                     const containsHtml = /<[a-z][\s\S]*>/i.test(botReply);
                     addMessage('bot', botReply, containsHtml);
                } else if (!botReply) {
                     addMessage('bot', "Sorry, I didn't get a specific response. Can you try rephrasing?");
                } else {
                    console.warn("Received unexpected response format:", data);
                    addMessage('bot', "Sorry, I received an unexpected response format.");
                }
                 promptForNextAction();

            })
            .catch(error => {
                removeTypingIndicator();
                console.error('Error sending/receiving message:', error);
                addMessage('bot', `Sorry, I encountered an error. Please check the connection or try again later. (${error.message})`);
            });
        }

        chatbotInput.addEventListener('input', () => {
            chatbotInput.style.height = 'auto';
            const maxHeight = parseInt(window.getComputedStyle(chatbotInput).maxHeight, 10);
            const newHeight = Math.min(chatbotInput.scrollHeight, maxHeight);
            chatbotInput.style.height = `${newHeight}px`;
        });

        initializeChat();

    </script>

</body>
</html>