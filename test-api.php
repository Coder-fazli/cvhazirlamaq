<?php
// Test script to diagnose API key issues
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>API Key Diagnostics</h2>";

// Read the API key file
$apiKeyFile = __DIR__ . '/api_key.txt';
if (file_exists($apiKeyFile)) {
    $apiKey = file_get_contents($apiKeyFile);
    $apiKeyTrimmed = trim($apiKey);
    
    echo "<p><strong>API key file exists:</strong> YES</p>";
    echo "<p><strong>Raw file size:</strong> " . strlen($apiKey) . " bytes</p>";
    echo "<p><strong>Trimmed key length:</strong> " . strlen($apiKeyTrimmed) . " characters</p>";
    echo "<p><strong>Key starts with:</strong> " . substr($apiKeyTrimmed, 0, 20) . "...</p>";
    echo "<p><strong>Key ends with:</strong> ..." . substr($apiKeyTrimmed, -20) . "</p>";
    
    // Check for invisible characters
    $hasInvisible = strlen($apiKey) !== strlen($apiKeyTrimmed);
    echo "<p><strong>Has invisible characters:</strong> " . ($hasInvisible ? 'YES' : 'NO') . "</p>";
    
    // Show hex dump of first and last 20 characters
    echo "<p><strong>First 20 chars (hex):</strong> " . bin2hex(substr($apiKeyTrimmed, 0, 20)) . "</p>";
    echo "<p><strong>Last 20 chars (hex):</strong> " . bin2hex(substr($apiKeyTrimmed, -20)) . "</p>";
    
    // Test API call
    echo "<h3>Testing API Connection</h3>";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.openai.com/v1/models',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $apiKeyTrimmed,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => true
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "<p><strong>HTTP Status Code:</strong> $httpCode</p>";
    
    if (curl_error($ch)) {
        echo "<p><strong>CURL Error:</strong> " . curl_error($ch) . "</p>";
    } else {
        echo "<p><strong>Response:</strong></p>";
        echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
    }
    
    curl_close($ch);
    
} else {
    echo "<p><strong>API key file:</strong> NOT FOUND</p>";
}

?>