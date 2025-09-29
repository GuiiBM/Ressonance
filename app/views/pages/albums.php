<?php
require_once '../components/init.php';
$pageTitle = 'Álbuns';
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
        <section class="albums-page">
            <h1>ÁLBUNS</h1>
            <div class="albums-grid">
                <?php 
                $albums = $db->getAllAlbums();
                
                foreach($albums as $album): 
                ?>
                <div class="album-card-page" onclick="openAlbumModal(<?= $album['id'] ?>)">
                    <img src="<?= htmlspecialchars($album['image']) ?>" alt="<?= htmlspecialchars($album['title']) ?>">
                    <h4><?= htmlspecialchars($album['title']) ?></h4>
                    <p><?= htmlspecialchars($album['artist_name']) ?></p>
                    <small><?= $album['song_count'] ?> música<?= $album['song_count'] != 1 ? 's' : '' ?></small>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include '../components/player.php'; ?>

    <?php include '../components/album-modal.php'; ?>

    <?php include '../components/scripts.php'; ?>
</body>
</html>