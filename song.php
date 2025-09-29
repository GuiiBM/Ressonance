<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

$songId = $_GET['id'] ?? null;
if (!$songId) {
    header('Location: index.php');
    exit;
}

// Buscar dados da música
$stmt = $pdo->prepare("
    SELECT s.*, ar.name as artist_name, al.title as album_title, al.image as album_image,
           GROUP_CONCAT(CONCAT(sf.file_format, ':', sf.file_path) SEPARATOR '|') as audio_files
    FROM songs s 
    JOIN artists ar ON s.artist_id = ar.id 
    LEFT JOIN albums al ON s.album_id = al.id
    LEFT JOIN song_files sf ON s.id = sf.song_id AND sf.is_verified = TRUE
    WHERE s.id = ?
    GROUP BY s.id
");
$stmt->execute([$songId]);
$song = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$song) {
    header('Location: index.php');
    exit;
}

// Processar arquivos de áudio
$audioFiles = [];
if ($song['audio_files']) {
    foreach (explode('|', $song['audio_files']) as $fileData) {
        list($format, $path) = explode(':', $fileData);
        $audioFiles[] = ['format' => $format, 'path' => $path];
    }
}

// Incrementar plays
$pdo->prepare("UPDATE songs SET plays = plays + 1 WHERE id = ?")->execute([$songId]);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($song['title']) ?> - <?= htmlspecialchars($song['artist_name']) ?> | Ressonance</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <div class="logo-container">
                <div class="logo-text">RESSONANCE</div>
            </div>
        </div>
        <div class="profile">
            <a href="index.php">HOME</a>
            <?php if (isset($_SESSION['user_logged']) && $_SESSION['user_logged']): ?>
                <a href="auto_oauth.php"><?= htmlspecialchars($_SESSION['user_name']) ?></a>
            <?php else: ?>
                <a href="auto_oauth.php">ENTRAR</a>
            <?php endif; ?>
        </div>
    </header>

    <main style="margin-top: 70px; padding: 2rem; max-width: 1200px; margin-left: auto; margin-right: auto;">
        <div style="display: grid; grid-template-columns: 300px 1fr; gap: 3rem; margin-bottom: 3rem;">
            <div style="text-align: center;">
                <img src="<?= htmlspecialchars($song['album_image'] ?: 'https://via.placeholder.com/300x300/1db954/ffffff?text=♪') ?>" 
                     alt="<?= htmlspecialchars($song['title']) ?>" 
                     style="width: 100%; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.4);">
            </div>
            
            <div>
                <h1 style="font-size: 3rem; margin-bottom: 1rem; font-weight: 700;"><?= htmlspecialchars($song['title']) ?></h1>
                <h2 style="font-size: 1.5rem; color: #b3b3b3; margin-bottom: 2rem;"><?= htmlspecialchars($song['artist_name']) ?></h2>
                
                <?php if ($song['album_title']): ?>
                    <p style="margin-bottom: 1rem;"><strong>Álbum:</strong> <?= htmlspecialchars($song['album_title']) ?></p>
                <?php endif; ?>
                
                <?php if ($song['duration']): ?>
                    <p style="margin-bottom: 1rem;"><strong>Duração:</strong> <?= $song['duration'] ?></p>
                <?php endif; ?>
                
                <p style="margin-bottom: 2rem;"><strong>Reproduções:</strong> <?= number_format($song['plays']) ?></p>
                
                <?php if (!empty($audioFiles)): ?>
                    <div style="margin-bottom: 2rem;">
                        <h3 style="margin-bottom: 1rem;">Formatos Disponíveis:</h3>
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <?php foreach ($audioFiles as $file): ?>
                                <button onclick="playAudio('<?= htmlspecialchars($file['path']) ?>')" 
                                        style="background: #1db954; color: #000; border: none; padding: 1rem 2rem; border-radius: 25px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                                    <i class="fas fa-play"></i> <?= strtoupper($file['format']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <button onclick="playMainAudio()" id="mainPlayBtn" 
                            style="background: linear-gradient(135deg, #1db954, #1ed760); color: #000; border: none; padding: 1rem 2rem; border-radius: 50px; font-size: 1.2rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 16px rgba(29, 185, 84, 0.4);">
                        <i class="fas fa-play"></i> REPRODUZIR
                    </button>
                    
                    <button onclick="toggleLike()" id="likeBtn" 
                            style="background: none; border: 2px solid #b3b3b3; color: #b3b3b3; padding: 1rem; border-radius: 50%; cursor: pointer; transition: all 0.3s;">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div style="background: #1d1d1d; padding: 2rem; border-radius: 12px;">
            <h3 style="margin-bottom: 1rem;">Player de Áudio</h3>
            <audio id="audioPlayer" controls style="width: 100%; background: #282828; border-radius: 8px;">
                Seu navegador não suporta o elemento de áudio.
            </audio>
        </div>
    </main>

    <script>
        let currentAudio = null;
        const audioPlayer = document.getElementById('audioPlayer');
        const mainPlayBtn = document.getElementById('mainPlayBtn');
        
        function playAudio(filePath) {
            audioPlayer.src = filePath;
            audioPlayer.load();
            audioPlayer.play();
            updatePlayButton(true);
        }
        
        function playMainAudio() {
            <?php if (!empty($audioFiles)): ?>
                playAudio('<?= htmlspecialchars($audioFiles[0]['path']) ?>');
            <?php endif; ?>
        }
        
        function updatePlayButton(isPlaying) {
            const icon = mainPlayBtn.querySelector('i');
            if (isPlaying) {
                icon.className = 'fas fa-pause';
                mainPlayBtn.innerHTML = '<i class="fas fa-pause"></i> PAUSAR';
            } else {
                icon.className = 'fas fa-play';
                mainPlayBtn.innerHTML = '<i class="fas fa-play"></i> REPRODUZIR';
            }
        }
        
        function toggleLike() {
            const likeBtn = document.getElementById('likeBtn');
            const icon = likeBtn.querySelector('i');
            
            if (likeBtn.style.color === 'rgb(255, 75, 90)') {
                likeBtn.style.color = '#b3b3b3';
                likeBtn.style.borderColor = '#b3b3b3';
                icon.className = 'far fa-heart';
            } else {
                likeBtn.style.color = '#ff4b5a';
                likeBtn.style.borderColor = '#ff4b5a';
                icon.className = 'fas fa-heart';
            }
        }
        
        // Event listeners
        audioPlayer.addEventListener('play', () => updatePlayButton(true));
        audioPlayer.addEventListener('pause', () => updatePlayButton(false));
        audioPlayer.addEventListener('ended', () => updatePlayButton(false));
        
        mainPlayBtn.addEventListener('click', () => {
            if (audioPlayer.paused) {
                audioPlayer.play();
            } else {
                audioPlayer.pause();
            }
        });
    </script>
</body>
</html>