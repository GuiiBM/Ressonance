<?php
// Arquivo de teste para verificar se os arquivos de áudio estão sendo servidos corretamente
require_once 'app/config/database.php';
require_once 'app/views/components/database-queries.php';

$db = new DatabaseQueries($pdo);
$songs = $db->getInitialSongs(3);

echo "<h2>Teste de Reprodução de Áudio</h2>";
echo "<p>Verificando se os arquivos de áudio estão acessíveis:</p>";

foreach ($songs as $song) {
    echo "<div style='margin: 20px; padding: 15px; border: 1px solid #ccc;'>";
    echo "<h3>" . htmlspecialchars($song['title']) . " - " . htmlspecialchars($song['artist_name']) . "</h3>";
    
    if ($song['audio_files']) {
        $audioFiles = [];
        foreach (explode('|', $song['audio_files']) as $fileData) {
            if (strpos($fileData, ':') !== false) {
                list($format, $path) = explode(':', $fileData, 2);
                $audioFiles[] = ['format' => $format, 'path' => $path];
            }
        }
        
        foreach ($audioFiles as $file) {
            $audioUrl = '/Ressonance/audio.php?f=' . urlencode($file['path']);
            echo "<p><strong>Formato:</strong> " . strtoupper($file['format']) . "</p>";
            echo "<p><strong>Arquivo:</strong> " . htmlspecialchars($file['path']) . "</p>";
            echo "<p><strong>URL:</strong> <a href='" . $audioUrl . "' target='_blank'>" . $audioUrl . "</a></p>";
            
            // Verificar se o arquivo existe
            $filePath = 'audio/' . basename($file['path']);
            if (file_exists($filePath)) {
                echo "<p style='color: green;'>✓ Arquivo existe no servidor</p>";
                echo "<audio controls style='width: 100%;'>";
                echo "<source src='" . $audioUrl . "' type='audio/mpeg'>";
                echo "Seu navegador não suporta o elemento de áudio.";
                echo "</audio>";
            } else {
                echo "<p style='color: red;'>✗ Arquivo não encontrado: " . $filePath . "</p>";
            }
            echo "<hr>";
        }
    } else {
        echo "<p style='color: orange;'>Nenhum arquivo de áudio encontrado</p>";
    }
    echo "</div>";
}
?>