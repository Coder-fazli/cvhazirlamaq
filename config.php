<?php
// Configuration file for AI Name Rewriter
// Security and API settings

// OpenAI API Configuration
// Load API key from external file (not tracked by git)
$apiKeyFile = __DIR__ . '/api_key.txt';
if (file_exists($apiKeyFile)) {
    define('OPENAI_API_KEY', trim(file_get_contents($apiKeyFile)));
} else {
    define('OPENAI_API_KEY', ''); // Will cause error if not set
}
define('OPENAI_MODEL', 'gpt-4o-mini');
define('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions');

// Rate limiting settings
define('MAX_REQUESTS_PER_IP_PER_HOUR', 50); // Generous limit
define('MAX_REQUESTS_PER_IP_PER_DAY', 200);

// Security settings
define('ALLOWED_ORIGINS', [
    'https://cvhazirlamaq.com',
    'https://www.cvhazirlamaq.com',
    'https://coder-fazli.github.io',
    'http://localhost' // For testing
]);

// Database file for tracking usage (simple file-based system)
define('USAGE_LOG_FILE', 'usage_log.json');

// Error logging
define('ERROR_LOG_FILE', 'error_log.txt');

// Security headers
function setSecurityHeaders() {
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    
    // CORS headers
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if (in_array($origin, ALLOWED_ORIGINS)) {
        header("Access-Control-Allow-Origin: $origin");
    }
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

// Utility function to log errors
function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message" . PHP_EOL;
    file_put_contents(ERROR_LOG_FILE, $logEntry, FILE_APPEND | LOCK_EX);
}

// Get client IP address
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}
?>