<?php
// Test pagination API

echo "=== TESTING PAGINATION API ===\n\n";

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

echo "Testing Product API with pagination...\n\n";

// Test different pagination scenarios
$testCases = [
    [
        'name' => 'Basic pagination - page 1, limit 5',
        'params' => ['page' => 1, 'limit' => 5]
    ],
    [
        'name' => 'Page 2, limit 3',
        'params' => ['page' => 2, 'limit' => 3]
    ],
    [
        'name' => 'Search with pagination',
        'params' => ['page' => 1, 'limit' => 5, 'search' => 'nước hoa']
    ],
    [
        'name' => 'Sort by name ASC',
        'params' => ['page' => 1, 'limit' => 5, 'sort_by' => 'name', 'sort_order' => 'ASC']
    ],
    [
        'name' => 'Sort by price DESC',
        'params' => ['page' => 1, 'limit' => 5, 'sort_by' => 'price', 'sort_order' => 'DESC']
    ]
];

try {
    $productModel = new \App\Models\Product();

    foreach ($testCases as $testCase) {
        echo "--- {$testCase['name']} ---\n";

        $params = $testCase['params'];
        $limit = $params['limit'] ?? 10;
        $offset = (($params['page'] ?? 1) - 1) * $limit;
        $search = $params['search'] ?? '';
        $category_id = $params['category_id'] ?? null;
        $sort_by = $params['sort_by'] ?? 'created_at';
        $sort_order = $params['sort_order'] ?? 'DESC';

        $result = $productModel->findAllWithCategoryPaginated(
            $limit,
            $offset,
            $search,
            $category_id,
            $sort_by,
            $sort_order
        );

        echo "Current page: {$result['current_page']}\n";
        echo "Total pages: {$result['total_pages']}\n";
        echo "Total items: {$result['total_items']}\n";
        echo "Per page: {$result['per_page']}\n";
        echo "Has next: " . ($result['has_next'] ? 'Yes' : 'No') . "\n";
        echo "Has prev: " . ($result['has_prev'] ? 'Yes' : 'No') . "\n";
        echo "Items returned: " . count($result['data']) . "\n";

        if (!empty($result['data'])) {
            echo "First item: {$result['data'][0]['name']}\n";
        }

        echo "\n";
    }

    echo "✅ All pagination tests completed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>