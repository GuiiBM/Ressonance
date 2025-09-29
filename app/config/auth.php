<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'database.php';

// Configuração Google OAuth - SUBSTITUA PELOS SEUS VALORES
define('GOOGLE_CLIENT_ID', '111855969825-11ku1sli4acsmdv4nclpm6aq7iet70ur.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'COLE_SEU_CLIENT_SECRET_AQUI');
define('GOOGLE_REDIRECT_URI', 'http://localhost/Ressonance/auth.php');

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ../../../login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ../../../index.php');
        exit;
    }
}

// Processar callback do Google
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    // Trocar código por token
    $tokenUrl = 'https://oauth2.googleapis.com/token';
    $tokenData = [
        'client_id' => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri' => GOOGLE_REDIRECT_URI,
        'grant_type' => 'authorization_code',
        'code' => $code
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tokenUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $tokenResponse = curl_exec($ch);
    curl_close($ch);
    
    $tokenInfo = json_decode($tokenResponse, true);
    
    if (isset($tokenInfo['access_token'])) {
        // Obter informações do usuário
        $userUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $tokenInfo['access_token'];
        $userResponse = file_get_contents($userUrl);
        $userInfo = json_decode($userResponse, true);
        
        // Salvar/atualizar usuário no banco
        $stmt = $pdo->prepare("
            INSERT INTO users (google_id, name, email, profile_picture) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
            name = VALUES(name), 
            profile_picture = VALUES(profile_picture)
        ");
        $stmt->execute([
            $userInfo['id'],
            $userInfo['name'],
            $userInfo['email'],
            $userInfo['picture']
        ]);
        
        // Buscar dados completos do usuário
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->execute([$userInfo['id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Criar sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        header('Location: ../../../profile.php');
        exit;
    }
}

function logout() {
    session_destroy();
    header('Location: ../../../index.php');
    exit;
}

if (isset($_GET['logout'])) {
    logout();
}
?>