<?php
// Complete Order API test with session
echo "Testing Order API with session...\n\n";

// Step 1: Login
echo "1. Logging in...\n";
$loginUrl = 'http://localhost:8000/api/login';
$loginData = json_encode([
    'email' => 'email',
    'password' => 'password'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($loginData)
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookies.txt'); // Save cookies
curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Use cookies

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login Response (HTTP $httpCode): $response\n\n";

if ($httpCode === 200) {
    // Step 2: Test Orders API
    echo "2. Testing Orders API...\n";
    $ordersUrl = 'http://localhost:8000/api/orders';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ordersUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt'); // Use saved cookies
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Orders Response (HTTP $httpCode): $response\n\n";
    
    // Step 3: Test specific order
    echo "3. Testing specific order (ID: 26)...\n";
    $orderUrl = 'http://localhost:8000/api/orders/26';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $orderUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Order Detail Response (HTTP $httpCode): $response\n\n";
    
    // Step 4: Test approve order
        echo "4. Testing approve order (ID: 26)...\n";
    $approveUrl = 'http://localhost:8000/api/orders/26/approve';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $approveUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookies.txt');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Approve Order Response (HTTP $httpCode): $response\n\n";
    
} else {
    echo "Login failed, cannot test orders API\n";
}

// Cleanup
if (file_exists('cookies.txt')) {
    unlink('cookies.txt');
}

echo "Test completed!\n";
?>
