<?php
require __DIR__ . '/../vendor/autoload.php';
// register App\ autoloader
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require_once $file;
});

// use Database directly
$db = (new App\Core\Database())->getConnection();
echo "Accounts:\n";
$stmt = $db->query('SELECT id, email, created_at FROM accounts ORDER BY id DESC LIMIT 10');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "- id={$r['id']} email={$r['email']} created_at={$r['created_at']}\n";
}

echo "\nUsers:\n";
$stmt = $db->query('SELECT id, account_id, full_name, birthday FROM users ORDER BY id DESC LIMIT 10');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "- id={$r['id']} account_id={$r['account_id']} full_name={$r['full_name']} birthday={$r['birthday']}\n";
}
