<?php
$file = $_GET['f'] ?? '';
$imagePath = '';

// Verificar em diferentes locais
$possiblePaths = [
    'public/assets/images/' . basename($file),
    'storage/uploads/images/' . basename($file),
    'images/' . basename($file)
];

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        $imagePath = $path;
        break;
    }
}

if (!$imagePath || !file_exists($imagePath)) {
    // Retornar imagem placeholder em vez de 404
    header('Location: https://via.placeholder.com/160x160/8a2be2/ffffff?text=%E2%99%AA');
    exit;
}

$extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
$allowedFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if (!in_array($extension, $allowedFormats)) {
    http_response_code(403);
    exit;
}

$contentTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif',
    'webp' => 'image/webp'
];

$contentType = $contentTypes[$extension] ?? 'image/jpeg';

header('Content-Type: ' . $contentType);
header('Content-Length: ' . filesize($imagePath));
header('Cache-Control: public, max-age=31536000');
readfile($imagePath);
?>