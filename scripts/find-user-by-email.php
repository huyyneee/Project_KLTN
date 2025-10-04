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

if ($argc < 2) {
    echo "Usage: php find-user-by-email.php <email>\n";
    exit(1);
}
$email = $argv[1];
try {
    $db = (new App\Core\Database())->getConnection();
    $stmt = $db->prepare('SELECT * FROM accounts WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $acc = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$acc) {
        echo "No account found for $email\n";
        exit(0);
    }
    echo "Account: id={$acc['id']} email={$acc['email']} created_at={$acc['created_at']}\n";
    $stmt = $db->prepare('SELECT * FROM users WHERE account_id = :aid LIMIT 1');
    $stmt->execute([':aid' => $acc['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        echo "User row: id={$user['id']} account_id={$user['account_id']} full_name={$user['full_name']} birthday={$user['birthday']} gender={$user['gender']}\n";
    } else {
        echo "No users row for account_id={$acc['id']}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
