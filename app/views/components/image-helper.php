<?php
// Helper para correção automática de imagens

function getImageUrl($imagePath) {
    if (empty($imagePath) || $imagePath === 'NULL') {
        return 'https://via.placeholder.com/160x160/8a2be2/ffffff?text=%E2%99%AA';
    }
    
    if (strpos($imagePath, 'http') === 0 || strpos($imagePath, 'data:') === 0) {
        return $imagePath;
    }
    
    if (strpos($imagePath, 'image.php') !== false) {
        return $imagePath;
    }
    
    // Detectar BASE_URL
    $baseUrl = defined('BASE_URL') ? BASE_URL : 
               (defined('RESSONANCE_URL') ? RESSONANCE_URL : '/Ressonance/Ressonance');
    
    // Extrair apenas o nome do arquivo
    $fileName = basename($imagePath);
    return $baseUrl . '/image.php?f=' . urlencode($fileName);
}

function fixImageTag($imagePath, $alt = '', $class = '', $style = '') {
    $src = getImageUrl($imagePath);
    $attributes = [];
    
    if ($alt) $attributes[] = 'alt="' . htmlspecialchars($alt) . '"';
    if ($class) $attributes[] = 'class="' . htmlspecialchars($class) . '"';
    if ($style) $attributes[] = 'style="' . htmlspecialchars($style) . '"';
    
    $attributeString = implode(' ', $attributes);
    
    return '<img src="' . htmlspecialchars($src) . '" ' . $attributeString . ' onerror="this.src=\'https://via.placeholder.com/160x160/8a2be2/ffffff?text=%E2%99%AA\'">';
}
?>