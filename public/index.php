<?php
// Main entry point for the application
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoload PSR-4 cho namespace App\*
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
// Load global helper functions (send_mail, generate_verification_code, ...)
// so controllers and routes can call them without needing to require the file everywhere.
@require_once __DIR__ . '/../app/Helpers.php';

require_once __DIR__ . '/../app/Helpers.php';

// Get request method and URI
$method = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Remove query string and get path
$path = parse_url($uri, PHP_URL_PATH);

// Check if it's an API route first
if (strpos($path, '/api') === 0) {
    require_once __DIR__ . '/api/index.php';
} else {
    // Use existing web routing system
    require_once __DIR__ . '/../routes/web.php';
}
?>