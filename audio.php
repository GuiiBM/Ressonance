<?php
$file = $_GET['f'] ?? '';
$audioPath = 'audio/' . basename($file);

if (!file_exists($audioPath)) {
    http_response_code(404);
    echo 'Arquivo não encontrado';
    exit;
}

$extension = strtolower(pathinfo($audioPath, PATHINFO_EXTENSION));
$allowedFormats = ['mp3', 'wav', 'ogg', 'flac', 'm4a'];

if (!in_array($extension, $allowedFormats)) {
    http_response_code(403);
    echo 'Formato não suportado';
    exit;
}

// Definir Content-Type baseado na extensão
$contentTypes = [
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'flac' => 'audio/flac',
    'm4a' => 'audio/mp4'
];

$fileSize = filesize($audioPath);
$contentType = $contentTypes[$extension] ?? 'audio/mpeg';

// Suporte a Range Requests para streaming
if (isset($_SERVER['HTTP_RANGE'])) {
    $range = $_SERVER['HTTP_RANGE'];
    $ranges = explode('=', $range);
    $offsets = explode('-', $ranges[1]);
    $offset = intval($offsets[0]);
    $length = intval($offsets[1]) ?: $fileSize - 1;
    
    if ($offset > $fileSize || $length > $fileSize) {
        http_response_code(416);
        exit;
    }
    
    $contentLength = $length - $offset + 1;
    
    header('HTTP/1.1 206 Partial Content');
    header('Accept-Ranges: bytes');
    header('Content-Range: bytes ' . $offset . '-' . $length . '/' . $fileSize);
    header('Content-Length: ' . $contentLength);
    header('Content-Type: ' . $contentType);
    
    $file = fopen($audioPath, 'rb');
    fseek($file, $offset);
    echo fread($file, $contentLength);
    fclose($file);
} else {
    header('Accept-Ranges: bytes');
    header('Content-Type: ' . $contentType);
    header('Content-Length: ' . $fileSize);
    readfile($audioPath);
}
?>