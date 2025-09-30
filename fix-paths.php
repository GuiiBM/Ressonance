<?php
// Script manual para forçar correção de caminhos
define('SKIP_PATH_CHECK', true);
require_once 'app/config/path-checker.php';

// Forçar verificação
$_GET['force'] = true;

echo "<h2>Correção de Caminhos - Ressonance</h2>";
echo "<p>Executando verificação completa...</p>";

// Executar correção
checkAndFixPaths();

// Verificar saúde do sistema
$issues = systemHealthCheck();

if (empty($issues)) {
    echo "<p style='color: green;'>✓ Todos os caminhos e arquivos estão corretos!</p>";
} else {
    echo "<p style='color: orange;'>Problemas encontrados:</p>";
    echo "<ul>";
    foreach ($issues as $issue) {
        echo "<li style='color: red;'>$issue</li>";
    }
    echo "</ul>";
}

echo "<p><a href='index.php'>Voltar ao site</a> | <a href='health-check.php'>Verificar saúde</a></p>";
?>