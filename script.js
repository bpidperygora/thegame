/**
 * Class to handle WebSocket connections.
 */
class WebSocketHandler {
    /**
     * Initializes the WebSocket connection.
     * @param {string} url The WebSocket server URL.
     */
    constructor(url) {
        /** @type {WebSocket} */
        this.ws = new WebSocket(url);

        this.registerEventHandlers();
    }

    /**
     * Registers event handlers for the WebSocket.
     */
    registerEventHandlers() {
        this.ws.onopen = () => {
            console.log('WebSocket connection established');
            this.ws.send(JSON.stringify({ action: 'register', id: TAB_ID }));
        };

        this.ws.onmessage = (event) => {
            /** @type {Object<string: string>} */
            const data = JSON.parse(event.data);

            if (data.action === 'logout') {
                console.log('Logging out');
                // Ensure the logout process is securely handled
                window.location.href = '/logout.php';
            }
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        this.ws.onclose = () => {
            console.log('WebSocket connection closed');
        };
    }
}

// Usage
document.addEventListener('DOMContentLoaded', () => {
    const webSocketHandler = new WebSocketHandler('ws://localhost:8081');
});