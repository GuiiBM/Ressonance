<?php
require_once 'app/config/database.php';

try {
    $sql = "ALTER TABLE songs ADD COLUMN image VARCHAR(500) NULL";
    $pdo->exec($sql);
    echo "✅ Coluna 'image' adicionada com sucesso na tabela 'songs'!";
} catch(PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "ℹ️ Coluna 'image' já existe na tabela 'songs'.";
    } else {
        echo "❌ Erro: " . $e->getMessage();
    }
}
?>