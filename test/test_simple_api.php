<?php
// Test simple API endpoint

echo "=== TESTING SIMPLE API ===\n\n";

// Simulate the API request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/test';

echo "Method: {$_SERVER['REQUEST_METHOD']}\n";
echo "URI: {$_SERVER['REQUEST_URI']}\n\n";

// Test the routing logic
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

echo "Parsed path: $path\n";

// Handle different API path formats
if (strpos($path, '/api/') === 0) {
    $path = substr($path, 4); // Remove '/api' prefix
    echo "After removing /api prefix: $path\n";
} elseif (strpos($path, '/api') === 0) {
    $path = substr($path, 4); // Remove '/api' prefix
    echo "After removing /api prefix (case 2): $path\n";
}

// Remove trailing slash
$path = rtrim($path, '/');
echo "After removing trailing slash: '$path'\n";

// If path is empty, set to root
if (empty($path)) {
    $path = '/';
    echo "Path was empty, set to root: '$path'\n";
}

echo "Final path for routing: '$path'\n\n";

// Test switch case
switch ($path) {
    case '/test':
        echo "✅ Matches /test route\n";
        echo "Response: {\"success\": true, \"message\": \"Test API working\"}\n";
        break;
    case '/employees':
        echo "✅ Matches /employees route\n";
        break;
    default:
        echo "❌ No match found for '$path'\n";
        break;
}

echo "\n=== TEST COMPLETED ===\n";
?>