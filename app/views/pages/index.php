<?php
require_once '../components/init.php';
$pageTitle = 'Home';
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
        <section class="recommendations">
            <div class="section-header">
                <h3>MÚSICAS QUE VOCÊ PODE GOSTAR</h3>
                <a href="<?= BASE_URL ?>/all-songs.php" class="see-all-btn">Ver Todas</a>
            </div>
            <div class="playlist-grid">
                <?php 
                // Carregar algumas músicas iniciais para mostrar imediatamente
                $initialSongs = $db->getInitialSongs(6);
                
                foreach($initialSongs as $song): 
                    $audioFiles = [];
                    if (isset($song['audio_files']) && $song['audio_files']) {
                        foreach (explode('|', $song['audio_files']) as $fileData) {
                            if (strpos($fileData, ':') !== false) {
                                list($format, $path) = explode(':', $fileData, 2);
                                $audioFiles[] = ['format' => $format, 'path' => $path];
                            }
                        }
                    } else {
                        $audioFiles[] = ['format' => 'mp3', 'path' => $song['file_path'] ?? 'demo.mp3'];
                    }
                ?>
                <div class="playlist-item" onclick="playMusic('<?= htmlspecialchars($song['title'], ENT_QUOTES) ?>', '<?= htmlspecialchars($song['artist_name'], ENT_QUOTES) ?>', <?= htmlspecialchars(json_encode($audioFiles), ENT_QUOTES) ?>, '<?= htmlspecialchars(getImageUrl($song['image']), ENT_QUOTES) ?>')">
                    <?= fixImageTag($song['image'], $song['title']) ?>
                    <div class="playlist-item-content">
                        <h4><?= htmlspecialchars($song['title']) ?></h4>
                        <p><?= htmlspecialchars($song['artist_name']) ?></p>
                        <?php if (count($audioFiles) > 1): ?>
                            <div class="format-selector-mini">
                                <?php foreach ($audioFiles as $file): ?>
                                    <span class="format-badge"><?= strtoupper($file['format']) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="play-overlay">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="albums-section">
            <div class="section-header">
                <h3>ÁLBUNS RECENTES</h3>
                <a href="<?= BASE_URL ?>/albums.php" class="see-all-btn">Ver Todos</a>
            </div>
            <div class="albums-scroll">
                <?php 
                $albums = $db->getRecentAlbums(8);
                
                foreach($albums as $album): 
                ?>
                <div class="album-card" onclick="openAlbumModal(<?= $album['id'] ?>)">
                    <?= fixImageTag($album['image'], $album['title']) ?>
                    <h4><?= htmlspecialchars($album['title']) ?></h4>
                    <p><?= htmlspecialchars($album['artist_name']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="new-artists">
            <div class="section-header">
                <h3>ARTISTAS NOVOS</h3>
                <a href="<?= BASE_URL ?>/artists.php" class="see-all-btn">Ver Todos</a>
            </div>
            <div class="artists-scroll" id="artistsContainer">
                <!-- Artistas carregados dinamicamente -->
            </div>
        </section>

        <section class="ad-space">
            <div class="ad-banner">ESPAÇO PARA BANDAS OU ANUNCIOS</div>
        </section>
    </main>

    <?php include '../components/player.php'; ?>



    <?php include '../components/album-modal.php'; ?>

    <?php include '../components/scripts.php'; ?>

</body>
</html>