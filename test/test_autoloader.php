<?php
// Test autoloader

echo "=== TESTING AUTOLOADER ===\n\n";

// Include the same autoloader as in public/api/index.php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0)
        return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    echo "Looking for class: $class\n";
    echo "File path: $file\n";
    if (file_exists($file)) {
        echo "✅ File exists, loading...\n";
        require_once $file;
    } else {
        echo "❌ File not found\n";
    }
    echo "\n";
});

echo "Testing AccountModel...\n";
try {
    $accountModel = new \App\Models\AccountModel();
    echo "✅ AccountModel loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Error loading AccountModel: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error loading AccountModel: " . $e->getMessage() . "\n";
}

echo "\nTesting UserModel...\n";
try {
    $userModel = new \App\Models\UserModel();
    echo "✅ UserModel loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Error loading UserModel: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error loading UserModel: " . $e->getMessage() . "\n";
}

echo "\nTesting EmployeeApiController...\n";
try {
    $controller = new \App\Controllers\EmployeeApiController();
    echo "✅ EmployeeApiController loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Error loading EmployeeApiController: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error loading EmployeeApiController: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>