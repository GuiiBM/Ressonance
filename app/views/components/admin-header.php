<header class="admin-header">
    <div class="admin-header-left">
        <div class="admin-logo">
            <i class="fas fa-cog"></i>
            <span>ADMIN</span>
        </div>
        <h1 class="admin-site-name">RESSONANCE</h1>
    </div>
    <nav class="admin-nav">
        <a href="../../../index.php" class="admin-nav-link">
            <i class="fas fa-home"></i>
            <span>SITE</span>
        </a>
        <a href="../../../profile.php" class="admin-nav-link">
            <i class="fas fa-user"></i>
            <span>PERFIL</span>
        </a>
        <a href="../../../auth.php?logout=1" class="admin-nav-link logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>SAIR</span>
        </a>
    </nav>
</header>

<style>
.admin-header {
    background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    border-bottom: 3px solid #ff4b5a;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    position: sticky;
    top: 0;
    z-index: 1000;
}

.admin-header-left {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.admin-logo {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #ff4b5a;
    color: #fff;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-weight: 700;
    font-size: 0.9rem;
    box-shadow: 0 2px 10px rgba(255, 75, 90, 0.3);
}

.admin-logo i {
    font-size: 1.2rem;
    animation: spin 3s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.admin-site-name {
    color: #fff;
    font-size: 1.8rem;
    font-weight: 300;
    letter-spacing: 2px;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0,0,0,0.5);
}

.admin-nav {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.admin-nav-link {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: rgba(255,255,255,0.1);
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.admin-nav-link:hover {
    background: rgba(255,255,255,0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.admin-nav-link.logout {
    background: rgba(255, 75, 90, 0.2);
    border-color: #ff4b5a;
}

.admin-nav-link.logout:hover {
    background: #ff4b5a;
    color: #fff;
}

.admin-nav-link i {
    font-size: 1rem;
}

@media (max-width: 768px) {
    .admin-header {
        padding: 1rem;
        flex-direction: column;
        gap: 1rem;
    }
    
    .admin-header-left {
        gap: 1rem;
    }
    
    .admin-site-name {
        font-size: 1.4rem;
    }
    
    .admin-nav {
        width: 100%;
        justify-content: center;
    }
    
    .admin-nav-link span {
        display: none;
    }
}
</style>