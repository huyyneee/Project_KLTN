<?php
// Test autoloader on server

echo "=== TESTING AUTOLOADER ON SERVER ===\n\n";

// Test the exact same autoloader as in public/api/index.php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    
    if (strncmp($prefix, $class, $len) !== 0)
        return;
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

echo "Current working directory: " . getcwd() . "\n";
echo "Base directory exists: " . (is_dir(__DIR__ . '/app/') ? 'Yes' : 'No') . "\n";
echo "Models directory exists: " . (is_dir(__DIR__ . '/app/Models/') ? 'Yes' : 'No') . "\n";
echo "AccountModel file exists: " . (file_exists(__DIR__ . '/app/Models/AccountModel.php') ? 'Yes' : 'No') . "\n";

echo "\nTesting AccountModel...\n";
try {
    $accountModel = new \App\Models\AccountModel();
    echo "✅ AccountModel loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>
