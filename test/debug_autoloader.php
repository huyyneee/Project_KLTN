<?php
// Debug autoloader

echo "=== DEBUGGING AUTOLOADER ===\n\n";

// Test the exact same autoloader as in public/api/index.php
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/app/';
    $len = strlen($prefix);
    
    echo "Trying to load class: $class\n";
    echo "Prefix: $prefix\n";
    echo "Base dir: $base_dir\n";
    echo "Prefix length: $len\n";
    
    if (strncmp($prefix, $class, $len) !== 0) {
        echo "❌ Class doesn't start with App\\ prefix\n";
        return;
    }
    
    $relative_class = substr($class, $len);
    echo "Relative class: $relative_class\n";
    
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    echo "Looking for file: $file\n";
    
    if (file_exists($file)) {
        echo "✅ File exists, loading...\n";
        require_once $file;
    } else {
        echo "❌ File not found\n";
        echo "Current working directory: " . getcwd() . "\n";
        echo "Base directory exists: " . (is_dir($base_dir) ? 'Yes' : 'No') . "\n";
        if (is_dir($base_dir)) {
            echo "Contents of app directory:\n";
            $files = scandir($base_dir);
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    echo "  - $file\n";
                }
            }
        }
    }
    echo "\n" . str_repeat("-", 50) . "\n\n";
});

echo "Testing AccountModel...\n";
try {
    $accountModel = new \App\Models\AccountModel();
    echo "✅ AccountModel loaded successfully\n";
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
?>
