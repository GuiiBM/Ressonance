<?php
require_once '../components/init.php';
$pageTitle = 'Artistas';

$artists = $db->getAllArtists();
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
        <section class="artists-page">
            <h3>TODOS OS ARTISTAS</h3>
            <div class="artists-grid">
                <?php foreach($artists as $artist): ?>
                <div class="artist-card-page">
                    <?= fixImageTag($artist['image'], $artist['name']) ?>
                    <h4><?= htmlspecialchars($artist['name']) ?></h4>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include '../components/player.php'; ?>

    <?php include '../components/scripts.php'; ?>
</body>
</html>