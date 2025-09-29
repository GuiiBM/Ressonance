<?php
// Configurações comuns da aplicação

// Configurações de site
define('SITE_NAME', 'RESSONANCE');
define('SITE_LOGO', IMAGES_URL . '/logo.png');

// Configurações de upload
define('UPLOAD_DIR', 'storage/uploads/audio/');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_AUDIO_FORMATS', ['mp3', 'wav', 'ogg', 'flac', 'm4a', 'aac']);

// Configurações de paginação
define('SONGS_PER_PAGE', 20);
define('ALBUMS_PER_PAGE', 12);
define('ARTISTS_PER_PAGE', 16);

// Configurações de segurança
define('RATE_LIMIT_REQUESTS', 100);
define('RATE_LIMIT_WINDOW', 3600); // 1 hora

// Funções utilitárias comuns
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

function formatDuration($seconds) {
    if ($seconds < 3600) {
        return gmdate("i:s", $seconds);
    } else {
        return gmdate("H:i:s", $seconds);
    }
}

function sanitizeFilename($filename) {
    return preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
}

function generateUniqueFilename($originalName, $prefix = '') {
    $extension = pathinfo($originalName, PATHINFO_EXTENSION);
    $basename = pathinfo($originalName, PATHINFO_FILENAME);
    return $prefix . time() . '_' . sanitizeFilename($basename) . '.' . $extension;
}
?>