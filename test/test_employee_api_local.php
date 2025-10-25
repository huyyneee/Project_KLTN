<?php
// Test Employee API locally

echo "=== TESTING EMPLOYEE API LOCALLY ===\n\n";

// Include autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0)
        return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file))
        require_once $file;
});

// Load helpers
@require_once __DIR__ . '/../app/Helpers.php';

// Simulate API request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/employees';

echo "Method: {$_SERVER['REQUEST_METHOD']}\n";
echo "URI: {$_SERVER['REQUEST_URI']}\n\n";

try {
    // Test EmployeeApiController directly
    $controller = new \App\Controllers\EmployeeApiController();
    echo "✅ EmployeeApiController created successfully\n";

    // Test index method
    echo "Testing index method...\n";
    $controller->index();

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>