# 🔒 Segurança - Ressonance

## 📋 Visão Geral

A segurança do Ressonance é implementada em múltiplas camadas, protegendo contra vulnerabilidades comuns e garantindo a integridade dos dados dos usuários. O sistema implementa as melhores práticas de segurança web.

## 🎯 Princípios de Segurança

### 🛡️ **Defesa em Profundidade**
- **Validação de Entrada** - Sanitização de todos os inputs
- **Autenticação Segura** - Senhas hasheadas e sessões seguras
- **Autorização** - Controle de acesso baseado em roles
- **Criptografia** - Dados sensíveis criptografados
- **Auditoria** - Logs de segurança detalhados

### 🔐 **Conformidade**
- **OWASP Top 10** - Proteção contra vulnerabilidades principais
- **GDPR** - Proteção de dados pessoais
- **ISO 27001** - Padrões de segurança da informação
- **PCI DSS** - Segurança para dados de pagamento (futuro)

## 🛡️ Proteções Implementadas

### **1. Injeção SQL (SQL Injection)**
```php
// ✅ CORRETO - Prepared Statements
$stmt = $pdo->prepare("SELECT * FROM songs WHERE artist_id = ? AND genre = ?");
$stmt->execute([$artistId, $genre]);

// ❌ INCORRETO - Concatenação direta
// $sql = "SELECT * FROM songs WHERE artist_id = $artistId"; // VULNERÁVEL!

// Classe de proteção
class SecureQuery {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function select($table, $conditions = [], $params = []) {
        $sql = "SELECT * FROM " . $this->sanitizeTableName($table);
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', array_map(function($col) {
                return $this->sanitizeColumnName($col) . " = ?";
            }, array_keys($conditions)));
            $params = array_values($conditions);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private function sanitizeTableName($table) {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    }
    
    private function sanitizeColumnName($column) {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $column);
    }
}
```

### **2. Cross-Site Scripting (XSS)**
```php
// Sanitização de saída
function sanitizeOutput($data) {
    if (is_array($data)) {
        return array_map('sanitizeOutput', $data);
    }
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Helper para HTML seguro
function safeHtml($content, $allowedTags = []) {
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', implode(',', $allowedTags));
    $purifier = new HTMLPurifier($config);
    return $purifier->purify($content);
}

// Template seguro
function renderTemplate($template, $data) {
    $sanitizedData = array_map('sanitizeOutput', $data);
    extract($sanitizedData);
    include $template;
}
```

### **3. Cross-Site Request Forgery (CSRF)**
```php
// Geração de token CSRF
class CSRFProtection {
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    public static function validateToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function getTokenField() {
        $token = self::generateToken();
        return "<input type='hidden' name='csrf_token' value='$token'>";
    }
}

// Uso em formulários
?>
<form method="POST" action="/upload">
    <?= CSRFProtection::getTokenField() ?>
    <input type="file" name="audio" required>
    <button type="submit">Upload</button>
</form>

<?php
// Validação no servidor
if ($_POST) {
    if (!CSRFProtection::validateToken($_POST['csrf_token'] ?? '')) {
        die('Token CSRF inválido');
    }
    // Processar formulário...
}
```

### **4. Autenticação Segura**
```php
class SecureAuth {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Hash seguro de senha
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_ARGON2ID, [
            'memory_cost' => 65536, // 64 MB
            'time_cost' => 4,       // 4 iterations
            'threads' => 3          // 3 threads
        ]);
    }
    
    // Verificação de senha
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    // Login seguro
    public function login($email, $password) {
        // Rate limiting
        if ($this->isRateLimited($email)) {
            throw new Exception('Muitas tentativas. Tente novamente em 15 minutos.');
        }
        
        $stmt = $this->pdo->prepare("SELECT id, password_hash, is_active FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user || !$this->verifyPassword($password, $user['password_hash'])) {
            $this->logFailedAttempt($email);
            throw new Exception('Credenciais inválidas');
        }
        
        if (!$user['is_active']) {
            throw new Exception('Conta desativada');
        }
        
        $this->createSession($user['id']);
        $this->logSuccessfulLogin($user['id']);
        
        return $user;
    }
    
    // Rate limiting
    private function isRateLimited($email) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM login_attempts 
            WHERE email = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() >= 5;
    }
}
```

### **5. Controle de Sessão**
```php
class SecureSession {
    public static function start() {
        // Configurações seguras de sessão
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', 1);
        
        session_start();
        
        // Regenerar ID da sessão periodicamente
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutos
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    public static function destroy() {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    public static function isValid() {
        return isset($_SESSION['user_id']) && 
               isset($_SESSION['last_activity']) &&
               (time() - $_SESSION['last_activity']) < 3600; // 1 hora
    }
}
```

## 🔐 Validação e Sanitização

### **Validação de Entrada**
```php
class InputValidator {
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public static function validatePassword($password) {
        // Mínimo 8 caracteres, pelo menos 1 maiúscula, 1 minúscula, 1 número
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d@$!%*?&]{8,}$/', $password);
    }
    
    public static function validateFilename($filename) {
        // Apenas caracteres alfanuméricos, pontos, hífens e underscores
        return preg_match('/^[a-zA-Z0-9._-]+$/', $filename);
    }
    
    public static function validateAudioFile($file) {
        $errors = [];
        
        // Verificar tipo MIME
        $allowedTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/flac'];
        if (!in_array($file['type'], $allowedTypes)) {
            $errors[] = 'Tipo de arquivo não permitido';
        }
        
        // Verificar extensão
        $allowedExtensions = ['mp3', 'wav', 'ogg', 'flac'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            $errors[] = 'Extensão não permitida';
        }
        
        // Verificar tamanho
        $maxSize = 50 * 1024 * 1024; // 50MB
        if ($file['size'] > $maxSize) {
            $errors[] = 'Arquivo muito grande';
        }
        
        // Verificar se é realmente um arquivo de áudio
        if (!$this->isValidAudioFile($file['tmp_name'])) {
            $errors[] = 'Arquivo corrompido ou inválido';
        }
        
        return $errors;
    }
    
    private static function isValidAudioFile($filepath) {
        // Verificar assinatura do arquivo
        $handle = fopen($filepath, 'rb');
        $header = fread($handle, 4);
        fclose($handle);
        
        // Assinaturas conhecidas
        $signatures = [
            'ID3' => 'mp3',
            'RIFF' => 'wav',
            'OggS' => 'ogg',
            'fLaC' => 'flac'
        ];
        
        foreach ($signatures as $sig => $format) {
            if (strpos($header, $sig) === 0) {
                return true;
            }
        }
        
        return false;
    }
}
```

### **Sanitização de Dados**
```php
class DataSanitizer {
    public static function sanitizeString($input, $maxLength = 255) {
        $sanitized = trim($input);
        $sanitized = strip_tags($sanitized);
        $sanitized = htmlspecialchars($sanitized, ENT_QUOTES, 'UTF-8');
        return substr($sanitized, 0, $maxLength);
    }
    
    public static function sanitizeFilename($filename) {
        // Remover caracteres perigosos
        $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Remover múltiplos underscores
        $sanitized = preg_replace('/_+/', '_', $sanitized);
        
        // Remover pontos no início
        $sanitized = ltrim($sanitized, '.');
        
        return $sanitized;
    }
    
    public static function sanitizePath($path) {
        // Remover tentativas de path traversal
        $sanitized = str_replace(['../', '..\\', './'], '', $path);
        
        // Normalizar separadores
        $sanitized = str_replace('\\', '/', $sanitized);
        
        // Remover múltiplas barras
        $sanitized = preg_replace('/\/+/', '/', $sanitized);
        
        return trim($sanitized, '/');
    }
}
```

## 🔍 Auditoria e Logs

### **Sistema de Logs**
```php
class SecurityLogger {
    private $logFile;
    
    public function __construct($logFile = 'security.log') {
        $this->logFile = __DIR__ . '/../../storage/logs/' . $logFile;
    }
    
    public function logSecurityEvent($event, $details = []) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id' => $_SESSION['user_id'] ?? null,
            'details' => $details
        ];
        
        $logLine = json_encode($logEntry) . "\n";
        file_put_contents($this->logFile, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    public function logFailedLogin($email, $reason = 'invalid_credentials') {
        $this->logSecurityEvent('failed_login', [
            'email' => $email,
            'reason' => $reason
        ]);
    }
    
    public function logSuccessfulLogin($userId) {
        $this->logSecurityEvent('successful_login', [
            'user_id' => $userId
        ]);
    }
    
    public function logFileUpload($filename, $size, $type) {
        $this->logSecurityEvent('file_upload', [
            'filename' => $filename,
            'size' => $size,
            'type' => $type
        ]);
    }
    
    public function logSuspiciousActivity($activity, $details = []) {
        $this->logSecurityEvent('suspicious_activity', [
            'activity' => $activity,
            'details' => $details
        ]);
        
        // Alertar administradores em casos críticos
        if (in_array($activity, ['sql_injection_attempt', 'xss_attempt', 'path_traversal'])) {
            $this->alertAdministrators($activity, $details);
        }
    }
    
    private function alertAdministrators($activity, $details) {
        // Implementar notificação para administradores
        // Email, Slack, etc.
    }
}
```

### **Monitoramento de Intrusão**
```php
class IntrusionDetection {
    private $logger;
    
    public function __construct() {
        $this->logger = new SecurityLogger();
    }
    
    public function detectSQLInjection($input) {
        $patterns = [
            '/(\bunion\b.*\bselect\b)/i',
            '/(\bselect\b.*\bfrom\b)/i',
            '/(\binsert\b.*\binto\b)/i',
            '/(\bdelete\b.*\bfrom\b)/i',
            '/(\bdrop\b.*\btable\b)/i',
            '/(\bor\b.*=.*)/i',
            '/(\'.*or.*\'.*=.*\')/i'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                $this->logger->logSuspiciousActivity('sql_injection_attempt', [
                    'input' => $input,
                    'pattern' => $pattern
                ]);
                return true;
            }
        }
        
        return false;
    }
    
    public function detectXSS($input) {
        $patterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/javascript:/i',
            '/on\w+\s*=/i',
            '/<iframe[^>]*>.*?<\/iframe>/is'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                $this->logger->logSuspiciousActivity('xss_attempt', [
                    'input' => $input,
                    'pattern' => $pattern
                ]);
                return true;
            }
        }
        
        return false;
    }
    
    public function detectPathTraversal($input) {
        $patterns = [
            '/\.\.\//',
            '/\.\.\\\\/',
            '/\.\.\%2f/',
            '/\.\.\%5c/'
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                $this->logger->logSuspiciousActivity('path_traversal', [
                    'input' => $input,
                    'pattern' => $pattern
                ]);
                return true;
            }
        }
        
        return false;
    }
}
```

## 🛡️ Headers de Segurança

### **Headers HTTP Seguros**
```php
class SecurityHeaders {
    public static function setSecureHeaders() {
        // Prevenir XSS
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevenir MIME sniffing
        header('X-Content-Type-Options: nosniff');
        
        // Prevenir clickjacking
        header('X-Frame-Options: DENY');
        
        // HSTS (HTTPS obrigatório)
        if (isset($_SERVER['HTTPS'])) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
        
        // Content Security Policy
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' cdnjs.cloudflare.com; " .
               "style-src 'self' 'unsafe-inline' fonts.googleapis.com cdnjs.cloudflare.com; " .
               "font-src 'self' fonts.gstatic.com; " .
               "img-src 'self' data: via.placeholder.com; " .
               "media-src 'self'; " .
               "connect-src 'self'";
        
        header("Content-Security-Policy: $csp");
        
        // Referrer Policy
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Feature Policy
        header("Feature-Policy: camera 'none'; microphone 'none'; geolocation 'none'");
    }
}

// Aplicar headers em todas as páginas
SecurityHeaders::setSecureHeaders();
```

## 🔐 Criptografia

### **Criptografia de Dados Sensíveis**
```php
class DataEncryption {
    private $key;
    
    public function __construct() {
        $this->key = $this->getEncryptionKey();
    }
    
    private function getEncryptionKey() {
        $keyFile = __DIR__ . '/../../storage/keys/encryption.key';
        
        if (!file_exists($keyFile)) {
            $key = random_bytes(32); // 256 bits
            file_put_contents($keyFile, base64_encode($key));
            chmod($keyFile, 0600);
            return $key;
        }
        
        return base64_decode(file_get_contents($keyFile));
    }
    
    public function encrypt($data) {
        $iv = random_bytes(16); // 128 bits
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $this->key, 0, $iv);
        return base64_encode($iv . $encrypted);
    }
    
    public function decrypt($encryptedData) {
        $data = base64_decode($encryptedData);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $this->key, 0, $iv);
    }
}

// Uso para dados sensíveis
$encryption = new DataEncryption();
$encryptedEmail = $encryption->encrypt($userEmail);
$decryptedEmail = $encryption->decrypt($encryptedEmail);
```

## 🔧 Configurações de Segurança

### **Configuração do PHP**
```ini
; php.ini - Configurações de segurança

; Desabilitar funções perigosas
disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source

; Ocultar versão do PHP
expose_php = Off

; Limitar uploads
file_uploads = On
upload_max_filesize = 50M
post_max_size = 50M
max_file_uploads = 5

; Configurações de sessão
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict
session.use_strict_mode = 1
session.gc_maxlifetime = 3600

; Logs de erro
log_errors = On
error_log = /var/log/php_errors.log
display_errors = Off
```

### **Configuração do Apache (.htaccess)**
```apache
# .htaccess - Configurações de segurança

# Prevenir acesso a arquivos sensíveis
<Files ~ "^\.">
    Order allow,deny
    Deny from all
</Files>

<Files ~ "\.(log|sql|conf|key)$">
    Order allow,deny
    Deny from all
</Files>

# Headers de segurança
Header always set X-Frame-Options DENY
Header always set X-Content-Type-Options nosniff
Header always set X-XSS-Protection "1; mode=block"
Header always set Referrer-Policy "strict-origin-when-cross-origin"

# HTTPS redirect
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Prevenir hotlinking
RewriteCond %{HTTP_REFERER} !^$
RewriteCond %{HTTP_REFERER} !^https?://(www\.)?yourdomain\.com [NC]
RewriteRule \.(jpg|jpeg|png|gif|mp3|wav|flac)$ - [F]
```

## 🚨 Resposta a Incidentes

### **Plano de Resposta**
```php
class IncidentResponse {
    private $logger;
    
    public function __construct() {
        $this->logger = new SecurityLogger();
    }
    
    public function handleSecurityIncident($type, $severity, $details) {
        // Log do incidente
        $this->logger->logSecurityEvent('security_incident', [
            'type' => $type,
            'severity' => $severity,
            'details' => $details
        ]);
        
        // Ações baseadas na severidade
        switch ($severity) {
            case 'critical':
                $this->handleCriticalIncident($type, $details);
                break;
            case 'high':
                $this->handleHighIncident($type, $details);
                break;
            case 'medium':
                $this->handleMediumIncident($type, $details);
                break;
        }
    }
    
    private function handleCriticalIncident($type, $details) {
        // Bloquear IP imediatamente
        $this->blockIP($details['ip'] ?? $_SERVER['REMOTE_ADDR']);
        
        // Notificar administradores
        $this->notifyAdministrators('CRITICAL', $type, $details);
        
        // Invalidar todas as sessões se necessário
        if (in_array($type, ['data_breach', 'admin_compromise'])) {
            $this->invalidateAllSessions();
        }
    }
    
    private function blockIP($ip) {
        // Adicionar IP à lista de bloqueio
        $blockedIPs = file_get_contents('blocked_ips.txt');
        if (strpos($blockedIPs, $ip) === false) {
            file_put_contents('blocked_ips.txt', $ip . "\n", FILE_APPEND);
        }
    }
}
```

## 🔮 Funcionalidades Futuras

- **Two-Factor Authentication (2FA)** - Autenticação de dois fatores
- **OAuth Integration** - Login social seguro
- **API Rate Limiting** - Limitação de requisições
- **Web Application Firewall** - Firewall de aplicação
- **Automated Vulnerability Scanning** - Varredura automática
- **Blockchain Audit Trail** - Trilha de auditoria imutável

---

**📚 Próximos Passos:**
- [Configuração](../03-configuracao/) - Setup de segurança
- [Banco de Dados](../06-banco-dados/) - Segurança de dados
- [Manutenção](../09-manutencao/) - Monitoramento contínuo