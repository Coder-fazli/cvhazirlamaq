<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>PHP Debug Information</h2>";

// Check PHP version
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";

// Check if config file exists and is readable
$configPath = __DIR__ . '/config.php';
echo "<p><strong>Config file exists:</strong> " . (file_exists($configPath) ? 'YES' : 'NO') . "</p>";
echo "<p><strong>Config file readable:</strong> " . (is_readable($configPath) ? 'YES' : 'NO') . "</p>";

// Try to include config
try {
    include $configPath;
    echo "<p><strong>Config loaded:</strong> YES</p>";
    
    // Check if OPENAI_API_KEY is defined (without showing the actual key)
    echo "<p><strong>API Key defined:</strong> " . (defined('OPENAI_API_KEY') ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>API Key length:</strong> " . (defined('OPENAI_API_KEY') ? strlen(OPENAI_API_KEY) : '0') . "</p>";
    
} catch (Exception $e) {
    echo "<p><strong>Config error:</strong> " . $e->getMessage() . "</p>";
}

// Check if cURL is available
echo "<p><strong>cURL available:</strong> " . (function_exists('curl_init') ? 'YES' : 'NO') . "</p>";

// Check file permissions for required files
$files = ['ai-api.php', 'openai-handler.php', 'rate-limiter.php'];
foreach ($files as $file) {
    $filePath = __DIR__ . '/' . $file;
    echo "<p><strong>$file exists:</strong> " . (file_exists($filePath) ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>$file readable:</strong> " . (is_readable($filePath) ? 'YES' : 'NO') . "</p>";
}

// Test a simple API call simulation
echo "<h3>Testing API Components</h3>";

try {
    // Test rate limiter
    if (file_exists(__DIR__ . '/rate-limiter.php')) {
        include_once __DIR__ . '/rate-limiter.php';
        echo "<p><strong>Rate limiter loaded:</strong> YES</p>";
    }
    
    // Test OpenAI handler
    if (file_exists(__DIR__ . '/openai-handler.php')) {
        include_once __DIR__ . '/openai-handler.php';
        echo "<p><strong>OpenAI handler loaded:</strong> YES</p>";
    }
    
} catch (Exception $e) {
    echo "<p><strong>Component loading error:</strong> " . $e->getMessage() . "</p>";
}

echo "<h3>Server Environment</h3>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Script Name:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";

// Check if directories are writable
$dirs = ['usage_data', 'logs'];
foreach ($dirs as $dir) {
    $dirPath = __DIR__ . '/' . $dir;
    echo "<p><strong>$dir directory exists:</strong> " . (is_dir($dirPath) ? 'YES' : 'NO') . "</p>";
    echo "<p><strong>$dir directory writable:</strong> " . (is_writable($dirPath) ? 'YES' : 'NO') . "</p>";
}

?>