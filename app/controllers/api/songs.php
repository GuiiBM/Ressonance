<?php
define('SECURE_ACCESS', true);
require_once '../../config/database.php';
require_once 'security.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
setSecurityHeaders();
RateLimit::check($_SERVER['REMOTE_ADDR']);

header('Content-Type: application/json');

$action = sanitizeInput($_GET['action'] ?? '');

try {
    switch ($action) {
        case 'get_album_songs':
            $albumId = validateId($_GET['album_id'] ?? 0);
            if (!$albumId) {
                http_response_code(400);
                echo json_encode(['error' => 'ID do álbum inválido']);
                break;
            }
            
            // Buscar informações do álbum
            $stmt = $pdo->prepare("
                SELECT al.*, ar.name as artist_name
                FROM albums al
                JOIN artists ar ON al.artist_id = ar.id
                WHERE al.id = ?
            ");
            $stmt->execute([$albumId]);
            $album = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$album) {
                echo json_encode(['success' => false, 'error' => 'Álbum não encontrado']);
                break;
            }
            
            // Buscar músicas do álbum
            $stmt = $pdo->prepare("
                SELECT s.*, GROUP_CONCAT(CONCAT(sf.file_format, ':', SUBSTRING_INDEX(sf.file_path, '/', -1)) SEPARATOR '|') as audio_files
                FROM songs s
                LEFT JOIN song_files sf ON s.id = sf.song_id AND sf.is_verified = TRUE
                WHERE s.album_id = ?
                GROUP BY s.id
                ORDER BY s.title ASC
            ");
            $stmt->execute([$albumId]);
            $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($songs as &$song) {
                $audioFiles = [];
                if ($song['audio_files']) {
                    foreach (explode('|', $song['audio_files']) as $fileData) {
                        if (strpos($fileData, ':') !== false) {
                            list($format, $path) = explode(':', $fileData, 2);
                            $audioFiles[] = ['format' => $format, 'path' => $path];
                        }
                    }
                }
                $song['audio_files'] = $audioFiles;
            }
            
            echo json_encode(['success' => true, 'album' => $album, 'songs' => $songs]);
            break;
            
        case 'get_songs':
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = min(50, max(1, intval($_GET['limit'] ?? 12)));
            $offset = intval($_GET['offset'] ?? 0);
            
            $stmt = $pdo->prepare("
                SELECT s.*, ar.name as artist_name,
                       GROUP_CONCAT(DISTINCT CONCAT(sf.file_format, ':', SUBSTRING_INDEX(sf.file_path, '/', -1)) SEPARATOR '|') as audio_files
                FROM songs s 
                JOIN artists ar ON s.artist_id = ar.id 
                LEFT JOIN song_files sf ON s.id = sf.song_id AND sf.is_verified = TRUE
                WHERE sf.file_path IS NOT NULL
                GROUP BY s.id
                ORDER BY s.plays DESC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($songs as &$song) {
                $audioFiles = [];
                if ($song['audio_files']) {
                    foreach (explode('|', $song['audio_files']) as $fileData) {
                        if (strpos($fileData, ':') !== false) {
                            list($format, $path) = explode(':', $fileData, 2);
                            $audioFiles[] = ['format' => $format, 'path' => $path];
                        }
                    }
                }
                $song['audio_files'] = $audioFiles;
            }
            
            // Debug: verificar se image está presente
            error_log('Songs data: ' . print_r($songs[0] ?? [], true));
            
            echo json_encode(['success' => true, 'data' => $songs]);
            break;
            
        case 'get_artists':
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = min(50, max(1, intval($_GET['limit'] ?? 10)));
            $offset = ($page - 1) * $limit;
            
            $stmt = $pdo->prepare("
                SELECT * FROM artists 
                ORDER BY name ASC 
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'data' => $artists]);
            break;
            
        case 'get_audio_files':
            $songId = validateId($_GET['song_id'] ?? 0);
            if (!$songId) {
                echo json_encode([]);
                break;
            }
            
            $stmt = $pdo->prepare("
                SELECT id, file_path, file_format, file_size, uploaded_by, is_verified
                FROM song_files 
                WHERE song_id = ? AND is_verified = TRUE
                ORDER BY file_format
            ");
            $stmt->execute([$songId]);
            $files = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($files);
            break;
            
        case 'get_all_songs':
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = min(50, max(1, intval($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;
            
            $stmt = $pdo->prepare("
                SELECT s.*, ar.name as artist_name, al.title as album_title,
                       GROUP_CONCAT(DISTINCT CONCAT(sf.file_format, ':', SUBSTRING_INDEX(sf.file_path, '/', -1)) SEPARATOR '|') as audio_files
                FROM songs s 
                JOIN artists ar ON s.artist_id = ar.id 
                LEFT JOIN albums al ON s.album_id = al.id
                LEFT JOIN song_files sf ON s.id = sf.song_id AND sf.is_verified = TRUE
                WHERE sf.file_path IS NOT NULL
                GROUP BY s.id
                ORDER BY s.title
                LIMIT ? OFFSET ?
            ");
            $stmt->execute([$limit, $offset]);
            $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($songs as &$song) {
                $audioFiles = [];
                if ($song['audio_files']) {
                    foreach (explode('|', $song['audio_files']) as $fileData) {
                        if (strpos($fileData, ':') !== false) {
                            list($format, $path) = explode(':', $fileData, 2);
                            $audioFiles[] = ['format' => $format, 'path' => $path];
                        }
                    }
                }
                $song['audio_files'] = $audioFiles;
            }
            
            echo json_encode(['success' => true, 'data' => $songs]);
            break;
            
        case 'get_available_songs':
            $albumId = validateId($_GET['album_id'] ?? 0);
            
            $stmt = $pdo->prepare("
                SELECT s.id, s.title, ar.name as artist_name
                FROM songs s 
                JOIN artists ar ON s.artist_id = ar.id 
                WHERE s.album_id IS NULL OR s.album_id != ?
                ORDER BY s.title
            ");
            $stmt->execute([$albumId]);
            $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode(['success' => true, 'songs' => $songs]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Ação inválida']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro interno']);
}
?>