<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Autoload PSR-4 cho namespace App\*
spl_autoload_register(function ($class) {
	$prefix = 'App\\';
	$base_dir = __DIR__ . '/../app/';
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) return;
	$relative_class = substr($class, $len);
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
	if (file_exists($file)) require_once $file;
});
// Load global helper functions (send_mail, generate_verification_code, ...)
// so controllers and routes can call them without needing to require the file everywhere.
@require_once __DIR__ . '/../app/Helpers.php';

require_once __DIR__ . '/../routes/web.php';