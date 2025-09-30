<?php
// Inicialização segura e universal
try {
    // 1. Garantir bootstrap
    if (!class_exists('UniversalBootstrap')) {
        require_once '../../../bootstrap.php';
    }
    
    // 2. Inicializar se necessário
    if (!file_exists('../../../.ressonance_ready')) {
        UniversalBootstrap::init();
    }
    
    // 3. Configurar constantes básicas
    if (!defined('BASE_URL')) {
        define('BASE_URL', defined('RESSONANCE_URL') ? RESSONANCE_URL : '/Ressonance/Ressonance');
    }
    
    // 4. Conectar banco
    if (!isset($pdo)) {
        if (defined('RESSONANCE_DB_HOST')) {
            $pdo = new PDO(
                "mysql:host=" . RESSONANCE_DB_HOST . ";dbname=" . RESSONANCE_DB_NAME . ";charset=utf8mb4",
                RESSONANCE_DB_USER,
                RESSONANCE_DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } else {
            $pdo = new PDO(
                "mysql:host=localhost;dbname=ressonance_music;charset=utf8mb4",
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
    }
    
    // 5. Carregar helpers
    require_once 'config-common.php';
    require_once 'image-helper.php';
    require_once 'database-queries.php';
    
    // 6. Iniciar sessão
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    // 7. Instanciar queries
    $db = new DatabaseQueries($pdo);
    
} catch (Exception $e) {
    // Fallback de emergência
    error_log('Erro na inicialização: ' . $e->getMessage());
    
    // Tentar conexão básica
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=ressonance_music;charset=utf8mb4", 'root', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        if (!defined('BASE_URL')) define('BASE_URL', '/Ressonance/Ressonance');
        
        require_once 'config-common.php';
        require_once 'image-helper.php';
        require_once 'database-queries.php';
        
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        $db = new DatabaseQueries($pdo);
        
    } catch (Exception $e2) {
        die('Sistema indisponível. Verifique se MySQL está rodando.');
    }
}
?>