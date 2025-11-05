<?php
// Test API pagination

echo "=== TESTING API PAGINATION ===\n\n";

// Test different API endpoints with pagination
$baseUrl = "http://localhost:8000/api";

$testCases = [
    [
        'name' => 'Basic pagination - page 1, limit 5',
        'url' => $baseUrl . '/products?page=1&limit=5'
    ],
    [
        'name' => 'Page 2, limit 3',
        'url' => $baseUrl . '/products?page=2&limit=3'
    ],
    [
        'name' => 'Search with pagination',
        'url' => $baseUrl . '/products?page=1&limit=5&search=nước hoa'
    ],
    [
        'name' => 'Sort by name ASC',
        'url' => $baseUrl . '/products?page=1&limit=5&sort_by=name&sort_order=ASC'
    ],
    [
        'name' => 'Sort by price DESC',
        'url' => $baseUrl . '/products?page=1&limit=5&sort_by=price&sort_order=DESC'
    ]
];

foreach ($testCases as $testCase) {
    echo "--- {$testCase['name']} ---\n";
    echo "URL: {$testCase['url']}\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testCase['url']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo "HTTP Code: $httpCode\n";

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        echo "Response structure:\n";
        echo "- success: " . (isset($data['success']) ? ($data['success'] ? 'true' : 'false') : 'not set') . "\n";
        echo "- message: " . (isset($data['message']) ? $data['message'] : 'not set') . "\n";
        echo "- data type: " . (isset($data['data']) ? gettype($data['data']) : 'not set') . "\n";
        echo "- pagination: " . (isset($data['data']['pagination']) ? 'exists' : 'not exists') . "\n";

        if ($data && isset($data['data']) && isset($data['data']['pagination'])) {
            echo "✅ Success!\n";
            echo "Items returned: " . count($data['data']['data']) . "\n";
            echo "Current page: {$data['data']['pagination']['current_page']}\n";
            echo "Total pages: {$data['data']['pagination']['total_pages']}\n";
            echo "Total items: {$data['data']['pagination']['total_items']}\n";
            echo "Per page: {$data['data']['pagination']['per_page']}\n";
            if (!empty($data['data']['data'])) {
                echo "First item: {$data['data']['data'][0]['name']}\n";
            }
        } else {
            echo "❌ Invalid response format\n";
            echo "Raw response: " . substr($response, 0, 500) . "...\n";
        }
    } else {
        echo "❌ HTTP Error: $httpCode\n";
        echo "Response: " . substr($response, 0, 200) . "...\n";
    }

    echo "\n";
}

echo "=== TEST COMPLETED ===\n";
?>