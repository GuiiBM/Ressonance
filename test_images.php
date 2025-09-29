<?php
require_once 'app/config/database.php';

// Testar query direta
$stmt = $pdo->query("SELECT id, title, image FROM songs WHERE image IS NOT NULL LIMIT 3");
$songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Teste direto da query:</h3>";
foreach ($songs as $song) {
    echo "ID: " . $song['id'] . " - " . $song['title'] . " - Imagem: " . $song['image'] . "<br>";
}

// Testar a query da função getInitialSongs
$stmt = $pdo->prepare("
    SELECT s.*, ar.name as artist_name,
           GROUP_CONCAT(DISTINCT CONCAT(sf.file_format, ':', SUBSTRING_INDEX(sf.file_path, '/', -1)) SEPARATOR '|') as audio_files
    FROM songs s 
    JOIN artists ar ON s.artist_id = ar.id 
    LEFT JOIN song_files sf ON s.id = sf.song_id AND sf.is_verified = TRUE
    WHERE sf.file_path IS NOT NULL
    GROUP BY s.id
    ORDER BY s.plays DESC 
    LIMIT 3
");
$stmt->execute();
$songs2 = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Teste da query getInitialSongs:</h3>";
foreach ($songs2 as $song) {
    echo "ID: " . $song['id'] . " - " . $song['title'] . " - Imagem: " . ($song['image'] ?? 'NULL') . "<br>";
}
?>