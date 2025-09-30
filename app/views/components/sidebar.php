<?php
// Sidebar comum para todas as páginas
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <h2><a href="<?= BASE_URL ?>/" style="color: #ffffff; text-decoration: none;">HOME</a></h2>
    <nav>
        <ul>
            <li><a href="<?= BASE_URL ?>/albums.php" <?= $currentPage === 'albums.php' ? 'class="active"' : '' ?>>ÁLBUNS</a></li>
            <li><a href="<?= BASE_URL ?>/artists.php" <?= $currentPage === 'artists.php' ? 'class="active"' : '' ?>>ARTISTAS</a></li>
            <li><a href="<?= BASE_URL ?>/all-songs.php" <?= $currentPage === 'all-songs.php' ? 'class="active"' : '' ?>>TODAS AS MÚSICAS</a></li>
            <li><a href="#liked"><i class="fas fa-heart"></i> CURTIDAS</a></li>
            <li><a href="<?= BASE_URL ?>/upload_song.php"><i class="fas fa-upload"></i> UPLOAD</a></li>
        </ul>
    </nav>
</aside>