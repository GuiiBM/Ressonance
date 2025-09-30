<?php
// Verificação completa de integridade do sistema
define('SKIP_PATH_CHECK', true);

echo "<h1>🔍 Verificação de Integridade - Sistema Ressonance</h1>";

// Verificar estrutura de arquivos
echo "<h2>📁 Estrutura de Arquivos</h2>";
$requiredFiles = [
    'index.php' => 'Página inicial',
    'audio.php' => 'Servidor de áudio',
    'image.php' => 'Servidor de imagens',
    'admin.php' => 'Painel administrativo',
    'fix-paths.php' => 'Correção de caminhos',
    'health-check.php' => 'Verificação de saúde',
    'app/config/database.php' => 'Configuração do banco',
    'app/config/paths.php' => 'Configuração de caminhos',
    'public/assets/css/styles.css' => 'CSS principal',
    'public/assets/js/config.js' => 'Configurações JavaScript',
    'public/assets/js/player-core.js' => 'Player de música'
];

$missingFiles = [];
foreach ($requiredFiles as $file => $desc) {
    if (file_exists($file)) {
        echo "✅ $desc ($file)<br>";
    } else {
        echo "❌ $desc ($file) - <strong>AUSENTE</strong><br>";
        $missingFiles[] = $file;
    }
}

// Verificar pastas
echo "<h2>📂 Estrutura de Pastas</h2>";
$requiredDirs = [
    'app' => 'Aplicação principal',
    'app/config' => 'Configurações',
    'app/views' => 'Views e páginas',
    'public/assets' => 'Assets públicos',
    'audio' => 'Arquivos de música',
    'docs' => 'Documentação'
];

$missingDirs = [];
foreach ($requiredDirs as $dir => $desc) {
    if (is_dir($dir)) {
        echo "✅ $desc ($dir/)<br>";
    } else {
        echo "❌ $desc ($dir/) - <strong>AUSENTE</strong><br>";
        $missingDirs[] = $dir;
    }
}

// Verificar banco de dados
echo "<h2>🗄️ Banco de Dados</h2>";
try {
    require_once 'app/config/database.php';
    echo "✅ Conexão com banco estabelecida<br>";
    
    $tables = ['artists', 'albums', 'songs', 'song_files', 'playlists'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "✅ Tabela '$table' - $count registros<br>";
        } catch (Exception $e) {
            echo "❌ Tabela '$table' - ERRO: " . $e->getMessage() . "<br>";
        }
    }
} catch (Exception $e) {
    echo "❌ Erro na conexão: " . $e->getMessage() . "<br>";
}

// Verificar configurações
echo "<h2>⚙️ Configurações</h2>";
require_once 'app/config/paths.php';
echo "✅ BASE_URL: " . BASE_URL . "<br>";
echo "✅ CSS_URL: " . CSS_URL . "<br>";
echo "✅ JS_URL: " . JS_URL . "<br>";

// Verificar arquivos de música
echo "<h2>🎵 Arquivos de Música</h2>";
$audioDir = 'audio';
if (is_dir($audioDir)) {
    $audioFiles = glob($audioDir . '/*.{mp3,flac,wav,ogg,m4a}', GLOB_BRACE);
    $count = count($audioFiles);
    echo "✅ Pasta de áudio encontrada - $count arquivos<br>";
    
    if ($count > 0) {
        echo "<details><summary>Ver arquivos ($count)</summary>";
        foreach (array_slice($audioFiles, 0, 10) as $file) {
            $size = filesize($file);
            $sizeFormatted = $size > 1024*1024 ? round($size/(1024*1024), 1).'MB' : round($size/1024, 1).'KB';
            echo "• " . basename($file) . " ($sizeFormatted)<br>";
        }
        if ($count > 10) echo "... e mais " . ($count - 10) . " arquivos<br>";
        echo "</details>";
    }
} else {
    echo "❌ Pasta de áudio não encontrada<br>";
}

// Resumo final
echo "<h2>📊 Resumo</h2>";
$totalIssues = count($missingFiles) + count($missingDirs);

if ($totalIssues == 0) {
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px;'>";
    echo "<strong>🎉 Sistema íntegro!</strong><br>";
    echo "Todos os arquivos e configurações estão corretos.";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<strong>⚠️ Problemas encontrados: $totalIssues</strong><br>";
    if (!empty($missingFiles)) {
        echo "Arquivos ausentes: " . implode(', ', $missingFiles) . "<br>";
    }
    if (!empty($missingDirs)) {
        echo "Pastas ausentes: " . implode(', ', $missingDirs) . "<br>";
    }
    echo "</div>";
}

echo "<br><p>";
echo "<a href='fix-paths.php'>🔧 Corrigir Caminhos</a> | ";
echo "<a href='health-check.php'>🏥 Verificar Saúde</a> | ";
echo "<a href='index.php'>🏠 Voltar ao Site</a>";
echo "</p>";
?>