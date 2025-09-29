<?php
require_once '../components/init.php';
require_once '../../config/auth.php';
requireLogin();

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Atualizar perfil
if ($_POST && isset($_POST['update_profile'])) {
    $age = $_POST['age'] ? (int)$_POST['age'] : null;
    $city = $_POST['city'] ?: null;
    
    $stmt = $pdo->prepare("UPDATE users SET age = ?, city = ? WHERE id = ?");
    $stmt->execute([$age, $city, $_SESSION['user_id']]);
    
    header('Location: profile.php?updated=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Ressonance</title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <div class="logo">LOGO</div>
            <h1 class="site-name">RESSONANCE</h1>
        </div>
        <nav class="nav">
            <ul>
                <li><a href="../../../index.php">HOME</a></li>
                <li><a href="#discover">DESCUBRA</a></li>
                <li><a href="#trending">TENDÊNCIAS</a></li>
                <li><a href="#events">EVENTOS</a></li>
            </ul>
        </nav>
        <div class="profile">
            <a href="../../../auth.php?logout=1">SAIR</a>
        </div>
    </header>

    <main class="profile-main">
        <div class="profile-container">
            <div class="profile-header">
                <img src="<?= htmlspecialchars($user['profile_picture'] ?: 'https://via.placeholder.com/150x150/1db954/ffffff?text=U') ?>" 
                     alt="Foto do perfil" class="profile-image">
                <div class="profile-info">
                    <h1><?= htmlspecialchars($user['name']) ?></h1>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                    <?php if ($user['is_admin']): ?>
                        <span class="admin-badge">ADMINISTRADOR</span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($_GET['updated'])): ?>
                <div class="success-message">Perfil atualizado com sucesso!</div>
            <?php endif; ?>

            <form method="POST" class="profile-form">
                <div class="form-group">
                    <label>Nome:</label>
                    <input type="text" value="<?= htmlspecialchars($user['name']) ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="age">Idade:</label>
                    <input type="number" id="age" name="age" value="<?= $user['age'] ?>" min="13" max="120">
                </div>
                
                <div class="form-group">
                    <label for="city">Cidade:</label>
                    <input type="text" id="city" name="city" value="<?= htmlspecialchars($user['city']) ?>" maxlength="255">
                </div>
                
                <button type="submit" name="update_profile" class="update-btn">Atualizar Perfil</button>
            </form>

            <?php if ($user['is_admin']): ?>
                <div class="admin-section">
                    <h3>Área Administrativa</h3>
                    <a href="../../../admin.php" class="admin-btn">Gerenciar Sistema</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <style>
        .profile-main {
            margin-top: 70px;
            padding: 2rem;
            min-height: calc(100vh - 70px);
            display: flex;
            justify-content: center;
        }
        
        .profile-container {
            max-width: 600px;
            width: 100%;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            background-color: #1d1d1d;
            padding: 2rem;
            border-radius: 12px;
        }
        
        .profile-image {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .profile-info h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }
        
        .profile-info p {
            color: #b3b3b3;
            margin-bottom: 0.5rem;
        }
        
        .admin-badge {
            background-color: #ff4b5a;
            color: #ffffff;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .profile-form {
            background-color: #1d1d1d;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            background-color: #282828;
            border: 1px solid #404040;
            border-radius: 4px;
            color: #ffffff;
        }
        
        .form-group input:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .update-btn {
            background-color: #1db954;
            color: #000000;
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .update-btn:hover {
            background-color: #1ed760;
        }
        
        .admin-section {
            background-color: #1d1d1d;
            padding: 2rem;
            border-radius: 12px;
            text-align: center;
        }
        
        .admin-btn {
            background-color: #ff4b5a;
            color: #ffffff;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 600;
            display: inline-block;
            margin-top: 1rem;
            transition: background-color 0.3s;
        }
        
        .admin-btn:hover {
            background-color: #ff6b7a;
        }
        
        .success-message {
            background-color: #1db954;
            color: #000000;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            text-align: center;
            font-weight: 600;
        }
    </style>
</body>
</html>