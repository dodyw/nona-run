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
    <title>nona.run | Free Webhook Testing Tool for Developers</title>
    <meta name="description" content="nona.run is a free webhook testing tool that helps developers test, debug and monitor HTTP webhooks and API callbacks. Simple, fast, and reliable webhook testing platform.">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://nona.run/">
    <meta property="og:title" content="nona.run - Webhook Testing Tool for Developers">
    <meta property="og:description" content="Test and debug your webhooks easily with nona.run. Real-time webhook testing, request inspection, and API callback monitoring for developers.">
    <meta property="og:image" content="https://nona.run/og-image.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://nona.run/">
    <meta property="twitter:title" content="nona.run - Webhook Testing Made Simple">
    <meta property="twitter:description" content="Test and debug your webhooks easily with nona.run. Real-time webhook testing, request inspection, and API callback monitoring for developers.">
    <meta property="twitter:image" content="https://nona.run/twitter-image.jpg">

    <!-- Additional SEO Meta Tags -->
    <meta name="keywords" content="webhook tester, webhook testing tool, webhook debugger, HTTP webhook testing, API callback testing, webhook monitor, developer tools, webhook debugging, API testing">
    <meta name="author" content="nona.run">
    <meta name="robots" content="index, follow">
    <meta name="canonical" content="https://nona.run/">

    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "nona.run",
        "applicationCategory": "DeveloperApplication",
        "operatingSystem": "Web",
        "url": "https://nona.run",
        "description": "A free webhook testing tool for developers to test and debug HTTP webhooks and API callbacks",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "USD"
        },
        "features": [
            "Real-time webhook testing",
            "Request inspection",
            "API callback monitoring",
            "Developer-friendly interface"
        ]
    }
    </script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Nona.run</h1>
            <form method="post" class="create-endpoint">
                <input type="hidden" name="action" value="create_endpoint">
                <button type="submit">Create New Endpoint</button>
            </form>
        </header>

        <h2>A free webhook testing tool for developers to test and debug HTTP webhooks and API callbacks</h2>

        <div class="endpoints" style="margin-top:40px">
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

    <footer style="text-align: center; margin-top: 20px;">
        <p>nona.run is developed by <a href="https://www.nicecoder.com" target="_blank">nicecoder.com</a> &copy; <?php echo date("Y"); ?></p>
    </footer>

    <script>
        const currentEndpoint = <?= json_encode($currentEndpoint) ?>;
    </script>
    <script src="assets/js/script.js"></script>
</body>
</html>
