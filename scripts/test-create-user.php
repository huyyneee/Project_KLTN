<?php
require __DIR__ . '/../vendor/autoload.php';

// register same PSR-4 autoloader used by public/index.php for App\ namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Models\UserModel;

/** create test user **/
$user = new UserModel();
$now = date('Y-m-d H:i:s');
$data = [
    'account_id' => 1,
    'full_name' => 'Test User CLI',
    'phone' => '',
    'address' => '',
    'birthday' => '2000-01-01',
    'gender' => 'male',
    'created_at' => $now,
    'updated_at' => $now
];
$id = $user->create($data);
echo "Inserted user id: $id\n";
