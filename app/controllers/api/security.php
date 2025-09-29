<?php
if (!defined('SECURE_ACCESS')) {
    http_response_code(403);
    die('Acesso negado');
}

class RateLimit {
    private static $requests = [];
    
    public static function check($ip, $limit = 60, $window = 60) {
        $now = time();
        $key = md5($ip);
        
        if (!isset(self::$requests[$key])) {
            self::$requests[$key] = [];
        }
        
        self::$requests[$key] = array_filter(self::$requests[$key], function($time) use ($now, $window) {
            return ($now - $time) < $window;
        });
        
        if (count(self::$requests[$key]) >= $limit) {
            http_response_code(429);
            die(json_encode(['error' => 'Muitas requisições']));
        }
        
        self::$requests[$key][] = $now;
    }
}

function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function validateId($id) {
    return filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function setSecurityHeaders() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
?>