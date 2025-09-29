<?php
require_once '../components/init.php';

if ($_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    // Login admin simples
    if ($email === 'admin@ressonance.com' && $password === 'admin123') {
        $_SESSION['user_id'] = 1;
        $_SESSION['user_name'] = 'Admin';
        $_SESSION['user_email'] = $email;
        $_SESSION['is_admin'] = true;
        
        header('Location: admin.php');
        exit;
    }
    
    header('Location: login.php?error=1');
    exit;
}
?>