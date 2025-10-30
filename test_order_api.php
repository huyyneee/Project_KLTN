<?php
// Test Order API endpoints
session_start();

$base_url = 'http://localhost:8080';

// Function to make HTTP requests
function makeRequest($endpoint, $method = 'GET', $data = null)
{
    global $base_url;
    $url = $base_url . $endpoint;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'code' => $httpCode,
        'response' => json_decode($response, true)
    ];
}

// First, login as admin
$loginData = [
    'email' => 'kaiser@gmail.com',
    'password' => '123456'
];

echo "1. Testing admin login...\n";
$loginResult = makeRequest('/api/login', 'POST', $loginData);
echo "Status Code: " . $loginResult['code'] . "\n";
echo "Response: " . print_r($loginResult['response'], true) . "\n\n";

// Test GET /api/orders (list all orders)
echo "2. Testing GET all orders...\n";
$ordersResult = makeRequest('/api/orders');
echo "Status Code: " . $ordersResult['code'] . "\n";
echo "Response: " . print_r($ordersResult['response'], true) . "\n\n";

// Test GET /api/orders/{id} (get specific order)
$orderId = 1; // Replace with an actual order ID from your database
echo "3. Testing GET specific order (ID: $orderId)...\n";
$orderResult = makeRequest("/api/orders/$orderId");
echo "Status Code: " . $orderResult['code'] . "\n";
echo "Response: " . print_r($orderResult['response'], true) . "\n\n";

// Test POST /api/orders/{id}/approve (approve order)
echo "4. Testing approve order (ID: $orderId)...\n";
$approveResult = makeRequest("/api/orders/$orderId/approve", 'POST');
echo "Status Code: " . $approveResult['code'] . "\n";
echo "Response: " . print_r($approveResult['response'], true) . "\n\n";