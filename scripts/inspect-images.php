<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = (new Database())->getConnection();
$stmt = $db->prepare('SELECT id, product_id, url, is_main FROM product_images ORDER BY id DESC LIMIT 50');
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) {
    echo "No product_images rows found.\n";
    exit(0);
}
foreach ($rows as $r) {
    $url = $r['url'];
    $checkPath = null;
    // normalize: if url starts with / remove leading slash for file path
    if (preg_match('#^https?://#i', $url)) {
        $exists = 'remote';
    } else {
        $p = $url;
        if (strpos($p, '/') === 0) $p = substr($p,1);
        $fs = __DIR__ . '/../public/' . $p;
        $exists = file_exists($fs) ? 'yes' : 'no';
        $checkPath = $fs;
    }
    echo sprintf("id=%s product_id=%s is_main=%s url=%s exists=%s %s\n", $r['id'], $r['product_id'], $r['is_main'], $r['url'], $exists, $checkPath ? 'path='.$checkPath : '');
}
