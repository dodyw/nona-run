<?php
define('MAX_MESSAGES', 10000);
define('COOKIE_NAME', 'webhook_session');
define('COOKIE_LIFETIME', 60 * 60 * 24 * 365 * 10); // 10 years
define('DB_FILE', __DIR__ . '/webhook.db');

// Initialize SQLite database
try {
    $db = new SQLite3(DB_FILE);
    
    // Create endpoints table
    $db->exec('
        CREATE TABLE IF NOT EXISTS endpoints (
            id TEXT PRIMARY KEY,
            session_id TEXT NOT NULL,
            created_at INTEGER NOT NULL
        )
    ');
    
    // Create messages table
    $db->exec('
        CREATE TABLE IF NOT EXISTS messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            endpoint_id TEXT NOT NULL,
            method TEXT NOT NULL,
            headers TEXT NOT NULL,
            query TEXT NOT NULL,
            body TEXT,
            timestamp INTEGER NOT NULL,
            FOREIGN KEY (endpoint_id) REFERENCES endpoints(id)
        )
    ');
    
    // Create index for faster queries
    $db->exec('CREATE INDEX IF NOT EXISTS idx_endpoint_timestamp ON messages(endpoint_id, timestamp DESC)');
    $db->exec('CREATE INDEX IF NOT EXISTS idx_session_endpoints ON endpoints(session_id)');
    
} catch (Exception $e) {
    die('Database initialization failed: ' . $e->getMessage());
}
