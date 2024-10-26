<?php
require_once 'functions.php';

$session = getUserSession();

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_endpoint':
            $endpoint = generateEndpoint();
            saveEndpoint($session, $endpoint);
            header('Location: /?endpoint=' . $endpoint);
            exit();
            
        case 'delete_endpoint':
            if (isset($_POST['endpoint']) && deleteEndpoint($session, $_POST['endpoint'])) {
                $endpoints = getEndpoints($session);
                header('Location: /' . (!empty($endpoints) ? '?endpoint=' . $endpoints[0] : ''));
                exit();
            }
            break;
    }
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
                    <div class="endpoint-wrapper">
                        <a href="/?endpoint=<?= htmlspecialchars($endpoint) ?>" 
                           class="endpoint <?= $endpoint === $currentEndpoint ? 'active' : '' ?>">
                            <?= htmlspecialchars($endpoint) ?>
                        </a>
                        <form method="post" class="delete-endpoint" onsubmit="return confirm('Are you sure you want to delete this endpoint and all its messages?');">
                            <input type="hidden" name="action" value="delete_endpoint">
                            <input type="hidden" name="endpoint" value="<?= htmlspecialchars($endpoint) ?>">
                            <button type="submit" class="delete-btn" title="Delete Endpoint">×</button>
                        </form>
                    </div>
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
