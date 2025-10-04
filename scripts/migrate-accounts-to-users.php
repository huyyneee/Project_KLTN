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

try {
    $db = (new App\Core\Database())->getConnection();
    // find accounts that don't have corresponding users
    $sql = "SELECT a.id, a.full_name, a.created_at, a.updated_at FROM accounts a LEFT JOIN users u ON u.account_id = a.id WHERE u.account_id IS NULL";
    $stmt = $db->query($sql);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$rows) {
        echo "No accounts to migrate.\n";
        exit(0);
    }
    $insert = $db->prepare("INSERT INTO users (account_id, full_name, phone, address, birthday, gender, created_at, updated_at) VALUES (:account_id, :full_name, :phone, :address, :birthday, :gender, :created_at, :updated_at)");
    $count = 0;
    foreach ($rows as $r) {
        $insert->execute([
            ':account_id' => $r['id'],
            ':full_name' => $r['full_name'] ?? '',
            ':phone' => '',
            ':address' => '',
            ':birthday' => null,
            ':gender' => null,
            ':created_at' => $r['created_at'] ?? date('Y-m-d H:i:s'),
            ':updated_at' => $r['updated_at'] ?? date('Y-m-d H:i:s')
        ]);
        $count++;
        echo "Migrated account_id={$r['id']}\n";
    }
    echo "Done. Migrated $count accounts.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
