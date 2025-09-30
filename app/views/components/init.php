<?php
// Inicialização original restaurada
require_once '../../config/paths.php';
require_once '../../config/database.php';
require_once '../../config/auth.php';
require_once 'config-common.php';
require_once 'database-queries.php';
require_once 'image-helper.php';

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Instanciar classe de queries
$db = new DatabaseQueries($pdo);
?>