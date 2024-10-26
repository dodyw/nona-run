function copyEndpoint() {
    const endpointUrl = document.getElementById('endpointUrl').textContent;
    navigator.clipboard.writeText(endpointUrl);
}

function formatTimestamp(timestamp) {
    return new Date(timestamp * 1000).toLocaleString();
}

function toggleMessage(element) {
    const content = element.querySelector('.message-content');
    content.classList.toggle('active');
}

function formatHeaders(headers) {
    let formatted = '';
    for (const [key, value] of Object.entries(headers)) {
        formatted += `${key}: ${value}\n`;
    }
    return formatted;
}

function createMessageElement(message) {
    const div = document.createElement('div');
    div.className = 'message';
    div.onclick = () => toggleMessage(div);

    div.innerHTML = `
        <div class="message-header">
            <span class="method ${message.method}">${message.method}</span>
            <span class="timestamp">${formatTimestamp(message.timestamp)}</span>
        </div>
        <div class="message-content">
            <h3>Headers</h3>
            <pre>${formatHeaders(message.headers)}</pre>
            
            <h3>Query Parameters</h3>
            <pre>${JSON.stringify(message.query, null, 2)}</pre>
            
            <h3>Body</h3>
            <pre>${message.body || '(empty)'}</pre>
        </div>
    `;

    return div;
}

let lastMessageCount = 0;

function updateMessages() {
    if (!currentEndpoint) return;

    fetch(`/?get_messages=1&endpoint=${currentEndpoint}`)
        .then(response => response.json())
        .then(messages => {
            const messageList = document.getElementById('messageList');
            const messageCount = document.getElementById('messageCount');
            
            // Update message count
            if (messageCount) {
                messageCount.textContent = messages.length;
            }

            // Only update if we have new messages
            if (messages.length > lastMessageCount) {
                // Clear existing messages
                messageList.innerHTML = '';
                
                // Add all messages
                messages.forEach(message => {
                    const messageElement = createMessageElement(message);
                    messageList.appendChild(messageElement);
                });
                
                lastMessageCount = messages.length;
            }
        })
        .catch(error => console.error('Error fetching messages:', error));
}

// Update messages every second
if (currentEndpoint) {
    updateMessages();
    setInterval(updateMessages, 1000);
}
