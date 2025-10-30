<?php
// Test API directly by simulating the request

echo "=== TESTING API DIRECTLY ===\n\n";

// Simulate the API request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/employees';

echo "Method: {$_SERVER['REQUEST_METHOD']}\n";
echo "URI: {$_SERVER['REQUEST_URI']}\n\n";

// Include the API routes
try {
    require_once __DIR__ . '/routes/api.php';
    echo "✅ API routes loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Error loading API routes: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error loading API routes: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>