<?php
require_once '../components/init.php';
$pageTitle = 'Todas as Músicas';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include '../components/head.php'; ?>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <?php include '../components/sidebar.php'; ?>

    <main class="content">
        <section class="all-songs-section">
            <div class="section-header">
                <h3>TODAS AS MÚSICAS</h3>
                <div class="view-controls">
                    <button onclick="toggleView('grid')" class="view-btn active" id="gridBtn">
                        <i class="fas fa-th"></i>
                    </button>
                    <button onclick="toggleView('list')" class="view-btn" id="listBtn">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
            
            <div class="songs-container grid-view" id="songsContainer">
                <?php 
                $allSongs = $db->getAllSongs();
                
                if (empty($allSongs)): ?>
                    <div class="no-songs">
                        <i class="fas fa-music"></i>
                        <p>Nenhuma música encontrada</p>
                    </div>
                <?php else: ?>
                    <?php foreach($allSongs as $song): 
                        $audioFiles = [];
                        if ($song['audio_files']) {
                            foreach (explode('|', $song['audio_files']) as $fileData) {
                                if (strpos($fileData, ':') !== false) {
                                    list($format, $path) = explode(':', $fileData, 2);
                                    $audioFiles[] = ['format' => $format, 'path' => $path];
                                }
                            }
                        }
                    ?>
                    <div class="song-item" onclick="playMusic('<?= htmlspecialchars($song['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($song['artist_name'], ENT_QUOTES) ?>', <?= htmlspecialchars(json_encode($audioFiles), ENT_QUOTES) ?>)">
                        <div class="song-cover">
                            <img src="https://via.placeholder.com/200x200/1db954/ffffff?text=%E2%99%AA" alt="<?= htmlspecialchars($song['title']) ?>">
                            <div class="play-overlay">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <div class="song-info">
                            <h4><?= htmlspecialchars($song['title']) ?></h4>
                            <p><?= htmlspecialchars($song['artist_name']) ?></p>
                            <?php if ($song['album_title']): ?>
                                <small class="album-info"><?= htmlspecialchars($song['album_title']) ?></small>
                            <?php endif; ?>
                            <?php if (count($audioFiles) > 1): ?>
                                <div class="format-badges">
                                    <?php foreach ($audioFiles as $file): ?>
                                        <span class="format-badge"><?= strtoupper($file['format']) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <?php include '../components/player.php'; ?>

    <?php include '../components/scripts.php'; ?>
    
    <script>
        function toggleView(viewType) {
            const container = document.getElementById('songsContainer');
            const gridBtn = document.getElementById('gridBtn');
            const listBtn = document.getElementById('listBtn');
            
            if (viewType === 'grid') {
                container.className = 'songs-container grid-view';
                gridBtn.classList.add('active');
                listBtn.classList.remove('active');
            } else {
                container.className = 'songs-container list-view';
                listBtn.classList.add('active');
                gridBtn.classList.remove('active');
            }
        }
    </script>
</body>
</html>