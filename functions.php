<?php
require_once 'config.php';

function getDB() {
    static $db = null;
    if ($db === null) {
        $db = new SQLite3(DB_FILE);
        $db->enableExceptions(true);
    }
    return $db;
}

function generateEndpoint() {
    return bin2hex(random_bytes(16));
}

function getUserSession() {
    if (!isset($_COOKIE[COOKIE_NAME])) {
        $session = bin2hex(random_bytes(32));
        setcookie(COOKIE_NAME, $session, time() + COOKIE_LIFETIME, '/');
        return $session;
    }
    return $_COOKIE[COOKIE_NAME];
}

function getEndpoints($session) {
    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM endpoints WHERE session_id = ? ORDER BY created_at DESC');
    $stmt->bindValue(1, $session, SQLITE3_TEXT);
    $result = $stmt->execute();
    
    $endpoints = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $endpoints[] = $row['id'];
    }
    return $endpoints;
}

function saveEndpoint($session, $endpoint) {
    $db = getDB();
    $stmt = $db->prepare('INSERT OR IGNORE INTO endpoints (id, session_id, created_at) VALUES (?, ?, ?)');
    $stmt->bindValue(1, $endpoint, SQLITE3_TEXT);
    $stmt->bindValue(2, $session, SQLITE3_TEXT);
    $stmt->bindValue(3, time(), SQLITE3_INTEGER);
    $stmt->execute();
}

function getMessages($endpoint) {
    $db = getDB();
    
    // First check if we need to delete old messages
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM messages WHERE endpoint_id = ?');
    $stmt->bindValue(1, $endpoint, SQLITE3_TEXT);
    $result = $stmt->execute();
    $count = $result->fetchArray(SQLITE3_ASSOC)['count'];
    
    if ($count > MAX_MESSAGES) {
        // Delete excess messages
        $stmt = $db->prepare('
            DELETE FROM messages 
            WHERE id IN (
                SELECT id FROM messages 
                WHERE endpoint_id = ? 
                ORDER BY timestamp DESC 
                LIMIT -1 OFFSET ?
            )
        ');
        $stmt->bindValue(1, $endpoint, SQLITE3_TEXT);
        $stmt->bindValue(2, MAX_MESSAGES, SQLITE3_INTEGER);
        $stmt->execute();
    }
    
    // Get messages
    $stmt = $db->prepare('
        SELECT method, headers, query, body, timestamp 
        FROM messages 
        WHERE endpoint_id = ? 
        ORDER BY timestamp DESC 
        LIMIT ?
    ');
    $stmt->bindValue(1, $endpoint, SQLITE3_TEXT);
    $stmt->bindValue(2, MAX_MESSAGES, SQLITE3_INTEGER);
    $result = $stmt->execute();
    
    $messages = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $row['headers'] = json_decode($row['headers'], true);
        $row['query'] = json_decode($row['query'], true);
        $messages[] = $row;
    }
    return $messages;
}

function saveMessage($endpoint, $message) {
    $db = getDB();
    $stmt = $db->prepare('
        INSERT INTO messages (endpoint_id, method, headers, query, body, timestamp)
        VALUES (?, ?, ?, ?, ?, ?)
    ');
    
    $stmt->bindValue(1, $endpoint, SQLITE3_TEXT);
    $stmt->bindValue(2, $message['method'], SQLITE3_TEXT);
    $stmt->bindValue(3, json_encode($message['headers']), SQLITE3_TEXT);
    $stmt->bindValue(4, json_encode($message['query']), SQLITE3_TEXT);
    $stmt->bindValue(5, $message['body'], SQLITE3_TEXT);
    $stmt->bindValue(6, $message['timestamp'], SQLITE3_INTEGER);
    
    $stmt->execute();
}

function getMessageCount($endpoint) {
    $db = getDB();
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM messages WHERE endpoint_id = ?');
    $stmt->bindValue(1, $endpoint, SQLITE3_TEXT);
    $result = $stmt->execute();
    return $result->fetchArray(SQLITE3_ASSOC)['count'];
}
