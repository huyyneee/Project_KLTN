<?php
// Serve product main image by product id or image id.
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../config/config.php';

use App\Core\Database;

$db = (new Database())->getConnection();

$imageId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$productId = isset($_GET['product']) ? (int) $_GET['product'] : 0;

$row = null;
if ($imageId > 0) {
    $stmt = $db->prepare('SELECT * FROM product_images WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $imageId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
} elseif ($productId > 0) {
    // only accept the image marked as is_main for this product
    $stmt = $db->prepare('SELECT * FROM product_images WHERE product_id = :pid AND is_main = 1 LIMIT 1');
    $stmt->execute([':pid' => $productId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // if no is_main image, return placeholder immediately
    if (!$row) {
        header('X-Image-Source: no-main');
        send_placeholder();
    }
}

// simple inline SVG placeholder
function send_placeholder()
{
    header('Content-Type: image/svg+xml');
    header('Cache-Control: public, max-age=3600');
    echo '<?xml version="1.0" encoding="UTF-8"?><svg xmlns="http://www.w3.org/2000/svg" width="400" height="300" viewBox="0 0 400 300"><rect width="100%" height="100%" fill="#f3f4f6"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#9ca3af" font-family="Arial,Helvetica,sans-serif" font-size="18">No image</text></svg>';
    exit;
}

if (!$row) {
    send_placeholder();
}

$url = trim($row['url'] ?? '');
// normalize common escaped/encoded forms stored in DB (e.g. http:\/\/host or quoted strings)
$url = str_replace('\\/', '/', $url);
$url = trim($url, "'\" \t\n\r\0\x0B");
if (!$url) {
    send_placeholder();
}

// If url is absolute http(s) or protocol relative, try to fetch it
if (preg_match('#^https?://#i', $url) || preg_match('#^//+#', $url)) {
    // proxy remote URL
    $remote = $url;
    $ch = curl_init($remote);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // set a sensible user agent and timeouts
    curl_setopt($ch, CURLOPT_USERAGENT, 'Project_KLTN_ImageProxy/1.0');
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ctype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'image/jpeg';
    curl_close($ch);
    if ($code === 200 && $data !== false) {
        header('Content-Type: ' . $ctype);
        header('Cache-Control: public, max-age=86400');
        header('X-Image-Source: remote');
        echo $data;
        exit;
    }
    // try a fallback with file_get_contents (if allow_url_fopen enabled)
    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create(['http' => ['timeout' => 6, 'header' => "User-Agent: Project_KLTN_ImageProxy/1.0\r\n"]]);
        $data2 = @file_get_contents($remote, false, $ctx);
        if ($data2 !== false) {
            // attempt to guess mime type from content or default
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected = finfo_buffer($finfo, $data2);
            finfo_close($finfo);
            $ctype2 = $detected ?: 'image/jpeg';
            header('Content-Type: ' . $ctype2);
            header('Cache-Control: public, max-age=86400');
            header('X-Image-Source: remote-fallback');
            echo $data2;
            exit;
        }
    }
    send_placeholder();
}

// Non-absolute URL: try local file under public
$local = __DIR__ . '/' . ltrim($url, '/');
if (file_exists($local) && is_file($local)) {
    $mime = @mime_content_type($local) ?: 'image/jpeg';
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=86400');
    header('X-Image-Source: local');
    readfile($local);
    exit;
}

// Not found locally: attempt to fetch from the current host using URL path
$host = $_SERVER['HTTP_HOST'] ?? null;
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
if ($host) {
    $remote = $scheme . '://' . $host . (strpos($url, '/') === 0 ? $url : '/' . $url);
    $ch = curl_init($remote);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ctype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'image/jpeg';
    curl_close($ch);
    if ($code === 200 && $data !== false) {
        header('Content-Type: ' . $ctype);
        header('Cache-Control: public, max-age=86400');
        header('X-Image-Source: host-fallback');
        echo $data;
        exit;
    }
}

// Additional fallback: try using DB host (if configured) with port 8000
$dbHost = null;
if (function_exists('env')) {
    $dbHost = env('DB_HOST');
}
if (!$dbHost) {
    // try config file as a last resort
    $cfgPath = __DIR__ . '/../config/config.php';
    if (file_exists($cfgPath)) {
        $cfg = require $cfgPath;
        $dbHost = $cfg['database']['host'] ?? null;
    }
}
if ($dbHost && strpos($url, '/') === 0) {
    $remote = 'http://' . $dbHost . ':8000' . $url;
    $ch = curl_init($remote);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $ctype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'image/jpeg';
    curl_close($ch);
    if ($code === 200 && $data !== false) {
        header('Content-Type: ' . $ctype);
        header('Cache-Control: public, max-age=86400');
        header('X-Image-Source: db-host-fallback');
        echo $data;
        exit;
    }
    // fallback to allow_url_fopen
    if (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create(['http' => ['timeout' => 6, 'header' => "User-Agent: Project_KLTN_ImageProxy/1.0\r\n"]]);
        $data2 = @file_get_contents($remote, false, $ctx);
        if ($data2 !== false) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected = finfo_buffer($finfo, $data2);
            finfo_close($finfo);
            $ctype2 = $detected ?: 'image/jpeg';
            header('Content-Type: ' . $ctype2);
            header('Cache-Control: public, max-age=86400');
            header('X-Image-Source: db-host-fallback-fopen');
            echo $data2;
            exit;
        }
    }
}

// nothing worked -> placeholder
header('X-Image-Source: placeholder');
send_placeholder();
