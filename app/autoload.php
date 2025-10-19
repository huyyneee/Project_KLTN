<?php
/**
 * Simple Autoloader cho dự án
 * Tự động load các class khi được sử dụng
 */

spl_autoload_register(function ($className) {
    // Convert namespace to file path
    $className = ltrim($className, '\\');
    $fileName = '';
    $namespace = '';

    if ($lastNsPos = strrpos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }

    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    // Base directory là thư mục app
    $baseDir = __DIR__ . DIRECTORY_SEPARATOR;

    // Các thư mục cần tìm kiếm
    $directories = [
        $baseDir,
        $baseDir . 'Controllers' . DIRECTORY_SEPARATOR,
        $baseDir . 'Models' . DIRECTORY_SEPARATOR,
        $baseDir . 'Core' . DIRECTORY_SEPARATOR,
    ];

    foreach ($directories as $directory) {
        $fullPath = $directory . $fileName;
        if (file_exists($fullPath)) {
            require_once $fullPath;
            return;
        }
    }

    // Nếu không tìm thấy trong các thư mục trên, thử tìm trong thư mục gốc
    $rootPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . $fileName;
    if (file_exists($rootPath)) {
        require_once $rootPath;
        return;
    }
});
?>