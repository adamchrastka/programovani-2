<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$dataDir = __DIR__ . '/data';
$chatsFile = $dataDir . '/chats.txt';
$messagesDir = $dataDir . '/messages';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

if (!is_dir($messagesDir)) {
    mkdir($messagesDir, 0755, true);
}

if (!file_exists($chatsFile)) {
    file_put_contents($chatsFile, json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

$action = $_GET['action'] ?? $_POST['action'] ?? null;

switch ($action) {
    case 'getChats':
        handleGetChats();
        break;

    case 'getMessages':
        handleGetMessages();
        break;

    case 'sendMessage':
        handleSendMessage();
        break;

    case 'createChat':
        handleCreateChat();
        break;

    case 'deleteChat':
        handleDeleteChat();
        break;

    default:
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
}

function handleGetChats() {
    global $chatsFile;

    $chats = readJsonFile($chatsFile);
    usort($chats, function ($a, $b) {
        return ($b['timestamp'] ?? 0) <=> ($a['timestamp'] ?? 0);
    });

    echo json_encode([
        'success' => true,
        'data' => $chats
    ]);
}

function handleGetMessages() {
    $chatId = isset($_GET['chatId']) ? intval($_GET['chatId']) : 0;
    if ($chatId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing chatId']);
        return;
    }

    $messagePath = getMessagePath($chatId);
    $messages = readJsonFile($messagePath);

    usort($messages, function ($a, $b) {
        return ($a['timestamp'] ?? 0) <=> ($b['timestamp'] ?? 0);
    });

    echo json_encode([
        'success' => true,
        'data' => $messages
    ]);
}

function handleSendMessage() {
    $chatId = isset($_POST['chatId']) ? intval($_POST['chatId']) : 0;
    $sender = trim($_POST['sender'] ?? 'User');
    $text = trim($_POST['text'] ?? '');

    if ($chatId <= 0 || $text === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing required fields']);
        return;
    }

    $messagePath = getMessagePath($chatId);
    $messages = readJsonFile($messagePath);
    $nextId = empty($messages) ? 1 : max(array_column($messages, 'id')) + 1;
    $timestamp = time();

    $newMessage = [
        'id' => $nextId,
        'chatId' => $chatId,
        'sender' => $sender,
        'text' => $text,
        'time' => date('H:i', $timestamp),
        'timestamp' => $timestamp
    ];

    $messages[] = $newMessage;

    if (file_put_contents($messagePath, json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to save message']);
        return;
    }

    updateChatLastMessage($chatId, $text);

    echo json_encode([
        'success' => true,
        'data' => $newMessage
    ]);
}

function handleCreateChat() {
    global $chatsFile;

    $name = trim($_POST['name'] ?? '');
    $avatar = trim($_POST['avatar'] ?? 'U');

    if ($name === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Name is required']);
        return;
    }

    $chats = readJsonFile($chatsFile);
    $newId = empty($chats) ? 1 : max(array_column($chats, 'id')) + 1;
    $timestamp = time();

    $newChat = [
        'id' => $newId,
        'name' => $name,
        'avatar' => strtoupper(substr($avatar, 0, 2)),
        'lastMessage' => 'Konverzace byla právě vytvořena',
        'lastTime' => date('H:i', $timestamp),
        'timestamp' => $timestamp
    ];

    $chats[] = $newChat;

    if (file_put_contents($chatsFile, json_encode($chats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)) === false) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to create chat']);
        return;
    }

    echo json_encode([
        'success' => true,
        'data' => $newChat
    ]);
}

function handleDeleteChat() {
    global $chatsFile;

    $chatId = isset($_POST['chatId']) ? intval($_POST['chatId']) : 0;
    if ($chatId <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Missing chatId']);
        return;
    }

    $chats = readJsonFile($chatsFile);
    $filteredChats = array_values(array_filter($chats, function ($chat) use ($chatId) {
        return intval($chat['id'] ?? 0) !== $chatId;
    }));

    file_put_contents($chatsFile, json_encode($filteredChats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

    $messagePath = getMessagePath($chatId);
    if (file_exists($messagePath)) {
        unlink($messagePath);
    }

    echo json_encode(['success' => true]);
}

function updateChatLastMessage($chatId, $lastMessage) {
    global $chatsFile;

    $chats = readJsonFile($chatsFile);
    $timestamp = time();

    foreach ($chats as &$chat) {
        if (intval($chat['id'] ?? 0) === intval($chatId)) {
            $chat['lastMessage'] = $lastMessage;
            $chat['lastTime'] = date('H:i', $timestamp);
            $chat['timestamp'] = $timestamp;
            break;
        }
    }

    file_put_contents($chatsFile, json_encode($chats, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function readJsonFile($filePath) {
    if (!file_exists($filePath)) {
        return [];
    }

    $content = file_get_contents($filePath);
    if ($content === false || trim($content) === '') {
        return [];
    }

    $decoded = json_decode($content, true);
    return is_array($decoded) ? $decoded : [];
}

function getMessagePath($chatId) {
    global $messagesDir;

    return $messagesDir . '/chat_' . intval($chatId) . '.txt';
}
