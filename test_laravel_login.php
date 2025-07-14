<?php
/**
 * Test script to debug Laravel login form submission
 */

// Test the Laravel login form submission
$laravelUrl = 'http://localhost:8080/auth/login';
$credentials = [
    'email' => 'itslugenge96@gmail.com',
    'password' => 'user123',
    '_token' => 'test-token' // We'll need to get the actual CSRF token
];

echo "Testing Laravel login form submission...\n";

// First, get the login page to extract CSRF token
echo "Getting CSRF token from login page...\n";
$loginPageUrl = 'http://localhost:8080/login';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginPageUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$loginPageContent = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login page HTTP Code: $httpCode\n";

if ($httpCode === 200) {
    // Extract CSRF token from the page
    if (preg_match('/<input[^>]*name="_token"[^>]*value="([^"]*)"/', $loginPageContent, $matches)) {
        $csrfToken = $matches[1];
        echo "CSRF Token found: " . substr($csrfToken, 0, 20) . "...\n";
        
        // Now submit the login form
        $credentials['_token'] = $csrfToken;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $laravelUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($credentials));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, '/tmp/cookies.txt');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        
        echo "\nLogin form submission results:\n";
        echo "HTTP Code: $httpCode\n";
        echo "Final URL: $finalUrl\n";
        echo "Response length: " . strlen($response) . " characters\n";
        
        // Check if we were redirected to dashboard
        if (strpos($finalUrl, 'dashboard') !== false) {
            echo "SUCCESS: Redirected to dashboard!\n";
        } else {
            echo "ISSUE: Not redirected to dashboard\n";
            echo "Response preview: " . substr($response, 0, 500) . "...\n";
        }
        
    } else {
        echo "ERROR: Could not find CSRF token in login page\n";
        echo "Page content preview: " . substr($loginPageContent, 0, 500) . "...\n";
    }
} else {
    echo "ERROR: Could not load login page. HTTP Code: $httpCode\n";
}
