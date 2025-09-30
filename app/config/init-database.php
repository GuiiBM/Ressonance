<?php
// Inicialização do banco de dados - executa apenas na primeira vez
require_once __DIR__ . '/database.php';

function initializeDatabase() {
    try {
        // Conectar sem especificar database para criar se não existir
        $pdo_init = new PDO("mysql:host=" . DB_HOST . ";charset=utf8", DB_USER, DB_PASS);
        $pdo_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Criar database se não existir
        $pdo_init->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8 COLLATE utf8_general_ci");
        $pdo_init->exec("USE " . DB_NAME);
        
        // Verificar se as tabelas já existem
        $tables = $pdo_init->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            return "Database já inicializado";
        }
        
        // Log da primeira execução
        error_log("Ressonance: Inicializando banco de dados pela primeira vez");
        
        // Criar tabelas
        $sql = "
        CREATE TABLE artists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            image VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE albums (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            artist_id INT,
            image VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
        );
        
        CREATE TABLE songs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            artist_id INT NOT NULL,
            album_id INT,
            duration INT,
            file_path VARCHAR(500),
            image VARCHAR(500),
            plays INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
            FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL
        );
        
        CREATE TABLE song_files (
            id INT AUTO_INCREMENT PRIMARY KEY,
            song_id INT NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_format VARCHAR(10) NOT NULL,
            file_size BIGINT,
            uploaded_by VARCHAR(100),
            is_verified BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE
        );
        
        CREATE TABLE playlists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            image VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";
        
        $pdo_init->exec($sql);
        
        return "Database inicializado com sucesso";
        
    } catch(PDOException $e) {
        return "Erro ao inicializar database: " . $e->getMessage();
    }
}

// Executar apenas se chamado diretamente
if (basename($_SERVER['PHP_SELF']) == 'init-database.php') {
    echo initializeDatabase();
}
?>