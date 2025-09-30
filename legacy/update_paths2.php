<?php
require_once 'app/config/database.php';

try {
    $stmt = $pdo->prepare("UPDATE song_files SET file_path = REPLACE(file_path, 'public/assets/audio/', 'audio/')");
    $stmt->execute();
    echo "Caminhos atualizados para pasta raiz!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>