<?php
// Test EmployeeController directly

echo "=== TESTING EMPLOYEE CONTROLLER ===\n\n";

try {
    // Include the controller
    require_once __DIR__ . '/app/Controllers/EmployeeController.php';
    echo "✅ EmployeeController loaded successfully\n";

    // Create instance
    $controller = new \App\Controllers\EmployeeController();
    echo "✅ EmployeeController instantiated successfully\n";

    // Test if methods exist
    if (method_exists($controller, 'index')) {
        echo "✅ index() method exists\n";
    } else {
        echo "❌ index() method not found\n";
    }

    if (method_exists($controller, 'store')) {
        echo "✅ store() method exists\n";
    } else {
        echo "❌ store() method not found\n";
    }

    if (method_exists($controller, 'show')) {
        echo "✅ show() method exists\n";
    } else {
        echo "❌ show() method not found\n";
    }

    if (method_exists($controller, 'update')) {
        echo "✅ update() method exists\n";
    } else {
        echo "❌ update() method not found\n";
    }

    if (method_exists($controller, 'delete')) {
        echo "✅ delete() method exists\n";
    } else {
        echo "❌ delete() method not found\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>