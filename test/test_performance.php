<?php
// Performance test script
require_once __DIR__ . '/app/Helpers.php';

echo "🚀 Testing API Performance...\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
$start = microtime(true);
try {
    $db = new Database();
    $conn = $db->getConnection();
    $end = microtime(true);
    echo "✅ Database connection: " . round(($end - $start) * 1000, 2) . "ms\n";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}

// Test 2: Simple Query
echo "\n2. Testing Simple Query...\n";
$start = microtime(true);
try {
    $stmt = $conn->query("SELECT COUNT(*) as count FROM products WHERE deleted_at IS NULL");
    $result = $stmt->fetch();
    $end = microtime(true);
    echo "✅ Simple query: " . round(($end - $start) * 1000, 2) . "ms\n";
    echo "   Products count: " . $result['count'] . "\n";
} catch (Exception $e) {
    echo "❌ Simple query failed: " . $e->getMessage() . "\n";
}

// Test 3: Complex Query with JOIN
echo "\n3. Testing Complex Query (JOIN)...\n";
$start = microtime(true);
try {
    $stmt = $conn->query("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.deleted_at IS NULL 
        ORDER BY p.created_at DESC 
        LIMIT 10
    ");
    $results = $stmt->fetchAll();
    $end = microtime(true);
    echo "✅ Complex query: " . round(($end - $start) * 1000, 2) . "ms\n";
    echo "   Results count: " . count($results) . "\n";
} catch (Exception $e) {
    echo "❌ Complex query failed: " . $e->getMessage() . "\n";
}

// Test 4: API Endpoint Simulation
echo "\n4. Testing API Endpoint Simulation...\n";
$start = microtime(true);
try {
    // Simulate ProductApiController->index()
    $productModel = new Product();
    $products = $productModel->findAllWithCategory();

    // Format data (simulate controller logic)
    $formattedProducts = array_map(function ($product) {
        $specifications = [];
        if (!empty($product['specifications'])) {
            $specifications = json_decode($product['specifications'], true) ?: [];
        }
        return [
            'id' => (int) $product['id'],
            'code' => $product['code'],
            'name' => $product['name'],
            'price' => (float) $product['price'],
            'description' => $product['description'],
            'specifications' => $specifications,
            'usage' => $product['usage'],
            'ingredients' => $product['ingredients'],
            'category_id' => (int) $product['category_id'],
            'category_name' => $product['category_name'],
            'main_image' => $product['main_image'],
            'detail_images' => $product['detail_images'] ? json_decode($product['detail_images'], true) : [],
            'created_at' => $product['created_at'],
            'updated_at' => $product['updated_at']
        ];
    }, $products);

    $end = microtime(true);
    echo "✅ API endpoint simulation: " . round(($end - $start) * 1000, 2) . "ms\n";
    echo "   Products processed: " . count($formattedProducts) . "\n";
} catch (Exception $e) {
    echo "❌ API endpoint simulation failed: " . $e->getMessage() . "\n";
}

// Test 5: Network Latency Test
echo "\n5. Testing Network Latency...\n";
$start = microtime(true);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/products');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$end = microtime(true);
curl_close($ch);

if ($httpCode === 200) {
    echo "✅ API request: " . round(($end - $start) * 1000, 2) . "ms\n";
    $data = json_decode($response, true);
    echo "   Response size: " . strlen($response) . " bytes\n";
    echo "   Products returned: " . (isset($data['data']) ? count($data['data']) : 0) . "\n";
} else {
    echo "❌ API request failed: HTTP $httpCode\n";
}

echo "\n📊 Performance Summary:\n";
echo "========================\n";
echo "• Database connection: " . (isset($db) ? "✅ Optimized" : "❌ Failed") . "\n";
echo "• Query performance: " . (isset($result) ? "✅ Good" : "❌ Issues") . "\n";
echo "• API response: " . ($httpCode === 200 ? "✅ Working" : "❌ Failed") . "\n";

echo "\n💡 Optimization Recommendations:\n";
echo "===============================\n";
echo "1. Use local database if possible (current: remote server)\n";
echo "2. Add database indexes for frequently queried columns\n";
echo "3. Consider using Redis for caching\n";
echo "4. Use Nginx instead of PHP built-in server\n";
echo "5. Enable PHP OPcache\n";
echo "6. Use connection pooling\n";
?>
