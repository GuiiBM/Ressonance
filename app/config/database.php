<?php
// Configuração do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ressonance_music');

// Inicializar database na primeira execução
require_once __DIR__ . '/init-database.php';
initializeDatabase();

// Conexão com o banco
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// Função para buscar álbuns recentes
function getRecentAlbums($pdo, $limit = 4) {
    $stmt = $pdo->prepare("
        SELECT a.*, ar.name as artist_name 
        FROM albums a 
        JOIN artists ar ON a.artist_id = ar.id 
        ORDER BY a.created_at DESC 
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para buscar playlists
function getPlaylists($pdo, $limit = 6) {
    $stmt = $pdo->prepare("SELECT * FROM playlists ORDER BY created_at DESC LIMIT :limit");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Função para buscar músicas mais tocadas
function getTopSongs($pdo, $limit = 10) {
    $stmt = $pdo->prepare("
        SELECT s.*, ar.name as artist_name, a.title as album_title 
        FROM songs s 
        JOIN artists ar ON s.artist_id = ar.id 
        LEFT JOIN albums a ON s.album_id = a.id 
        ORDER BY s.plays DESC 
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>