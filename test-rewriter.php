<?php
// Test the actual rewriter functionality step by step
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>AI Rewriter Test</h2>";

// Test 1: Load config
try {
    require_once 'config.php';
    echo "<p>✅ Config loaded successfully</p>";
    echo "<p><strong>API Key defined:</strong> " . (defined('OPENAI_API_KEY') ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>API Key length:</strong> " . strlen(OPENAI_API_KEY) . "</p>";
    echo "<p><strong>Model:</strong> " . OPENAI_MODEL . "</p>";
} catch (Exception $e) {
    echo "<p>❌ Config error: " . $e->getMessage() . "</p>";
    exit;
}

// Test 2: Load OpenAI handler
try {
    require_once 'openai-handler.php';
    echo "<p>✅ OpenAI handler loaded</p>";
} catch (Exception $e) {
    echo "<p>❌ OpenAI handler error: " . $e->getMessage() . "</p>";
    exit;
}

// Test 3: Create handler instance
try {
    $handler = new OpenAIHandler();
    echo "<p>✅ OpenAI handler instance created</p>";
} catch (Exception $e) {
    echo "<p>❌ Handler instance error: " . $e->getMessage() . "</p>";
    exit;
}

// Test 4: Try a simple rewrite
try {
    echo "<h3>Testing Simple Rewrite</h3>";
    $testText = "Salam, bu sadə testdir.";
    
    echo "<p><strong>Test text:</strong> $testText</p>";
    echo "<p>Calling rewriteContent...</p>";
    
    $result = $handler->rewriteContent($testText, 'balanced', 'az', 'modern');
    
    if ($result && $result['success']) {
        echo "<p>✅ <strong>SUCCESS!</strong></p>";
        echo "<p><strong>Original:</strong> " . htmlspecialchars($result['original_length']) . " chars</p>";
        echo "<p><strong>Rewritten:</strong> " . htmlspecialchars($result['rewritten_length']) . " chars</p>";
        echo "<p><strong>Result:</strong> " . htmlspecialchars($result['rewritten_text']) . "</p>";
    } else {
        echo "<p>❌ <strong>FAILED - No success in result</strong></p>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ <strong>EXCEPTION:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Stack trace:</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

// Test 5: Direct API call test
echo "<h3>Direct API Call Test</h3>";
try {
    $data = [
        'model' => OPENAI_MODEL,
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant.'
            ],
            [
                'role' => 'user', 
                'content' => 'Say hello in Azerbaijani'
            ]
        ],
        'temperature' => 0.7,
        'max_tokens' => 50
    ];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => OPENAI_API_URL,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . OPENAI_API_KEY,
            'Content-Type: application/json'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_SSL_VERIFYPEER => true
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo "<p><strong>Direct API Status:</strong> $httpCode</p>";
    
    if (curl_error($ch)) {
        echo "<p><strong>CURL Error:</strong> " . curl_error($ch) . "</p>";
    } else {
        echo "<p><strong>Response (first 500 chars):</strong></p>";
        echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . "</pre>";
    }
    
    curl_close($ch);
    
} catch (Exception $e) {
    echo "<p>❌ Direct API error: " . $e->getMessage() . "</p>";
}

?>