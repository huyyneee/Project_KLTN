<?php
// Debug the exact routing logic from routes/api.php

echo "=== DEBUGGING ROUTES/API.PHP LOGIC ===\n\n";

// Simulate the exact same logic as in routes/api.php
$uri = '/api/employees';
$path = parse_url($uri, PHP_URL_PATH);

echo "Original URI: $uri\n";
echo "Parsed path: $path\n\n";

// Handle different API path formats (exact logic from routes/api.php)
if (strpos($path, '/api/') === 0) {
    $path = substr($path, 4); // Remove '/api' prefix
    echo "Removed '/api' prefix (case 1): $path\n";
} elseif (strpos($path, '/api') === 0) {
    $path = substr($path, 4); // Remove '/api' prefix
    echo "Removed '/api' prefix (case 2): $path\n";
}

// Remove trailing slash
$path = rtrim($path, '/');
echo "After removing trailing slash: '$path'\n";

// If path is empty, set to root
if (empty($path)) {
    $path = '/';
    echo "Path was empty, set to root: '$path'\n";
}

echo "\nFinal path for routing: '$path'\n\n";

// Test the switch cases
echo "=== TESTING SWITCH CASES ===\n";

switch ($path) {
    case '/employees':
        echo "✅ Matches case '/employees'\n";
        break;
    case '/':
        echo "✅ Matches case '/' (root)\n";
        break;
    default:
        echo "❌ No match in switch cases\n";
        echo "Available cases to check:\n";
        echo "- '/employees'\n";
        echo "- '/'\n";
        echo "- Other specific routes...\n";
        break;
}

echo "\n=== TESTING REGEX MATCHES ===\n";

// Test the regex pattern
if (preg_match('/^\/employees\/(\d+)$/', $path, $matches)) {
    echo "✅ Regex matches with ID: {$matches[1]}\n";
} else {
    echo "❌ Regex does not match\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
?>