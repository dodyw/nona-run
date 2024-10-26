<?php
require_once 'functions.php';

$session = getUserSession();

if (isset($_POST['action']) && $_POST['action'] === 'create_endpoint') {
    $endpoint = generateEndpoint();
    saveEndpoint($session, $endpoint);
    header('Location: /?endpoint=' . $endpoint);
    exit();
}

$endpoints = getEndpoints($session);
$currentEndpoint = $_GET['endpoint'] ?? ($endpoints[0] ?? null);

// AJAX endpoint for getting messages
if (isset($_GET['get_messages']) && $currentEndpoint) {
    header('Content-Type: application/json');
    echo json_encode(getMessages($currentEndpoint));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webhook Tester</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Webhook Tester</h1>
            <form method="post" class="create-endpoint">
                <input type="hidden" name="action" value="create_endpoint">
                <button type="submit">Create New Endpoint</button>
            </form>
        </header>

        <div class="endpoints">
            <h2>Your Endpoints</h2>
            <div class="endpoint-list">
                <?php foreach ($endpoints as $endpoint): ?>
                    <a href="/?endpoint=<?= htmlspecialchars($endpoint) ?>" 
                       class="endpoint <?= $endpoint === $currentEndpoint ? 'active' : '' ?>">
                        <?= htmlspecialchars($endpoint) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($currentEndpoint): ?>
        <div class="endpoint-details">
            <h2>Endpoint URL</h2>
            <div class="endpoint-url">
                <code id="endpointUrl"><?= htmlspecialchars($_SERVER['HTTP_HOST']) ?>/webhook.php/<?= htmlspecialchars($currentEndpoint) ?></code>
                <button onclick="copyEndpoint()">Copy</button>
            </div>
            <div class="message-count">
                Messages: <span id="messageCount"><?= getMessageCount($currentEndpoint) ?></span> / <?= MAX_MESSAGES ?>
            </div>
        </div>

        <div class="messages">
            <h2>Messages</h2>
            <div id="messageList"></div>
        </div>
        <?php endif; ?>
    </div>

    <script>
        const currentEndpoint = <?= json_encode($currentEndpoint) ?>;
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>
