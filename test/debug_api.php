<?php
// Debug script to check API routing
echo "=== API DEBUG INFORMATION ===\n\n";

// Simulate the same environment as the API
$method = 'GET';
$uri = '/api/employees';
$path = parse_url($uri, PHP_URL_PATH);

echo "Method: $method\n";
echo "URI: $uri\n";
echo "Path: $path\n\n";

// Check if path starts with /api
if (strpos($path, '/api') === 0) {
    echo "✅ Path starts with /api\n";

    // Remove /api prefix
    $apiPath = substr($path, 4);
    echo "API Path after removing /api: '$apiPath'\n";

    // Check if it matches '/employees'
    if ($apiPath === '/employees') {
        echo "✅ Matches /employees route\n";
    } else {
        echo "❌ Does not match /employees route\n";
        echo "Expected: '/employees'\n";
        echo "Got: '$apiPath'\n";
    }
} else {
    echo "❌ Path does not start with /api\n";
}

echo "\n=== Testing route matching ===\n";

// Test the exact switch case logic
switch ($path) {
    case '/api/employees':
        echo "✅ Direct match for /api/employees\n";
        break;
    case '/employees':
        echo "✅ Match for /employees (after /api removal)\n";
        break;
    default:
        echo "❌ No match found\n";
        echo "Available routes to test:\n";
        echo "- /api/employees\n";
        echo "- /employees\n";
        break;
}

echo "\n=== Testing regex patterns ===\n";

// Test regex for /employees/{id}
$testPaths = ['/employees/1', '/employees/123', '/employees/abc'];
foreach ($testPaths as $testPath) {
    if (preg_match('/^\/employees\/(\d+)$/', $testPath, $matches)) {
        echo "✅ Regex matches '$testPath' - ID: {$matches[1]}\n";
    } else {
        echo "❌ Regex does not match '$testPath'\n";
    }
}

echo "\n=== DEBUG COMPLETED ===\n";
?>