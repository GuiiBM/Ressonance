<?php
// Inicialização comum para todas as páginas
require_once '../../config/paths.php';
require_once '../../config/database.php';
require_once '../../config/auth.php';
require_once 'config-common.php';
require_once 'database-queries.php';

// Iniciar sessão e gerar token CSRF
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Instanciar classe de queries
$db = new DatabaseQueries($pdo);
?>