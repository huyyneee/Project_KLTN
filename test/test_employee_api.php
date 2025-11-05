<?php
// Test script for Employee API endpoints
header('Content-Type: application/json');

$baseUrl = 'http://159.65.2.46:8000/api';

echo "=== TESTING EMPLOYEE API ENDPOINTS ===\n\n";

// Test 1: GET /api/employees
echo "1. Testing GET /api/employees\n";
echo "URL: {$baseUrl}/employees\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/employees');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL Error: $error\n";
} else {
    echo "✅ HTTP Status: $httpCode\n";
    echo "Response: " . substr($response, 0, 200) . (strlen($response) > 200 ? '...' : '') . "\n";
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 2: POST /api/employees (Create new employee)
echo "2. Testing POST /api/employees (Create employee)\n";
echo "URL: {$baseUrl}/employees\n";

$employeeData = [
    'email' => 'test.employee@example.com',
    'password' => 'password123',
    'full_name' => 'Test Employee',
    'phone' => '0123456789',
    'address' => '123 Test Street',
    'birthday' => '1990-01-01',
    'gender' => 'male'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/employees');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($employeeData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ CURL Error: $error\n";
} else {
    echo "✅ HTTP Status: $httpCode\n";
    echo "Response: " . substr($response, 0, 200) . (strlen($response) > 200 ? '...' : '') . "\n";

    // Try to parse response to get employee ID
    $responseData = json_decode($response, true);
    if (isset($responseData['data']['id'])) {
        $employeeId = $responseData['data']['id'];
        echo "✅ Employee created with ID: $employeeId\n";

        // Test 3: GET /api/employees/{id}
        echo "\n3. Testing GET /api/employees/{$employeeId}\n";
        echo "URL: {$baseUrl}/employees/{$employeeId}\n";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/employees/' . $employeeId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo "❌ CURL Error: $error\n";
        } else {
            echo "✅ HTTP Status: $httpCode\n";
            echo "Response: " . substr($response, 0, 200) . (strlen($response) > 200 ? '...' : '') . "\n";
        }

        echo "\n" . str_repeat("-", 50) . "\n\n";

        // Test 4: PUT /api/employees/{id} (Update employee)
        echo "4. Testing PUT /api/employees/{$employeeId} (Update employee)\n";
        echo "URL: {$baseUrl}/employees/{$employeeId}\n";

        $updateData = [
            'full_name' => 'Updated Test Employee',
            'phone' => '0987654321',
            'address' => '456 Updated Street'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/employees/' . $employeeId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo "❌ CURL Error: $error\n";
        } else {
            echo "✅ HTTP Status: $httpCode\n";
            echo "Response: " . substr($response, 0, 200) . (strlen($response) > 200 ? '...' : '') . "\n";
        }

        echo "\n" . str_repeat("-", 50) . "\n\n";

        // Test 5: DELETE /api/employees/{id}
        echo "5. Testing DELETE /api/employees/{$employeeId}\n";
        echo "URL: {$baseUrl}/employees/{$employeeId}\n";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/employees/' . $employeeId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo "❌ CURL Error: $error\n";
        } else {
            echo "✅ HTTP Status: $httpCode\n";
            echo "Response: " . substr($response, 0, 200) . (strlen($response) > 200 ? '...' : '') . "\n";
        }
    }
}

echo "\n" . str_repeat("-", 50) . "\n\n";

// Test 6: Check if server is running
echo "6. Testing server connectivity\n";
echo "URL: {$baseUrl}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "❌ Server not reachable: $error\n";
    echo "❌ Please check if server is running on http://159.65.2.46:8000\n";
} else {
    echo "✅ Server is reachable (HTTP Status: $httpCode)\n";
}

echo "\n=== TEST COMPLETED ===\n";
?>