<?php
// Main API endpoint for AI Name Rewriter
// Handles all frontend requests and coordinates backend services

require_once 'config.php';
require_once 'openai-handler.php';
require_once 'rate-limiter.php';

// Set security headers
setSecurityHeaders();

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get client IP
$clientIP = getClientIP();

// Initialize services
$rateLimiter = new RateLimiter();
$aiHandler = new OpenAIHandler();

// Check rate limiting
$rateLimitCheck = $rateLimiter->canMakeRequest($clientIP);
if (!$rateLimitCheck['allowed']) {
    http_response_code(429);
    echo json_encode([
        'error' => 'Rate limit exceeded',
        'message' => $rateLimitCheck['message'],
        'reset_time' => $rateLimitCheck['reset_time'],
        'reason' => $rateLimitCheck['reason']
    ]);
    exit;
}

// Check for abuse patterns
if ($rateLimiter->detectAbuse($clientIP)) {
    http_response_code(429);
    echo json_encode([
        'error' => 'Request pattern detected as potential abuse',
        'message' => 'Çox sürətli sorğular göndərirsiniz. Zəhmət olmasa daha yavaş cəhd edin.'
    ]);
    exit;
}

// Get and validate input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

// Validate required fields
$text = trim($input['text'] ?? '');
if (empty($text)) {
    http_response_code(400);
    echo json_encode(['error' => 'Text field is required']);
    exit;
}

// Validate text length
if (strlen($text) > 5000) {
    http_response_code(400);
    echo json_encode(['error' => 'Text is too long. Maximum 5000 characters allowed.']);
    exit;
}

if (strlen($text) < 2) {
    http_response_code(400);
    echo json_encode(['error' => 'Text is too short. Minimum 2 characters required.']);
    exit;
}

// Get optional parameters with defaults
$creativity = $input['creativity'] ?? 'balanced';
$language = $input['language'] ?? 'az';
$style = $input['style'] ?? 'modern';

// Validate parameters
$validCreativity = ['conservative', 'balanced', 'creative', 'poetic'];
$validLanguages = ['az', 'en', 'tr'];
$validStyles = ['modern', 'academic', 'poetic', 'casual'];

if (!in_array($creativity, $validCreativity)) {
    $creativity = 'balanced';
}

if (!in_array($language, $validLanguages)) {
    $language = 'az';
}

if (!in_array($style, $validStyles)) {
    $style = 'modern';
}

// Basic content filtering
if (containsInappropriateContent($text)) {
    http_response_code(400);
    echo json_encode([
        'error' => 'Content not allowed',
        'message' => 'Mətn uyğunsuz məzmun ehtiva edir.'
    ]);
    exit;
}

try {
    // Record the request
    $rateLimiter->recordRequest($clientIP);
    
    // Log request for analytics
    logRequest($clientIP, $text, $creativity, $language, $style);
    
    // Process with AI
    $startTime = microtime(true);
    $result = $aiHandler->rewriteContent($text, $creativity, $language, $style);
    $processingTime = round((microtime(true) - $startTime) * 1000); // Convert to milliseconds
    
    if ($result['success']) {
        // Get user stats for response
        $userStats = $rateLimiter->getUserStats($clientIP);
        
        // Prepare successful response
        $response = [
            'success' => true,
            'rewritten_text' => $result['rewritten_text'],
            'original_length' => $result['original_length'],
            'rewritten_length' => $result['rewritten_length'],
            'creativity_level' => $result['creativity_level'],
            'language' => $result['language'],
            'style' => $result['style'],
            'processing_time_ms' => $processingTime,
            'remaining_requests' => [
                'hourly' => $userStats['hourly_remaining'],
                'daily' => $userStats['daily_remaining']
            ],
            'model_used' => OPENAI_MODEL
        ];
        
        // Log successful processing
        logSuccess($clientIP, $processingTime, strlen($result['rewritten_text']));
        
        http_response_code(200);
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        
    } else {
        // Handle AI processing error
        logError("AI processing failed for IP $clientIP: " . $result['error']);
        
        http_response_code(500);
        echo json_encode([
            'error' => 'Processing failed',
            'message' => 'AI xidməti müvəqqəti əlçatmazdır. Zəhmət olmasa bir az sonra yenidən cəhd edin.'
        ]);
    }
    
} catch (Exception $e) {
    // Handle unexpected errors
    logError("Unexpected error for IP $clientIP: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal server error',
        'message' => 'Daxili server xətası. Zəhmət olmasa dəstək ilə əlaqə saxlayın.'
    ]);
}

/**
 * Check for inappropriate content
 */
function containsInappropriateContent($text) {
    // Basic content filtering - can be enhanced
    $inappropriateWords = [
        // Add inappropriate words in multiple languages as needed
        'spam', 'hack', 'virus', 'malware'
    ];
    
    $textLower = strtolower($text);
    foreach ($inappropriateWords as $word) {
        if (strpos($textLower, $word) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Log request for analytics
 */
function logRequest($ip, $text, $creativity, $language, $style) {
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip_hash' => substr(md5($ip), 0, 8), // Anonymized IP
        'text_length' => strlen($text),
        'creativity' => $creativity,
        'language' => $language,
        'style' => $style,
        'is_name' => (count(explode(' ', trim($text))) <= 3 && preg_match('/^[a-zA-ZəöüçşığĞÜÇŞıİÖƏ\s]+$/', $text))
    ];
    
    $logEntry = json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents('requests.log', $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Log successful processing
 */
function logSuccess($ip, $processingTime, $outputLength) {
    $logData = [
        'timestamp' => date('Y-m-d H:i:s'),
        'ip_hash' => substr(md5($ip), 0, 8),
        'processing_time_ms' => $processingTime,
        'output_length' => $outputLength,
        'status' => 'success'
    ];
    
    $logEntry = json_encode($logData) . "\n";
    file_put_contents('success.log', $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Get system statistics (admin endpoint)
 */
if (isset($_GET['stats']) && $_GET['admin_key'] === 'your-admin-key-here') {
    $rateLimiter = new RateLimiter();
    $systemStats = $rateLimiter->getSystemStats();
    
    echo json_encode([
        'system_stats' => $systemStats,
        'server_time' => date('Y-m-d H:i:s'),
        'model' => OPENAI_MODEL
    ], JSON_PRETTY_PRINT);
    exit;
}
?>