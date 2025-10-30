<?php
// Debug pagination

echo "=== DEBUG PAGINATION ===\n\n";

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

    echo "Testing with search parameter...\n";

    $limit = 5;
    $offset = 0;
    $search = 'nước hoa';
    $category_id = null;
    $sort_by = 'created_at';
    $sort_order = 'DESC';

    echo "Parameters:\n";
    echo "- limit: $limit\n";
    echo "- offset: $offset\n";
    echo "- search: '$search'\n";
    echo "- category_id: " . ($category_id ?? 'null') . "\n";
    echo "- sort_by: $sort_by\n";
    echo "- sort_order: $sort_order\n\n";

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

    if (!empty($result['data'])) {
        echo "- First item: {$result['data'][0]['name']}\n";
    }

} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG COMPLETED ===\n";
?>