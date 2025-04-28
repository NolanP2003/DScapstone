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
        // ... (keep existing constants and variable declarations) ...
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

        const FLASK_BACKEND_URL = 'http://127.0.0.1:5000/chat'; // Ensure this is correct

        function initializeChat() {
            resetChat();
            // Note: Visibility/display handled by toggle button now
        }

        // --- TOGGLE/CLOSE/MINIMIZE LISTENERS (Keep as is) ---
        chatbotToggleButton.addEventListener('click', () => {
            const isVisible = chatbotPopup.classList.contains('visible');
            if (!isVisible) {
                // Opening the chat
                chatbotPopup.style.display = 'flex'; // Make it available for transition
                requestAnimationFrame(() => { // Allow display:flex to apply
                    chatbotPopup.classList.add('visible');
                });
                chatbotToggleButton.title = "Close Chat";

                const isFirstOpenOrReset = chatbotBody.children.length <= 1 || !currentMode;
                if (isFirstOpenOrReset) {
                     resetChat(); // Resets content and ensures initial prompt/options show
                } else if (isCollapsed) {
                    toggleMinimize(); // If reopening while collapsed, expand it
                }

                // Focus input if a mode is already selected
                if(currentMode && !isCollapsed) {
                    chatbotInputArea.style.display = 'flex';
                    chatbotInput.focus();
                } else if (!currentMode) {
                    // Ensure initial options are visible if no mode selected
                    initialOptions.style.display = 'block';
                    chatbotInputArea.style.display = 'none';
                }

            } else {
                 // Closing the chat
                 chatbotPopup.classList.remove('visible');
                 // Use transitionend event for cleaner display:none handling
                 chatbotPopup.addEventListener('transitionend', () => {
                    if (!chatbotPopup.classList.contains('visible')) { // Check again in case it reopened quickly
                         chatbotPopup.style.display = 'none';
                    }
                 }, { once: true });
                 chatbotToggleButton.title = "Open Chat";
            }
        });

        chatbotCloseButton.addEventListener('click', () => {
            chatbotPopup.classList.remove('visible');
            chatbotPopup.addEventListener('transitionend', () => {
               if (!chatbotPopup.classList.contains('visible')) {
                    chatbotPopup.style.display = 'none';
                    chatbotToggleButton.title = "Open Chat";
                    // Optionally fully reset when closed:
                    // resetChat();
               }
            }, { once: true });
        });

        function toggleMinimize() {
             isCollapsed = !isCollapsed;
             chatbotPopup.classList.toggle('collapsed', isCollapsed);
             chatbotMinimizeButton.innerHTML = isCollapsed ? expandIconSVG : minimizeIconSVG;
             chatbotMinimizeButton.title = isCollapsed ? 'Expand' : 'Minimize';
             // If expanding, ensure input area is visible if a mode is selected
             if (!isCollapsed && currentMode) {
                chatbotInputArea.style.display = 'flex';
             } else if (isCollapsed) {
                 chatbotInputArea.style.display = 'none'; // Hide input when minimizing
             }
        }
        chatbotMinimizeButton.addEventListener('click', toggleMinimize);


        // --- DRAGGING LOGIC (Keep as is) ---
         chatbotHeader.addEventListener('mousedown', (e) => {
            if (e.target.closest('button')) return;
            isDragging = true;
            const rect = chatbotPopup.getBoundingClientRect();
            // Calculate offset relative to the viewport, not the element's current CSS top/left
            offsetX = e.clientX - rect.left;
            offsetY = e.clientY - rect.top;
            chatbotHeader.style.cursor = 'grabbing';
            document.body.style.userSelect = 'none'; // Prevent text selection during drag

            // Ensure position is calculated absolutely after first drag
            const computedStyle = window.getComputedStyle(chatbotPopup);
            const currentTop = parseFloat(computedStyle.top) || rect.top;
            const currentLeft = parseFloat(computedStyle.left) || rect.left;
            chatbotPopup.style.top = `${currentTop}px`;
            chatbotPopup.style.left = `${currentLeft}px`;
            chatbotPopup.style.bottom = 'auto';
            chatbotPopup.style.right = 'auto';

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp, { once: true });
        });

        function onMouseMove(e) {
            if (!isDragging) return;
            e.preventDefault(); // Prevent potential text selection dragging issues
            let newX = e.clientX - offsetX;
            let newY = e.clientY - offsetY;

            // Prevent dragging off-screen (consider header height for bottom boundary)
            const maxX = window.innerWidth - chatbotPopup.offsetWidth;
            const maxY = window.innerHeight - chatbotPopup.offsetHeight;
            newX = Math.max(0, Math.min(newX, maxX));
            newY = Math.max(0, Math.min(newY, maxY));

            chatbotPopup.style.left = `${newX}px`;
            chatbotPopup.style.top = `${newY}px`;
        }

        function onMouseUp() {
            if (isDragging) {
                isDragging = false;
                chatbotHeader.style.cursor = 'move';
                document.body.style.userSelect = ''; // Re-enable text selection
                document.removeEventListener('mousemove', onMouseMove);
                // No need to remove mouseup listener here due to {once: true}
            }
        }


        // --- MODE SELECTION ---
        optionButtons.forEach(button => {
            button.addEventListener('click', () => {
                const selectedMode = button.getAttribute('data-mode');
                const modeName = selectedMode === 'general' ? 'General Health' : 'Medical Policies';
                currentMode = selectedMode;

                // Clear initial prompt and options
                const initialPrompt = chatbotBody.querySelector('.initial-prompt');
                if (initialPrompt) initialPrompt.remove();
                initialOptions.style.display = 'none';

                // **MODIFIED:** Clearer transition message
                const topic = selectedMode === 'general' ? 'health topic' : 'policy';
                addMessage('bot', `Okay, focusing on ${modeName}. What specific ${topic} can I help you find information about?`);

                chatbotInputArea.style.display = 'flex';
                chatbotInput.focus();
                scrollToBottom();
            });
        });

        // --- CORE CHAT FUNCTIONS ---
        sendButton.addEventListener('click', sendMessage);
        chatbotInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault(); // Prevent newline in textarea
                sendMessage();
            }
        });

        function resetChat() {
            chatbotBody.innerHTML = ''; // Clear previous messages
            chatbotBody.appendChild(initialOptions); // Add options container back

            // **MODIFIED:** Dynamic Greeting
            const hour = new Date().getHours();
            let greeting = "Hello";
            if (hour < 12) {
                greeting = "Good morning";
            } else if (hour < 18) {
                greeting = "Good afternoon";
            } else {
                greeting = "Good evening";
            }

            const initialPrompt = document.createElement('div');
            initialPrompt.classList.add('chat-message', 'bot-message', 'initial-prompt');
            // Use innerHTML for potential bolding or styling later
            initialPrompt.innerHTML = `<div class="message-content">${greeting}! I'm here to help. Please select a topic area to begin:</div>`;
            chatbotBody.insertBefore(initialPrompt, initialOptions); // Insert greeting *before* options

            initialOptions.style.display = 'block'; // Make sure options are visible
            chatbotInputArea.style.display = 'none'; // Hide input initially
            currentMode = null;
            chatbotInput.value = '';
            adjustTextareaHeight(); // Reset textarea height

            // Reset visual state (if needed)
            if (isCollapsed) {
                 toggleMinimize(); // Un-collapse if resetting
             }
            chatbotMinimizeButton.innerHTML = minimizeIconSVG;
            chatbotMinimizeButton.title = 'Minimize';

             // Reset position to default
             chatbotPopup.style.left = 'auto';
             chatbotPopup.style.top = 'auto';
             chatbotPopup.style.bottom = '100px';
             chatbotPopup.style.right = '25px';

             scrollToBottom(); // Scroll to top essentially, as content is minimal
        }

        function addMessage(sender, message, isHtml = false) {
            // Remove any existing "next action" buttons or prompts before adding a new message
            const existingNextActionOptions = chatbotBody.querySelector('.next-action-options');
            if (existingNextActionOptions) {
                 const previousPrompt = existingNextActionOptions.previousElementSibling;
                 // Also remove the "Is there anything else..." prompt message
                 if (previousPrompt && previousPrompt.classList.contains('bot-message') && previousPrompt.dataset.isPrompt === 'true') {
                     previousPrompt.remove();
                 }
                 existingNextActionOptions.remove();
            }

            const messageWrapper = document.createElement('div');
            messageWrapper.classList.add('chat-message', sender === 'user' ? 'user-message' : 'bot-message');
            const contentElement = document.createElement('div');
            contentElement.classList.add('message-content');

            if (isHtml) {
                // Basic sanitization (consider a more robust library like DOMPurify if security is critical)
                const cleanHtml = message.replace(/<script.*?>.*?<\/script>/gi, ''); // Remove script tags
                contentElement.innerHTML = cleanHtml; // Use innerHTML for lists etc.
            } else {
                contentElement.textContent = message; // Safer for plain text
            }

            messageWrapper.appendChild(contentElement);
            chatbotBody.appendChild(messageWrapper);
            scrollToBottom(); // Scroll down after adding message
        }

        function showTypingIndicator() {
            // Remove existing indicators and next action prompts first
            removeTypingIndicator();
            const existingNextActionOptions = chatbotBody.querySelector('.next-action-options');
            if (existingNextActionOptions) {
                 const previousPrompt = existingNextActionOptions.previousElementSibling;
                 if (previousPrompt && previousPrompt.classList.contains('bot-message') && previousPrompt.dataset.isPrompt === 'true') {
                     previousPrompt.remove();
                 }
                 existingNextActionOptions.remove();
            }

            const typingElement = document.createElement('div');
            typingElement.classList.add('chat-message', 'bot-message', 'typing-indicator');
            typingElement.innerHTML = `
                <div class="message-content">
                    <span></span><span></span><span></span>
                </div>`;
            chatbotBody.appendChild(typingElement);
            scrollToBottom();
            return typingElement; // Return element if needed elsewhere, though usually just removed
        }

        function removeTypingIndicator() {
             const indicator = chatbotBody.querySelector('.typing-indicator');
             if (indicator) indicator.remove();
        }

        function scrollToBottom() {
           // Using requestAnimationFrame ensures scrolling happens after DOM updates
           requestAnimationFrame(() => {
               chatbotBody.scrollTop = chatbotBody.scrollHeight;
           });
         }

        function promptForNextAction() {
             // Ensure previous prompts/buttons are gone
            const existingNextActionOptions = chatbotBody.querySelector('.next-action-options');
            if (existingNextActionOptions) {
                 const previousPrompt = existingNextActionOptions.previousElementSibling;
                 if (previousPrompt && previousPrompt.classList.contains('bot-message') && previousPrompt.dataset.isPrompt === 'true') {
                     previousPrompt.remove();
                 }
                 existingNextActionOptions.remove();
            }


             // **MODIFIED:** Refined prompt message
             const promptMessage = document.createElement('div');
             promptMessage.classList.add('chat-message', 'bot-message');
             promptMessage.dataset.isPrompt = 'true'; // Mark this as the prompt message
             const currentModeName = currentMode === 'general' ? 'General Health' : 'Medical Policies';
             promptMessage.innerHTML = `<div class="message-content">Finished with that query. Need help with anything else in ${currentModeName}? Feel free to ask another question, or switch topics below.</div>`;
             chatbotBody.appendChild(promptMessage);

             // Create container for the buttons
             const optionsContainer = document.createElement('div');
             optionsContainer.classList.add('next-action-options'); // Use distinct class if needed

             // Create buttons (reuse existing classes/styles)
             const btnGeneral = document.createElement('button');
             btnGeneral.classList.add('next-option-button'); // Use class defined in CSS
             btnGeneral.setAttribute('data-mode', 'general');
             btnGeneral.textContent = 'Switch to General Health';

             const btnMasonic = document.createElement('button');
             btnMasonic.classList.add('next-option-button'); // Use class defined in CSS
             btnMasonic.setAttribute('data-mode', 'masonic');
             btnMasonic.textContent = 'Switch to Medical Policies';

             // Add listeners to THESE 'next action' buttons
             [btnGeneral, btnMasonic].forEach(button => {
                 button.addEventListener('click', () => {
                     const newMode = button.getAttribute('data-mode');
                     const newModeName = newMode === 'general' ? 'General Health' : 'Medical Policies';
                     currentMode = newMode; // Update the current mode

                     // Remove the prompt message and the buttons
                     optionsContainer.remove();
                     if (promptMessage.parentNode === chatbotBody) {
                         promptMessage.remove();
                     }

                     // **MODIFIED:** Clearer transition message upon switching
                     const newTopic = newMode === 'general' ? 'health topic' : 'policy';
                     addMessage('bot', `Okay, switching focus to ${newModeName}. What specific ${newTopic} would you like to ask about?`);

                     chatbotInputArea.style.display = 'flex'; // Ensure input is visible
                     chatbotInput.focus();
                     scrollToBottom();
                 });
             });

             optionsContainer.appendChild(btnGeneral);
             optionsContainer.appendChild(btnMasonic);
             chatbotBody.appendChild(optionsContainer); // Append the buttons container

             scrollToBottom(); // Scroll to show the prompt and buttons
        }


        function sendMessage() {
            // If the user types while the 'next action' prompt is visible, clear it first.
            const existingNextActionOptions = chatbotBody.querySelector('.next-action-options');
            if (existingNextActionOptions) {
                 const previousPrompt = existingNextActionOptions.previousElementSibling;
                 if (previousPrompt && previousPrompt.classList.contains('bot-message') && previousPrompt.dataset.isPrompt === 'true') {
                    previousPrompt.remove();
                 }
                 existingNextActionOptions.remove();
            }

            const userMessage = chatbotInput.value.trim();
            if (!userMessage) {
                // Maybe add a subtle shake or visual cue to the input if empty? (Optional enhancement)
                return;
            }
            if (!currentMode) {
                addMessage('bot', "Please select either 'General Health' or 'Medical Policies' first using the buttons above.");
                return;
            }

            addMessage('user', userMessage);
            chatbotInput.value = ''; // Clear input
            adjustTextareaHeight(); // Reset height after clearing
            chatbotInput.focus();   // Keep focus on input

            const typingIndicator = showTypingIndicator();

            fetch(FLASK_BACKEND_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                     'Accept': 'application/json' // Important for backend to know what client expects
                },
                body: JSON.stringify({ message: userMessage, mode: currentMode }),
            })
            .then(response => {
                // Improved error handling based on response status
                if (!response.ok) {
                    // Try to get more specific error from response body if possible
                     return response.text().then(text => {
                         let errorMsg = `HTTP error! Status: ${response.status}`;
                         try {
                             const jsonError = JSON.parse(text);
                             // Assuming backend returns error in {'error': 'message'} or {'reply': [message]}
                             errorMsg += `: ${jsonError.error || (jsonError.reply && jsonError.reply[0]) || text}`;
                         } catch (e) {
                             errorMsg += `. ${text || 'No further details available.'}`;
                         }
                         throw new Error(errorMsg);
                     });
                }
                // Check content type before parsing JSON
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                     return response.json();
                } else {
                    // Handle cases where backend might return non-JSON unexpectedly
                    return response.text().then(text => {
                        throw new Error(`Received non-JSON response: ${contentType || 'Unknown'}. Content: ${text.substring(0, 100)}...`);
                    });
                }
            })
            .then(data => {
                removeTypingIndicator();
                let botReply = data.reply; // Expecting {'reply': [...]} or {'reply': "..."}

                // Handle different response formats gracefully
                if (Array.isArray(botReply) && botReply.length > 0) {
                    // Format list items correctly
                    const formattedReply = `<ul>${botReply.map(item => `<li>${sanitizeListItem(item)}</li>`).join('')}</ul>`;
                    addMessage('bot', formattedReply, true); // Add as HTML
                } else if (typeof botReply === 'string' && botReply.trim() !== '') {
                    // Check if the string itself contains HTML tags (basic check)
                    const containsHtml = /<[a-z][\s\S]*>/i.test(botReply);
                    addMessage('bot', botReply, containsHtml); // Add as plain text or HTML
                } else {
                    // Handle empty or unexpected replies
                    console.warn("Received empty or unexpected reply format:", data);
                    addMessage('bot', "Sorry, I didn't receive a specific answer. Could you try rephrasing your question?");
                }

                // Prompt for next action only after a successful response
                promptForNextAction();

            })
            .catch(error => {
                removeTypingIndicator();
                console.error('Chatbot Fetch Error:', error);
                // **MODIFIED:** More user-friendly error message
                let displayError = "Sorry, I encountered a problem processing your request. ";
                if (error.message.includes("Failed to fetch")) {
                    displayError += "Please check your internet connection and ensure the assistant service is running.";
                } else if (error.message.includes("HTTP error")) {
                     displayError += `There was an issue communicating with the assistant (${error.message}). Please try again shortly.`;
                } else {
                    displayError += "Please try again later. If the problem persists, contact technical support.";
                }
                 addMessage('bot', displayError);
                 // Decide if you want to promptForNextAction even after an error,
                 // maybe allowing them to switch modes or try again. Let's add it for now.
                 if (currentMode) { // Only prompt if a mode was active
                    promptForNextAction();
                 }
            });
        }

        // Adjust textarea height dynamically
        function adjustTextareaHeight() {
            chatbotInput.style.height = 'auto'; // Reset height
            const maxHeight = 100; // Same as max-height in CSS
            const scrollHeight = chatbotInput.scrollHeight;
            const newHeight = Math.min(scrollHeight, maxHeight);
            chatbotInput.style.height = `${newHeight}px`;
        }
        chatbotInput.addEventListener('input', adjustTextareaHeight);

        // Simple function to prevent basic HTML injection in list items from backend
        function sanitizeListItem(item) {
            if (typeof item !== 'string') return '';
            return item.replace(/</g, "<").replace(/>/g, ">");
        }


        // Initialize on load
        initializeChat();

    </script>

</body>
</html>