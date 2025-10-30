<?php
// Test Orders API with session authentication
$baseUrl = 'http://localhost:8080';

echo "Testing Orders API with session authentication...\n\n";

// Step 1: Login to get session
echo "Step 1: Login to get session...\n";
$loginData = [
    'email' => 'kaiser@gmail.com',
    'password' => 'your_password_here'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Login Response (HTTP $httpCode):\n";
echo $response . "\n\n";

if ($httpCode !== 200) {
    echo "❌ Login failed! Please check credentials.\n";
    exit(1);
}

$loginResult = json_decode($response, true);
if (!$loginResult['success']) {
    echo "❌ Login failed: " . $loginResult['message'] . "\n";
    exit(1);
}

echo "✅ Login successful!\n";
echo "Account ID: " . $loginResult['data']['account_id'] . "\n";
echo "Role: " . $loginResult['data']['role'] . "\n\n";

// Step 2: Get orders using session
echo "Step 2: Getting orders...\n";
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/api/orders');
curl_setopt($ch, CURLOPT_POST, false);
curl_setopt($ch, CURLOPT_HTTPGET, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

echo "Orders Response (HTTP $httpCode):\n";
echo $response . "\n\n";

if ($httpCode === 200) {
    $ordersResult = json_decode($response, true);
    if ($ordersResult['success']) {
        echo "✅ Orders retrieved successfully!\n";
        echo "Total orders: " . count($ordersResult['data']['orders']) . "\n";
        if (isset($ordersResult['data']['pagination'])) {
            echo "Pagination: " . json_encode($ordersResult['data']['pagination']) . "\n";
        }
    } else {
        echo "❌ Failed to get orders: " . $ordersResult['message'] . "\n";
    }
} else {
    echo "❌ Failed to get orders (HTTP $httpCode)\n";
}

curl_close($ch);

// Clean up
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "\nTest completed!\n";
?>