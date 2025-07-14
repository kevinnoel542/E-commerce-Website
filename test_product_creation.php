<?php

// Simple test script to debug product creation
require_once 'frontend-laravel/vendor/autoload.php';

use Illuminate\Http\Client\Factory as HttpClient;

// Test data
$testData = [
    'name' => 'Test Product',
    'description' => 'This is a test product',
    'price' => 29.99,
    'category_id' => null,
    'brand' => 'Test Brand',
    'stock_quantity' => 10,
    'images' => []
];

// Test JWT token (you'll need to get this from your session)
$token = 'YOUR_JWT_TOKEN_HERE'; // Replace with actual token

$fastApiUrl = 'http://localhost:8000/api/v1';

echo "Testing product creation...\n";
echo "Data to send: " . json_encode($testData, JSON_PRETTY_PRINT) . "\n\n";

try {
    $http = new HttpClient();
    
    $response = $http->timeout(30)
        ->withHeaders([
            'Authorization' => "Bearer {$token}",
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])
        ->post("{$fastApiUrl}/products/", $testData);

    echo "Response Status: " . $response->status() . "\n";
    echo "Response Headers: " . json_encode($response->headers(), JSON_PRETTY_PRINT) . "\n";
    echo "Response Body: " . $response->body() . "\n";

    if ($response->successful()) {
        echo "✅ Product created successfully!\n";
    } else {
        echo "❌ Product creation failed!\n";
    }

} catch (Exception $e) {
    echo "❌ Exception occurred: " . $e->getMessage() . "\n";
}
