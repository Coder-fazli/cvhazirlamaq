<?php
// Rate Limiter for AI Name Rewriter
// Prevents abuse and manages API costs

require_once 'config.php';

class RateLimiter {
    private $usageFile;
    
    public function __construct() {
        $this->usageFile = USAGE_LOG_FILE;
        $this->initializeUsageFile();
    }
    
    /**
     * Check if user can make a request
     */
    public function canMakeRequest($ip) {
        $usage = $this->getUsage();
        $currentTime = time();
        $currentDate = date('Y-m-d');
        $currentHour = date('Y-m-d-H');
        
        // Clean old data (older than 24 hours)
        $this->cleanOldData($usage, $currentTime);
        
        // Initialize user data if not exists
        if (!isset($usage[$ip])) {
            $usage[$ip] = [
                'hourly' => [],
                'daily' => []
            ];
        }
        
        // Check hourly limit
        $hourlyCount = $usage[$ip]['hourly'][$currentHour] ?? 0;
        if ($hourlyCount >= MAX_REQUESTS_PER_IP_PER_HOUR) {
            return [
                'allowed' => false,
                'reason' => 'hourly_limit',
                'message' => 'Saatlıq limit keçildi. Zəhmət olmasa bir saat sonra yenidən cəhd edin.',
                'reset_time' => strtotime('+1 hour', strtotime($currentHour . ':00:00'))
            ];
        }
        
        // Check daily limit
        $dailyCount = $usage[$ip]['daily'][$currentDate] ?? 0;
        if ($dailyCount >= MAX_REQUESTS_PER_IP_PER_DAY) {
            return [
                'allowed' => false,
                'reason' => 'daily_limit',
                'message' => 'Günlük limit keçildi. Sabah yenidən cəhd edin.',
                'reset_time' => strtotime('tomorrow')
            ];
        }
        
        return [
            'allowed' => true,
            'remaining_hourly' => MAX_REQUESTS_PER_IP_PER_HOUR - $hourlyCount,
            'remaining_daily' => MAX_REQUESTS_PER_IP_PER_DAY - $dailyCount
        ];
    }
    
    /**
     * Record a request
     */
    public function recordRequest($ip) {
        $usage = $this->getUsage();
        $currentDate = date('Y-m-d');
        $currentHour = date('Y-m-d-H');
        
        // Initialize user data if not exists
        if (!isset($usage[$ip])) {
            $usage[$ip] = [
                'hourly' => [],
                'daily' => []
            ];
        }
        
        // Increment counters
        $usage[$ip]['hourly'][$currentHour] = ($usage[$ip]['hourly'][$currentHour] ?? 0) + 1;
        $usage[$ip]['daily'][$currentDate] = ($usage[$ip]['daily'][$currentDate] ?? 0) + 1;
        
        // Add request timestamp for analytics
        if (!isset($usage[$ip]['requests'])) {
            $usage[$ip]['requests'] = [];
        }
        $usage[$ip]['requests'][] = [
            'timestamp' => time(),
            'date' => $currentDate,
            'hour' => $currentHour
        ];
        
        // Keep only last 100 requests per IP for memory efficiency
        if (count($usage[$ip]['requests']) > 100) {
            $usage[$ip]['requests'] = array_slice($usage[$ip]['requests'], -100);
        }
        
        $this->saveUsage($usage);
        
        return [
            'success' => true,
            'remaining_hourly' => MAX_REQUESTS_PER_IP_PER_HOUR - $usage[$ip]['hourly'][$currentHour],
            'remaining_daily' => MAX_REQUESTS_PER_IP_PER_DAY - $usage[$ip]['daily'][$currentDate]
        ];
    }
    
    /**
     * Get user statistics
     */
    public function getUserStats($ip) {
        $usage = $this->getUsage();
        $currentDate = date('Y-m-d');
        $currentHour = date('Y-m-d-H');
        
        if (!isset($usage[$ip])) {
            return [
                'hourly_used' => 0,
                'daily_used' => 0,
                'hourly_remaining' => MAX_REQUESTS_PER_IP_PER_HOUR,
                'daily_remaining' => MAX_REQUESTS_PER_IP_PER_DAY,
                'total_requests' => 0
            ];
        }
        
        $hourlyUsed = $usage[$ip]['hourly'][$currentHour] ?? 0;
        $dailyUsed = $usage[$ip]['daily'][$currentDate] ?? 0;
        $totalRequests = count($usage[$ip]['requests'] ?? []);
        
        return [
            'hourly_used' => $hourlyUsed,
            'daily_used' => $dailyUsed,
            'hourly_remaining' => MAX_REQUESTS_PER_IP_PER_HOUR - $hourlyUsed,
            'daily_remaining' => MAX_REQUESTS_PER_IP_PER_DAY - $dailyUsed,
            'total_requests' => $totalRequests
        ];
    }
    
    /**
     * Get system-wide statistics (for admin)
     */
    public function getSystemStats() {
        $usage = $this->getUsage();
        $currentDate = date('Y-m-d');
        $currentHour = date('Y-m-d-H');
        
        $stats = [
            'total_users' => count($usage),
            'active_users_today' => 0,
            'requests_today' => 0,
            'requests_this_hour' => 0,
            'top_users' => []
        ];
        
        foreach ($usage as $ip => $userData) {
            $dailyCount = $userData['daily'][$currentDate] ?? 0;
            $hourlyCount = $userData['hourly'][$currentHour] ?? 0;
            
            if ($dailyCount > 0) {
                $stats['active_users_today']++;
                $stats['requests_today'] += $dailyCount;
            }
            
            $stats['requests_this_hour'] += $hourlyCount;
            
            // Track top users (anonymized)
            $hashedIp = substr(md5($ip), 0, 8);
            $stats['top_users'][$hashedIp] = $dailyCount;
        }
        
        // Sort top users
        arsort($stats['top_users']);
        $stats['top_users'] = array_slice($stats['top_users'], 0, 10, true);
        
        return $stats;
    }
    
    /**
     * Initialize usage file if it doesn't exist
     */
    private function initializeUsageFile() {
        if (!file_exists($this->usageFile)) {
            $this->saveUsage([]);
        }
    }
    
    /**
     * Get usage data from file
     */
    private function getUsage() {
        if (!file_exists($this->usageFile)) {
            return [];
        }
        
        $content = file_get_contents($this->usageFile);
        if ($content === false) {
            logError('Failed to read usage file');
            return [];
        }
        
        $usage = json_decode($content, true);
        if ($usage === null) {
            logError('Failed to decode usage JSON');
            return [];
        }
        
        return $usage;
    }
    
    /**
     * Save usage data to file
     */
    private function saveUsage($usage) {
        $json = json_encode($usage, JSON_PRETTY_PRINT);
        if (file_put_contents($this->usageFile, $json, LOCK_EX) === false) {
            logError('Failed to save usage data');
            return false;
        }
        return true;
    }
    
    /**
     * Clean old data to prevent file from growing too large
     */
    private function cleanOldData($usage, $currentTime) {
        $oneDayAgo = $currentTime - 86400; // 24 hours
        $cleanupNeeded = false;
        
        foreach ($usage as $ip => &$userData) {
            // Clean old hourly data (older than 24 hours)
            foreach ($userData['hourly'] as $hour => $count) {
                $hourTimestamp = strtotime($hour . ':00:00');
                if ($hourTimestamp < $oneDayAgo) {
                    unset($userData['hourly'][$hour]);
                    $cleanupNeeded = true;
                }
            }
            
            // Clean old daily data (older than 30 days)
            $thirtyDaysAgo = $currentTime - (30 * 86400);
            foreach ($userData['daily'] as $date => $count) {
                $dateTimestamp = strtotime($date);
                if ($dateTimestamp < $thirtyDaysAgo) {
                    unset($userData['daily'][$date]);
                    $cleanupNeeded = true;
                }
            }
            
            // Clean old request logs (older than 7 days)
            if (isset($userData['requests'])) {
                $sevenDaysAgo = $currentTime - (7 * 86400);
                $userData['requests'] = array_filter($userData['requests'], function($request) use ($sevenDaysAgo) {
                    return $request['timestamp'] > $sevenDaysAgo;
                });
                if (count($userData['requests']) === 0) {
                    unset($userData['requests']);
                }
            }
        }
        
        // Save cleaned data if cleanup was needed
        if ($cleanupNeeded) {
            $this->saveUsage($usage);
        }
    }
    
    /**
     * Reset limits for a specific IP (admin function)
     */
    public function resetLimitsForIP($ip) {
        $usage = $this->getUsage();
        if (isset($usage[$ip])) {
            unset($usage[$ip]);
            $this->saveUsage($usage);
            return true;
        }
        return false;
    }
    
    /**
     * Check if IP appears to be abusing the system
     */
    public function detectAbuse($ip) {
        $usage = $this->getUsage();
        if (!isset($usage[$ip])) {
            return false;
        }
        
        $userData = $usage[$ip];
        $requests = $userData['requests'] ?? [];
        
        if (count($requests) < 10) {
            return false; // Not enough data
        }
        
        // Check for rapid-fire requests (more than 10 requests in 1 minute)
        $oneMinuteAgo = time() - 60;
        $recentRequests = array_filter($requests, function($request) use ($oneMinuteAgo) {
            return $request['timestamp'] > $oneMinuteAgo;
        });
        
        if (count($recentRequests) > 10) {
            logError("Potential abuse detected from IP: $ip - " . count($recentRequests) . " requests in 1 minute");
            return true;
        }
        
        return false;
    }
}
?>