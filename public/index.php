<?php
// Main entry point for the application
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