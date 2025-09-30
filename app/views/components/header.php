<?php
// Header comum para todas as páginas
$logoUrl = SITE_LOGO;
?>
<header class="header">
    <div class="header-left">
        <div class="logo-container">
            <?php if (!empty($logoUrl)): ?>
                <img src="<?= getImageUrl($logoUrl) ?>" alt="Ressonance" class="logo-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div class="logo-text" style="display: none;">RESSONANCE</div>
            <?php else: ?>
                <div class="logo-text">RESSONANCE</div>
            <?php endif; ?>
        </div>
    </div>
    <nav class="nav">
        <ul>
            <li><a href="<?= BASE_URL ?>/">HOME</a></li>
            <li><a href="<?= BASE_URL ?>/albums.php">ÁLBUNS</a></li>
            <li><a href="<?= BASE_URL ?>/artists.php">ARTISTAS</a></li>
            <li><a href="<?= BASE_URL ?>/all-songs.php">MÚSICAS</a></li>
        </ul>
    </nav>
    <div class="profile">
        <?php if (isLoggedIn()): ?>
            <a href="<?= BASE_URL ?>/profile.php"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name']) ?></a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php"><i class="fas fa-user"></i> ENTRAR</a>
        <?php endif; ?>
    </div>
</header>