const buttons = ['start', 'restart', 'stop', 'kill'];
const statusIndicator = document.getElementById('statusIndicator');
const statusText = document.getElementById('statusText');
const consoleOutput = document.getElementById('console-output');
const input = document.getElementById('commandInput');
const cpuUsageText = document.querySelector('.cpu-usage-info .cpu-usage');
const cpuUsageBar = document.querySelector('.cpu-usage-info .cpu-usage-bar');
const memoryUsageText = document.querySelector('.memory-usage-info .memory-usage');
const memoryUsageBar = document.querySelector('.memory-usage-info .memory-usage-bar');
const diskUsageText = document.querySelector('.disk-usage-info .disk-usage');
const diskUsageBar = document.querySelector('.disk-usage-info .disk-usage-bar');
const totalMemory = document.getElementById('totalMemory').value;
const totalDisk = document.getElementById('totalDisk').value;
const totalCPU = document.getElementById('totalCPU').value;
const orderId = document.getElementById('orderId').value;
const socketUrl = document.getElementById('socketUrl').value;
const ansi_up = new AnsiUp();

const translations = {
    starting: document.getElementById('translate-starting').textContent,
    stopping: document.getElementById('translate-stopping').textContent,
    running: document.getElementById('translate-running').textContent,
    offline: document.getElementById('translate-offline').textContent,
    suspended: document.getElementById('translate-suspended').textContent,
    installing: document.getElementById('translate-installing').textContent,
    updating: document.getElementById('translate-updating').textContent,
};


document.addEventListener('DOMContentLoaded', function () {
    let socket = null;
    let commandHistory = getCommandHistory();
    let historyIndex = commandHistory.length;

    function fetchNewToken() {
        fetch(socketUrl, {
            headers: {'Accept': 'application/json', 'Content-Type': 'application/json'},
        }).then(response => response.json()).then(data => authenticate(data))
            .catch(error => console.error('Error fetching new websocket token:', error));
    }

    function authenticate(data) {
        if (socket) {
            socket.send(JSON.stringify({'event': 'auth', 'args': [data.token]}));
        }
    }

    function addConsoleOutput(text) {
        if (text.includes('[Pterodactyl Daemon]:')) {
            consoleOutput.innerHTML = '';
            return;
        }
        const newLine = document.createElement('p');
        newLine.innerHTML = ansi_up.ansi_to_html(consoleReplace(text));
        consoleOutput.appendChild(newLine);
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
    }

    function consoleReplace(text) {
        return text
            .replace('=>....', '')
            .replace('>....', '')
            ;
    }

    function handleInput(event) {
        if (event.key === 'Enter' && input.value) {
            sendCommand(input.value.replace('/', ''));
            input.value = '';
        } else if (event.key === 'ArrowUp') {
            historyIndex = Math.max(historyIndex - 1, 0);
            input.value = commandHistory[historyIndex] || '';
            event.preventDefault();
        } else if (event.key === 'ArrowDown') {
            historyIndex = Math.min(historyIndex + 1, commandHistory.length);
            input.value = commandHistory[historyIndex] || '';
            event.preventDefault();
        }
    }

    function sendCommand(command) {
        if (socket) {
            socket.send(JSON.stringify({'event': 'send command', 'args': [command]}));
        }
        saveCommandToHistory(command);
        const index = commandHistory.indexOf(command);
        if (index > -1) {
            commandHistory.splice(index, 1);
        }
        commandHistory.push(command);
        historyIndex = commandHistory.length;
    }

    function setServerState(action) {
        if (socket) {
            socket.send(JSON.stringify({'event': 'set state', 'args': [action]}));
        }
    }

    function initializeWebSocket() {
        fetch(socketUrl, {headers: {'Accept': 'application/json', 'Content-Type': 'application/json'}})
            .then(response => response.json()).then(data => {
            socket = new WebSocket(data.socket);
            socket.onopen = () => authenticate(data);
            socket.onmessage = handleWebSocketMessage;
            socket.onerror = error => console.error('WebSocket Error: ', error);
        }).catch(error => console.error('Error fetching websocket data:', error));
    }

    function handleWebSocketMessage(event) {
        const message = JSON.parse(event.data);
        switch (message.event) {
            case 'console output':
                addConsoleOutput(message.args[0]);
                break;
            case 'status':
                updateButtonStates(message.args[0]);
                break;
            case 'stats':
                updateServerStatusAndResources(message.args[0]);
                break;
            case 'token expiring':
                fetchNewToken();
                break;
            case 'auth success':
                socket.send(JSON.stringify({'event': 'send logs', 'args': [null]}));
                break;
        }
    }

    function updateButtonStates(status) {
        const isStartDisabled = status === 'running' || status === 'starting';
        const isOtherDisabled = status === 'offline' || status === 'stopping';

        buttons.forEach(buttonId => {
            const button = document.getElementById(buttonId);
            if (button.id === 'kill') {
                return
            }
            if (button.id === 'start') {
                button.disabled = isStartDisabled;
            } else {
                button.disabled = isOtherDisabled;
            }
        });
    }


    buttons.forEach(buttonId => {
        const button = document.getElementById(buttonId);
        if (button) {
            button.addEventListener('click', function () {
                setServerState(buttonId);
            });
        }
    });

    input.addEventListener('keydown', handleInput);
    initializeWebSocket();
});

function saveCommandToHistory(command) {
    let history = getCommandHistory();
    const index = history.indexOf(command);
    if (index > -1) {
        history.splice(index, 1);
    }
    history.push(command);
    localStorage.setItem(`commandHistory_${orderId}`, JSON.stringify(history));
}

function getCommandHistory() {
    const historyKey = `commandHistory_${orderId}`;
    let history = localStorage.getItem(historyKey);
    return history ? JSON.parse(history) : [];
}

function updateServerStatusAndResources(data) {
    let colorClass = '';
    let translationsStatus = statusText.textContent;
    data = JSON.parse(data);
    switch (data.state) {
        case 'offline':
            colorClass = 'bg-red-600';
            translationsStatus = translations.offline;
            break;
        case 'running':
            colorClass = 'bg-emerald-600';
            translationsStatus = translations.running;
            break;
        case 'starting':
            colorClass = 'bg-orange-600';
            translationsStatus = translations.starting;
            break;
        case 'stopping':
            colorClass = 'bg-yellow-600';
            translationsStatus = translations.stopping;
            break;
        case 'installing':
            colorClass = 'bg-green-600';
            translationsStatus = translations.installing;
            break;
        case 'suspended':
            colorClass = 'bg-gray-600';
            translationsStatus = translations.suspended;
            break;
        case 'updating':
            colorClass = 'bg-purple-600';
            translationsStatus = translations.updating;
            break;
        default:
            colorClass = 'bg-gray-600';
    }

    statusIndicator.className = `flex w-4 h-4 ${colorClass} rounded-full mr-1.5 flex-shrink-0`;
    statusText.textContent = translationsStatus;

    // Update resource display
    const cpuPercent = data.cpu_absolute.toFixed(2);
    const memoryUsed = (data.memory_bytes / 1024 / 1024).toFixed(2);
    const diskUsed = (data.disk_bytes / 1024 / 1024).toFixed(2);

    const usagePercent = (cpuPercent / totalCPU * 100).toFixed(2);
    const displayPercent = Math.min(usagePercent, 100);

    cpuUsageText.textContent = totalCPU === '0' ? `${cpuPercent}% / ∞` : `${cpuPercent}% / ${totalCPU}%`;
    cpuUsageBar.style.width = `${displayPercent}%`;

    memoryUsageText.textContent = totalMemory === '0' ? `${memoryUsed} MB / ∞` : `${memoryUsed} MB / ${totalMemory} MB`;
    memoryUsageBar.style.width = `${Math.min((memoryUsed / totalMemory * 100).toFixed(2), 100)}%`;

    diskUsageText.textContent = totalDisk === '0' ? `${diskUsed} MB / ∞` : `${diskUsed} MB / ${totalDisk} MB`;
    diskUsageBar.style.width = `${Math.min((diskUsed / 1000 * 100), 100)}%`;
}
