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
            <h1>nona.run</h1>
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
