<?php
require_once 'app/config/database.php';

try {
    $stmt = $pdo->prepare("UPDATE song_files SET file_path = CONCAT('audio.php?f=', SUBSTRING_INDEX(file_path, '/', -1)) WHERE file_path LIKE 'audio/%'");
    $stmt->execute();
    echo "Caminhos atualizados para usar proxy PHP!";
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>