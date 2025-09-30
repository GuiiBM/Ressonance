<?php
// Verificação de saúde do sistema
define('SKIP_PATH_CHECK', true);
require_once 'app/config/path-checker.php';

header('Content-Type: application/json');

$health = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

// Verificar arquivos essenciais
$issues = systemHealthCheck();
if (!empty($issues)) {
    $health['status'] = 'warning';
    $health['checks']['files'] = $issues;
} else {
    $health['checks']['files'] = 'OK';
}

// Verificar banco de dados
try {
    require_once 'app/config/database.php';
    $stmt = $pdo->query("SELECT COUNT(*) FROM artists");
    $health['checks']['database'] = 'OK';
} catch (Exception $e) {
    $health['status'] = 'error';
    $health['checks']['database'] = 'Erro: ' . $e->getMessage();
}

// Verificar pastas de áudio
$audioDir = __DIR__ . '/audio';
if (is_dir($audioDir) && is_writable($audioDir)) {
    $audioCount = count(glob($audioDir . '/*.{mp3,flac,wav,ogg,m4a}', GLOB_BRACE));
    $health['checks']['audio'] = "OK - $audioCount arquivos";
} else {
    $health['status'] = 'warning';
    $health['checks']['audio'] = 'Pasta não encontrada ou sem permissão';
}

// Verificar assets
$cssFile = __DIR__ . '/public/assets/css/styles.css';
if (file_exists($cssFile)) {
    $health['checks']['css'] = 'OK';
} else {
    $health['status'] = 'error';
    $health['checks']['css'] = 'CSS não encontrado';
}

echo json_encode($health, JSON_PRETTY_PRINT);
?>