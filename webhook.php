<?php
require_once 'functions.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$endpoint = basename($_SERVER['REQUEST_URI']);

// If endpoint doesn't look like a valid hex string, return 404
if (!preg_match('/^[a-f0-9]{32}$/', $endpoint)) {
    http_response_code(404);
    exit();
}

// Check if endpoint exists in database
$db = getDB();
$stmt = $db->prepare('SELECT 1 FROM endpoints WHERE id = ?');
$stmt->bindValue(1, $endpoint, SQLITE3_TEXT);
$result = $stmt->execute();
if (!$result->fetchArray()) {
    http_response_code(404);
    exit();
}

// Check message count
if (getMessageCount($endpoint) >= MAX_MESSAGES) {
    http_response_code(429); // Too Many Requests
    echo json_encode(['error' => 'Maximum message limit reached for this endpoint']);
    exit();
}

// Capture request details
$message = [
    'method' => $_SERVER['REQUEST_METHOD'],
    'headers' => getallheaders(),
    'query' => $_GET,
    'body' => file_get_contents('php://input'),
    'timestamp' => time(),
];

saveMessage($endpoint, $message);

// Return success response
http_response_code(200);
echo json_encode(['status' => 'success']);
