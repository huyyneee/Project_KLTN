<?php
// Test employee role functionality

echo "=== TESTING EMPLOYEE ROLE ===\n\n";

// Include autoloader
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

// Load helpers
@require_once __DIR__ . '/../app/Helpers.php';

try {
    $db = (new \App\Core\Database())->getConnection();

    echo "Testing database structure...\n";

    // Check if role column exists in accounts table
    $checkSql = "SHOW COLUMNS FROM accounts LIKE 'role'";
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->execute();
    $roleColumn = $checkStmt->fetch(\PDO::FETCH_ASSOC);

    if ($roleColumn) {
        echo "✅ Role column exists in accounts table\n";
    } else {
        echo "❌ Role column does not exist. Adding it...\n";

        // Add role column
        $alterSql = "ALTER TABLE accounts ADD COLUMN role VARCHAR(50) DEFAULT 'user'";
        $alterStmt = $db->prepare($alterSql);
        $alterStmt->execute();

        echo "✅ Role column added successfully\n";
    }

    // Test creating employee with role
    echo "\nTesting employee creation with role...\n";

    $testData = [
        'email' => 'test.employee@example.com',
        'password' => 'password123',
        'full_name' => 'Test Employee',
        'phone' => '0123456789',
        'role' => 'employee'
    ];

    // Check if test employee already exists
    $checkUserSql = "SELECT id FROM accounts WHERE email = :email";
    $checkUserStmt = $db->prepare($checkUserSql);
    $checkUserStmt->execute([':email' => $testData['email']]);
    $existingUser = $checkUserStmt->fetch(\PDO::FETCH_ASSOC);

    if ($existingUser) {
        echo "Test employee already exists, deleting...\n";
        $deleteSql = "DELETE FROM accounts WHERE email = :email";
        $deleteStmt = $db->prepare($deleteSql);
        $deleteStmt->execute([':email' => $testData['email']]);
    }

    // Create test employee
    $db->beginTransaction();

    $accountSql = "INSERT INTO accounts (email, password, role, status, created_at) VALUES (:email, :password, :role, :status, :created_at)";
    $accountStmt = $db->prepare($accountSql);
    $accountStmt->execute([
        'email' => $testData['email'],
        'password' => md5($testData['password']),
        'role' => $testData['role'],
        'status' => 'active',
        'created_at' => date('Y-m-d H:i:s')
    ]);
    $accountId = $db->lastInsertId();

    $userSql = "INSERT INTO users (account_id, full_name, phone, created_at) VALUES (:account_id, :full_name, :phone, :created_at)";
    $userStmt = $db->prepare($userSql);
    $userStmt->execute([
        'account_id' => $accountId,
        'full_name' => $testData['full_name'],
        'phone' => $testData['phone'],
        'created_at' => date('Y-m-d H:i:s')
    ]);

    $db->commit();

    echo "✅ Test employee created successfully\n";

    // Test fetching employees with role
    echo "\nTesting employee fetch with role...\n";

    $fetchSql = "SELECT u.*, a.email, a.role, a.status as account_status, a.created_at as account_created 
                FROM users u 
                LEFT JOIN accounts a ON u.account_id = a.id
                WHERE a.role = 'employee'
                ORDER BY u.created_at DESC";

    $fetchStmt = $db->prepare($fetchSql);
    $fetchStmt->execute();
    $employees = $fetchStmt->fetchAll(\PDO::FETCH_ASSOC);

    echo "Found " . count($employees) . " employees\n";

    if (!empty($employees)) {
        $firstEmployee = $employees[0];
        echo "First employee:\n";
        echo "- Name: {$firstEmployee['full_name']}\n";
        echo "- Email: {$firstEmployee['email']}\n";
        echo "- Role: {$firstEmployee['role']}\n";
        echo "- Status: {$firstEmployee['account_status']}\n";
    }

    echo "\n✅ All tests completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>