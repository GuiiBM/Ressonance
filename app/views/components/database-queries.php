<?php
// Queries centralizadas do banco de dados

class DatabaseQueries {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Queries para músicas
    public function getInitialSongs($limit = 6) {
        $stmt = $this->pdo->prepare("
            SELECT s.*, ar.name as artist_name,
                   GROUP_CONCAT(DISTINCT CONCAT(sf.file_format, ':', SUBSTRING_INDEX(sf.file_path, '/', -1)) SEPARATOR '|') as audio_files
            FROM songs s 
            JOIN artists ar ON s.artist_id = ar.id 
            LEFT JOIN song_files sf ON s.id = sf.song_id AND sf.is_verified = TRUE
            WHERE sf.file_path IS NOT NULL
            GROUP BY s.id
            ORDER BY s.plays DESC 
            LIMIT " . intval($limit) . "
        ");
        $stmt->execute();
        $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug: verificar se image está presente
        error_log('Initial songs data: ' . print_r($songs[0] ?? [], true));
        
        return $songs;
    }
    
    public function getAllSongs() {
        return $this->pdo->query("
            SELECT s.*, ar.name as artist_name, al.title as album_title,
                   GROUP_CONCAT(DISTINCT CONCAT(sf.file_format, ':', SUBSTRING_INDEX(sf.file_path, '/', -1)) SEPARATOR '|') as audio_files
            FROM songs s 
            JOIN artists ar ON s.artist_id = ar.id 
            LEFT JOIN albums al ON s.album_id = al.id
            LEFT JOIN song_files sf ON s.id = sf.song_id AND sf.is_verified = TRUE
            WHERE sf.file_path IS NOT NULL
            GROUP BY s.id
            ORDER BY s.title
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Queries para álbuns
    public function getRecentAlbums($limit = 8) {
        $stmt = $this->pdo->prepare("
            SELECT al.*, ar.name as artist_name 
            FROM albums al 
            JOIN artists ar ON al.artist_id = ar.id 
            ORDER BY al.created_at DESC 
            LIMIT " . intval($limit) . "
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllAlbums() {
        return $this->pdo->query("
            SELECT al.*, ar.name as artist_name,
                   COUNT(s.id) as song_count
            FROM albums al 
            JOIN artists ar ON al.artist_id = ar.id 
            LEFT JOIN songs s ON s.album_id = al.id
            GROUP BY al.id
            ORDER BY al.title ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Queries para artistas
    public function getAllArtists() {
        return $this->pdo->query("
            SELECT * FROM artists ORDER BY name
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Queries para admin
    public function addArtist($name, $image) {
        $stmt = $this->pdo->prepare("INSERT INTO artists (name, image) VALUES (?, ?)");
        return $stmt->execute([$name, $image]);
    }
    
    public function updateArtist($id, $name, $image) {
        $stmt = $this->pdo->prepare("UPDATE artists SET name = ?, image = ? WHERE id = ?");
        return $stmt->execute([$name, $image, $id]);
    }
    
    public function deleteArtist($id) {
        $stmt = $this->pdo->prepare("DELETE FROM artists WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function addSong($title, $artistId, $albumId, $duration, $filePath) {
        $stmt = $this->pdo->prepare("INSERT INTO songs (title, artist_id, album_id, duration, file_path) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$title, $artistId, $albumId, $duration, $filePath]);
    }
    
    public function updateSong($id, $title, $artistId, $albumId, $duration) {
        $stmt = $this->pdo->prepare("UPDATE songs SET title = ?, artist_id = ?, album_id = ?, duration = ? WHERE id = ?");
        return $stmt->execute([$title, $artistId, $albumId, $duration, $id]);
    }
    
    public function deleteSong($id) {
        $stmt = $this->pdo->prepare("DELETE FROM songs WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function addAlbum($title, $artistId, $image) {
        $stmt = $this->pdo->prepare("INSERT INTO albums (title, artist_id, image) VALUES (?, ?, ?)");
        return $stmt->execute([$title, $artistId, $image]);
    }
    
    public function updateAlbum($id, $title, $artistId, $image) {
        $stmt = $this->pdo->prepare("UPDATE albums SET title = ?, artist_id = ?, image = ? WHERE id = ?");
        return $stmt->execute([$title, $artistId, $image, $id]);
    }
    
    public function deleteAlbum($id) {
        $stmt = $this->pdo->prepare("DELETE FROM albums WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    public function addSongFile($songId, $filePath, $format, $fileSize, $uploadedBy) {
        $stmt = $this->pdo->prepare("INSERT INTO song_files (song_id, file_path, file_format, file_size, uploaded_by, is_verified) VALUES (?, ?, ?, ?, ?, TRUE)");
        return $stmt->execute([$songId, $filePath, $format, $fileSize, $uploadedBy]);
    }
    
    public function deleteSongFile($fileId) {
        $stmt = $this->pdo->prepare("DELETE FROM song_files WHERE id = ?");
        return $stmt->execute([$fileId]);
    }
    
    public function addSongsToAlbum($albumId, $songIds) {
        foreach ($songIds as $songId) {
            $stmt = $this->pdo->prepare("UPDATE songs SET album_id = ? WHERE id = ?");
            $stmt->execute([$albumId, $songId]);
        }
        return true;
    }
    
    public function removeSongFromAlbum($songId) {
        $stmt = $this->pdo->prepare("UPDATE songs SET album_id = NULL WHERE id = ?");
        return $stmt->execute([$songId]);
    }
}
?>