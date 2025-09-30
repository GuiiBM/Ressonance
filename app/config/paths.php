<?php
// Configurações de caminhos da aplicação

// Diretórios base
define('ROOT_PATH', dirname(dirname(dirname(__FILE__))));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('STORAGE_PATH', ROOT_PATH . '/storage');

// Caminhos de assets
define('CSS_PATH', '/public/assets/css');
define('JS_PATH', '/public/assets/js');
define('IMAGES_PATH', '/public/assets/images');

// Caminhos de upload
define('UPLOAD_PATH', STORAGE_PATH . '/uploads');
define('AUDIO_UPLOAD_PATH', UPLOAD_PATH . '/audio');

// Auto-detecção de URL
$scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
$detectedUrl = '/Ressonance/Ressonance';
if (preg_match('/(.+\/Ressonance\/Ressonance)/', $scriptPath, $matches)) {
    $detectedUrl = $matches[1];
}

// URLs públicas
define('BASE_URL', $detectedUrl);
define('ASSETS_URL', BASE_URL . '/public/assets');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMAGES_URL', ASSETS_URL . '/images');

// URLs relativas para páginas
define('PAGES_URL', BASE_URL . '/app/views/pages');
define('API_URL', BASE_URL . '/app/controllers/api');
?>