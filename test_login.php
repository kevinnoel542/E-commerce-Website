<?php
/**
 * Test script to debug login functionality
 */

// Test the FastAPI login endpoint directly
$fastApiUrl = 'http://localhost:8000/api/v1/auth/login';
$credentials = [
    'email' => 'itslugenge96@gmail.com',
    'password' => 'user123'
];

echo "Testing FastAPI login endpoint...\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $fastApiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($credentials));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "\nParsed Response:\n";
    echo "Message: " . ($data['message'] ?? 'N/A') . "\n";
    echo "User ID: " . ($data['user']['id'] ?? 'N/A') . "\n";
    echo "User Email: " . ($data['user']['email'] ?? 'N/A') . "\n";
    echo "User Role: " . ($data['user']['role'] ?? 'N/A') . "\n";
    echo "Access Token: " . (isset($data['tokens']['access_token']) ? 'Present' : 'Missing') . "\n";
    echo "Refresh Token: " . (isset($data['tokens']['refresh_token']) ? 'Present' : 'Missing') . "\n";
} else {
    echo "Login failed with HTTP code: $httpCode\n";
}
