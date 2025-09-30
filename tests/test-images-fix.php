<?php
// Teste do sistema de correção de imagens
require_once 'app/views/components/image-helper.php';
require_once 'app/config/paths.php';

echo "<h1>🖼️ Teste do Sistema de Imagens</h1>";

// Testes de diferentes tipos de caminhos
$testImages = [
    'logo.png',
    '/old/path/image.jpg',
    'public/assets/images/logo.png',
    'http://example.com/image.jpg',
    '',
    null,
    'broken/path/image.png'
];

echo "<h2>Teste de Correção de URLs</h2>";
foreach ($testImages as $image) {
    $corrected = getImageUrl($image);
    echo "<p><strong>Original:</strong> " . ($image ?: 'null') . "<br>";
    echo "<strong>Corrigido:</strong> $corrected</p>";
}

echo "<h2>Teste de Tags IMG</h2>";
foreach ($testImages as $image) {
    echo "<div style='margin: 10px; padding: 10px; border: 1px solid #ccc;'>";
    echo "<p>Imagem: " . ($image ?: 'null') . "</p>";
    echo fixImageTag($image, 'Teste', 'test-image', 'width: 100px; height: 100px;');
    echo "</div>";
}

echo "<h2>Teste JavaScript</h2>";
echo "<script src='public/assets/js/config.js'></script>";
echo "<script src='public/assets/js/image-handler.js'></script>";
echo "<script>
setTimeout(() => {
    console.log('Testando ImageHandler...');
    console.log('getImageUrl(\"logo.png\"):', window.ImageHandler.getImageUrl('logo.png'));
    console.log('getImageUrl(\"/broken/path.jpg\"):', window.ImageHandler.getImageUrl('/broken/path.jpg'));
    console.log('getImageUrl(null):', window.ImageHandler.getImageUrl(null));
}, 1000);
</script>";

echo "<p><a href='index.php'>Voltar ao site</a></p>";
?>