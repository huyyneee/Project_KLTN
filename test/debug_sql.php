<?php
// Debug SQL generation

echo "=== DEBUG SQL GENERATION ===\n\n";

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
    $productModel = new \App\Models\Product();

    echo "Testing SQL generation...\n\n";

    $limit = 5;
    $offset = 0;
    $search = 'nước hoa';
    $category_id = null;
    $sort_by = 'created_at';
    $sort_order = 'DESC';

    // Build WHERE clause manually to debug
    $whereConditions = ["p.deleted_at IS NULL"];
    $params = [];

    // Add search condition
    if (!empty($search)) {
        $whereConditions[] = "(p.name LIKE :search OR p.description LIKE :search OR p.code LIKE :search)";
        $params[':search'] = "%{$search}%";
    }

    // Add category filter
    if ($category_id !== null) {
        $whereConditions[] = "p.category_id = :category_id";
        $params[':category_id'] = $category_id;
    }

    $whereClause = "WHERE " . implode(" AND ", $whereConditions);

    echo "WHERE clause: $whereClause\n";
    echo "Parameters:\n";
    foreach ($params as $key => $value) {
        echo "  $key => $value\n";
    }

    // Test count query
    $countSql = "SELECT COUNT(*) as total 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                {$whereClause}";

    echo "\nCount SQL: $countSql\n";

    // Test the actual method
    $result = $productModel->findAllWithCategoryPaginated(
        $limit,
        $offset,
        $search,
        $category_id,
        $sort_by,
        $sort_order
    );

    echo "Result:\n";
    echo "- Current page: {$result['current_page']}\n";
    echo "- Total pages: {$result['total_pages']}\n";
    echo "- Total items: {$result['total_items']}\n";
    echo "- Items returned: " . count($result['data']) . "\n";


} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
?>