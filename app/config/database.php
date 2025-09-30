<?php
// Auto-configuração do banco
$hosts = ['localhost', '127.0.0.1', 'mysql'];
$users = ['root', 'mysql'];
$passes = ['', 'root', 'password'];

$connected = false;
foreach ($hosts as $host) {
    foreach ($users as $user) {
        foreach ($passes as $pass) {
            try {
                $pdo = new PDO("mysql:host=$host", $user, $pass);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS ressonance_music");
                $pdo->exec("USE ressonance_music");
                
                // Criar tabelas se necessário
                $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
                if (count($tables) < 3) {
                    require_once __DIR__ . '/init-database.php';
                    initializeDatabase();
                }
                
                define('DB_HOST', $host);
                define('DB_USER', $user);
                define('DB_PASS', $pass);
                define('DB_NAME', 'ressonance_music');
                
                $pdo = new PDO("mysql:host=$host;dbname=ressonance_music;charset=utf8", $user, $pass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $connected = true;
                break 3;
            } catch (Exception $e) {
                continue;
            }
        }
    }
}

if (!$connected) {
    die('Erro: MySQL não encontrado. Verifique se está rodando.');
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