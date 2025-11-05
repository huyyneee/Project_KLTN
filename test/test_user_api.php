<?php
/**
 * Test script cho User API
 * Chạy script này để test các endpoint của User API
 */

// Test function để gọi API
function testApi($url, $method = 'GET', $data = null)
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

echo "=== TEST USER API ===\n\n";

// Test 1: Lấy danh sách tất cả khách hàng
echo "1. Test GET /api/users - Lấy danh sách tất cả khách hàng\n";
$result = testApi('http://localhost/api/users');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 2: Lấy thông tin chi tiết khách hàng (ID = 1)
echo "2. Test GET /api/users/1 - Lấy thông tin chi tiết khách hàng\n";
$result = testApi('http://localhost/api/users/1');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 3: Tìm kiếm khách hàng
echo "3. Test GET /api/users/search?q=Nguyen - Tìm kiếm khách hàng\n";
$result = testApi('http://localhost/api/users/search?q=Nguyen');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Test 4: Lấy danh sách khách hàng với phân trang
echo "4. Test GET /api/users/paginated?page=1&limit=5 - Lấy danh sách với phân trang\n";
$result = testApi('http://localhost/api/users/paginated?page=1&limit=5');
echo "Status: " . $result['status'] . "\n";
echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

echo "=== END TEST ===\n";
?>