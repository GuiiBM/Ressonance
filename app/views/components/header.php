<?php
// Header comum para todas as páginas
$logoUrl = SITE_LOGO;
?>
<header class="header">
    <div class="header-left">
        <div class="logo-container">
            <?php if (!empty($logoUrl)): ?>
                <img src="<?= htmlspecialchars($logoUrl) ?>" alt="Ressonance" class="logo-image">
            <?php else: ?>
                <div class="logo-text">RESSONANCE</div>
            <?php endif; ?>
        </div>
    </div>
    <nav class="nav">
        <ul>
            <li><a href="/Ressonance/">HOME</a></li>
            <li><a href="/Ressonance/albums.php">ÁLBUNS</a></li>
            <li><a href="/Ressonance/artists.php">ARTISTAS</a></li>
            <li><a href="/Ressonance/all-songs.php">MÚSICAS</a></li>
        </ul>
    </nav>
    <div class="profile">
        <?php if (isLoggedIn()): ?>
            <a href="/Ressonance/profile.php"><i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_name']) ?></a>
        <?php else: ?>
            <a href="/Ressonance/login.php"><i class="fas fa-user"></i> ENTRAR</a>
        <?php endif; ?>
    </div>
</header>