<?php
require_once 'app/config/database.php';

try {
    $stmt = $pdo->prepare("UPDATE song_files SET file_path = REPLACE(file_path, 'storage/uploads/audio/', 'public/assets/audio/')");
    $stmt->execute();
    echo "Caminhos atualizados com sucesso!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>