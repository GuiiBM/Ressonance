<?php
// Verificador de saúde ultra-rápido
header('Content-Type: application/json');

$status = [
    'status' => 'ok',
    'timestamp' => time(),
    'checks' => []
];

// PHP
$status['checks']['php'] = [
    'status' => version_compare(PHP_VERSION, '7.4.0') >= 0 ? 'ok' : 'error',
    'version' => PHP_VERSION
];

// Extensões
$required = ['pdo', 'json', 'mbstring'];
foreach ($required as $ext) {
    $status['checks']['ext_' . $ext] = [
        'status' => extension_loaded($ext) ? 'ok' : 'error'
    ];
}

// Arquivos essenciais
$files = ['bootstrap.php', 'audio.php', 'image.php', 'app/views/pages/index.php'];
foreach ($files as $file) {
    $status['checks']['file_' . str_replace(['/', '.'], '_', $file)] = [
        'status' => file_exists($file) ? 'ok' : 'error'
    ];
}

// Banco de dados
try {
    if (defined('RESSONANCE_DB_HOST')) {
        $pdo = new PDO("mysql:host=" . RESSONANCE_DB_HOST . ";dbname=" . RESSONANCE_DB_NAME, RESSONANCE_DB_USER, RESSONANCE_DB_PASS);
    } else {
        $pdo = new PDO("mysql:host=localhost;dbname=ressonance_music", 'root', '');
    }
    $status['checks']['database'] = ['status' => 'ok'];
} catch (Exception $e) {
    $status['checks']['database'] = ['status' => 'error', 'message' => $e->getMessage()];
}

// Status geral
foreach ($status['checks'] as $check) {
    if ($check['status'] === 'error') {
        $status['status'] = 'error';
        break;
    }
}

echo json_encode($status, JSON_PRETTY_PRINT);
?>