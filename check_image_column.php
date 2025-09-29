<?php
require_once 'app/config/database.php';

try {
    $stmt = $pdo->query("DESCRIBE songs");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Colunas da tabela songs:</h3>";
    foreach ($columns as $column) {
        echo $column['Field'] . " - " . $column['Type'] . "<br>";
    }
    
    // Verificar se existe alguma música com imagem
    $stmt = $pdo->query("SELECT id, title, image FROM songs WHERE image IS NOT NULL LIMIT 5");
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h3>Músicas com imagem:</h3>";
    foreach ($songs as $song) {
        echo "ID: " . $song['id'] . " - " . $song['title'] . " - Imagem: " . $song['image'] . "<br>";
    }
    
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
?>