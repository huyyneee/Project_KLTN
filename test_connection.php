<?php
// Test database connection
require_once __DIR__ . '/app/Core/Database.php';

echo "Testing database connection...\n\n";

try {
    $database = new App\Core\Database();
    $conn = $database->getConnection();

    if ($conn) {
        echo "✅ Database connection successful!\n";
        echo "✅ Connected to database successfully\n\n";

        // Test if tables exist
        $tables = ['accounts', 'users', 'categories', 'products'];

        foreach ($tables as $table) {
            try {
                $stmt = $conn->query("SELECT COUNT(*) as count FROM {$table}");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                echo "✅ Table '{$table}' exists with {$result['count']} records\n";
            } catch (PDOException $e) {
                echo "❌ Table '{$table}' does not exist: " . $e->getMessage() . "\n";
            }
        }

    } else {
        echo "❌ Database connection failed!\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>