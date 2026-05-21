let currentChatId = null;
let currentUserAvatar = 'MT';
const PHP_FILE = 'php.php';

document.addEventListener('DOMContentLoaded', () => {
    loadChats();
    attachEventListeners();
});

function attachEventListeners() {
    const sendBtn = document.getElementById('sendBtn');
    const messageInput = document.getElementById('messageInput');
    const newChatBtn = document.getElementById('newChatBtn');
    const searchChats = document.getElementById('searchChats');
    const attachBtn = document.getElementById('attachBtn');
    const fileInput = document.getElementById('fileInput');
    const emojiBtn = document.getElementById('emojiBtn');

    sendBtn.addEventListener('click', sendMessage);
    messageInput.addEventListener('keydown', handleMessageKeydown);
    messageInput.addEventListener('input', autoResizeMessageInput);
    newChatBtn.addEventListener('click', createNewChat);
    searchChats.addEventListener('input', filterChats);
    attachBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', handleFileUpload);
    emojiBtn.addEventListener('click', () => messageInput.focus());
}

function handleMessageKeydown(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

function autoResizeMessageInput() {
    const input = document.getElementById('messageInput');
    input.style.height = 'auto';
    input.style.height = `${Math.min(input.scrollHeight, 100)}px`;
}

function loadChats(selectedChatId = currentChatId) {
    fetch(`${PHP_FILE}?action=getChats`)
        .then(parseJsonResponse)
        .then((data) => {
            if (!data.success) {
                showError('Nepodařilo se načíst konverzace');
                return;
            }

            const chats = Array.isArray(data.data) ? data.data : [];
            renderChatList(chats);

            if (chats.length === 0) {
                currentChatId = null;
                updateEmptyState(
                    'Vítejte!',
                    'Klikněte + pro novou konverzaci',
                    'Zatím žádné konverzace'
                );
                return;
            }

            const chatToSelect = chats.find((chat) => chat.id === selectedChatId) ?? chats[0];
            selectChat(chatToSelect.id, chatToSelect.name);
        })
        .catch((error) => {
            console.error(error);
            showError('Nepodařilo se načíst konverzace');
        });
}

function renderChatList(chats) {
    const chatList = document.getElementById('chatList');
    chatList.innerHTML = '';

    chats.forEach((chat) => {
        const item = document.createElement('div');
        item.className = `chat-item${chat.id === currentChatId ? ' active' : ''}`;
        item.dataset.chatId = chat.id;
        item.innerHTML = `
            <div class="chat-avatar">${escapeHtml(chat.avatar || '?')}</div>
            <div class="chat-info">
                <div class="chat-name">${escapeHtml(chat.name || 'Bez názvu')}</div>
                <div class="chat-preview">${escapeHtml(chat.lastMessage || '')}</div>
            </div>
            <div class="chat-time">${escapeHtml(chat.lastTime || '')}</div>
        `;

        item.addEventListener('click', () => selectChat(chat.id, chat.name));
        item.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            showChatContextMenu(event, chat.id);
        });

        chatList.appendChild(item);
    });
}

function selectChat(chatId, chatName) {
    currentChatId = chatId;
    document.querySelectorAll('.chat-item').forEach((item) => {
        item.classList.toggle('active', Number(item.dataset.chatId) === Number(chatId));
    });
    document.getElementById('chatHeaderName').textContent = chatName;
    document.getElementById('chatHeaderStatus').textContent = 'Online';
    loadMessages(chatId);
}

function loadMessages(chatId) {
    fetch(`${PHP_FILE}?action=getMessages&chatId=${encodeURIComponent(chatId)}`)
        .then(parseJsonResponse)
        .then((data) => {
            if (!data.success) {
                showError('Nepodařilo se načíst zprávy');
                return;
            }

            renderMessages(Array.isArray(data.data) ? data.data : []);
        })
        .catch((error) => {
            console.error(error);
            showError('Nepodařilo se načíst zprávy');
        });
}

function renderMessages(messages) {
    const container = document.getElementById('messagesContainer');
    container.innerHTML = '';

    if (messages.length === 0) {
        container.innerHTML = '<div style="text-align:center; color:#999; margin-top:40px;">Zatím žádné zprávy</div>';
        return;
    }

    messages.forEach((message) => {
        const isOwn = message.sender === currentUserAvatar;
        const group = document.createElement('div');
        group.className = 'message-group';
        group.innerHTML = `
            <div class="message ${isOwn ? 'sent' : 'received'}">
                <div class="message-avatar">${escapeHtml(message.sender || '?')}</div>
                <div class="message-content">
                    <div class="message-text">${escapeHtml(message.text || '')}</div>
                    <div class="message-time">${escapeHtml(message.time || '')}</div>
                </div>
            </div>
        `;
        container.appendChild(group);
    });

    container.scrollTop = container.scrollHeight;
}

function sendMessage() {
    if (!currentChatId) {
        alert('Vyberte nejdříve konverzaci');
        return;
    }

    const input = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const text = input.value.trim();

    if (!text) {
        return;
    }

    sendBtn.disabled = true;

    const formData = new FormData();
    formData.append('action', 'sendMessage');
    formData.append('chatId', currentChatId);
    formData.append('sender', currentUserAvatar);
    formData.append('text', text);

    fetch(PHP_FILE, {
        method: 'POST',
        body: formData
    })
        .then(parseJsonResponse)
        .then((data) => {
            if (!data.success) {
                showError('Chyba při odesílání zprávy');
                return;
            }

            input.value = '';
            input.style.height = 'auto';
            loadMessages(currentChatId);
            loadChats(currentChatId);
        })
        .catch((error) => {
            console.error(error);
            showError('Nepodařilo se odeslat zprávu');
        })
        .finally(() => {
            sendBtn.disabled = false;
        });
}

function createNewChat() {
    const name = prompt('Zadejte jméno nové konverzace:');
    if (!name || !name.trim()) {
        return;
    }

    const trimmedName = name.trim();
    const formData = new FormData();
    formData.append('action', 'createChat');
    formData.append('name', trimmedName);
    formData.append('avatar', trimmedName.substring(0, 2).toUpperCase());

    fetch(PHP_FILE, {
        method: 'POST',
        body: formData
    })
        .then(parseJsonResponse)
        .then((data) => {
            if (!data.success) {
                showError('Chyba při vytvoření konverzace');
                return;
            }

            loadChats(data.data.id);
        })
        .catch((error) => {
            console.error(error);
            showError('Nepodařilo se vytvořit konverzaci');
        });
}

function deleteChat(chatId) {
    if (!confirm('Opravdu chcete odstranit tuto konverzaci?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', 'deleteChat');
    formData.append('chatId', chatId);

    fetch(PHP_FILE, {
        method: 'POST',
        body: formData
    })
        .then(parseJsonResponse)
        .then((data) => {
            if (!data.success) {
                showError('Chyba při mazání konverzace');
                return;
            }

            if (Number(currentChatId) === Number(chatId)) {
                currentChatId = null;
            }
            loadChats();
        })
        .catch((error) => {
            console.error(error);
            showError('Nepodařilo se odstranit konverzaci');
        });
}

function filterChats(event) {
    const searchValue = event.target.value.toLowerCase();
    document.querySelectorAll('.chat-item').forEach((item) => {
        const name = item.querySelector('.chat-name')?.textContent.toLowerCase() || '';
        const preview = item.querySelector('.chat-preview')?.textContent.toLowerCase() || '';
        item.style.display = name.includes(searchValue) || preview.includes(searchValue) ? '' : 'none';
    });
}

function handleFileUpload(event) {
    if (!currentChatId) {
        showError('Vyberte nejdříve konverzaci');
        event.target.value = '';
        return;
    }

    const files = Array.from(event.target.files || []);
    if (files.length === 0) {
        return;
    }

    Promise.all(files.map((file) => sendFileMessage(file.name)))
        .then(() => {
            loadMessages(currentChatId);
            loadChats(currentChatId);
        })
        .catch((error) => {
            console.error(error);
            showError('Nepodařilo se odeslat přílohu');
        })
        .finally(() => {
            event.target.value = '';
        });
}

function sendFileMessage(fileName) {
    const formData = new FormData();
    formData.append('action', 'sendMessage');
    formData.append('chatId', currentChatId);
    formData.append('sender', currentUserAvatar);
    formData.append('text', `[Příloha] ${fileName}`);

    return fetch(PHP_FILE, {
        method: 'POST',
        body: formData
    }).then(parseJsonResponse);
}

function showChatContextMenu(event, chatId) {
    document.getElementById('chatContextMenu')?.remove();

    const menu = document.createElement('div');
    menu.id = 'chatContextMenu';
    menu.style.cssText = `
        position: fixed;
        top: ${event.clientY}px;
        left: ${event.clientX}px;
        background: white;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        z-index: 1000;
        padding: 8px 0;
        min-width: 150px;
    `;

    const deleteOption = document.createElement('button');
    deleteOption.type = 'button';
    deleteOption.style.cssText = 'display:block; width:100%; padding:10px 16px; cursor:pointer; color:#d32f2f; font-size:14px; background:none; border:none; text-align:left;';
    deleteOption.textContent = 'Odstranit konverzaci';
    deleteOption.addEventListener('click', () => {
        deleteChat(chatId);
        menu.remove();
    });

    menu.appendChild(deleteOption);
    document.body.appendChild(menu);

    setTimeout(() => {
        document.addEventListener('click', function removeMenu(clickEvent) {
            if (!menu.contains(clickEvent.target)) {
                menu.remove();
                document.removeEventListener('click', removeMenu);
            }
        });
    }, 0);
}

function updateEmptyState(title, status, message) {
    document.getElementById('chatHeaderName').textContent = title;
    document.getElementById('chatHeaderStatus').textContent = status;
    document.getElementById('messagesContainer').innerHTML = `<div style="text-align:center; color:#999; margin-top:40px;">${escapeHtml(message)}</div>`;
}

function showError(message) {
    alert(message);
}

function parseJsonResponse(response) {
    if (!response.ok) {
        throw new Error(`HTTP ${response.status}`);
    }

    return response.json();
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text ?? '';
    return div.innerHTML;
}
