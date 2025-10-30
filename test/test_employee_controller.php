<?php
// Test EmployeeApiController directly

echo "=== TESTING EMPLOYEE API CONTROLLER ===\n\n";

try {
    // Include required files
    require_once __DIR__ . '/app/Core/Controller.php';
    require_once __DIR__ . '/app/Models/UserModel.php';
    require_once __DIR__ . '/app/Models/AccountModel.php';
    require_once __DIR__ . '/app/Controllers/EmployeeApiController.php';

    echo "✅ All files loaded successfully\n";

    // Create instance
    $controller = new \App\Controllers\EmployeeApiController();
    echo "✅ EmployeeApiController instantiated successfully\n";

    // Test methods
    $methods = ['index', 'store', 'show', 'update', 'delete'];
    foreach ($methods as $method) {
        if (method_exists($controller, $method)) {
            echo "✅ $method() method exists\n";
        } else {
            echo "❌ $method() method not found\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>