<?php
// Verificador e corretor automático de caminhos

function checkAndFixPaths() {
    $basePath = dirname(dirname(dirname(__FILE__)));
    $flagFile = $basePath . '/.paths_checked';
    
    // SEMPRE executar na primeira vez (arquivo não existe)
    // Depois executar a cada 6 horas ou se forçado
    if (file_exists($flagFile) && (time() - filemtime($flagFile)) < 21600 && !isset($_GET['force'])) {
        return;
    }
    
    // Detectar BASE_URL correto
    $scriptName = $_SERVER['SCRIPT_NAME'];
    $pathParts = explode('/', trim($scriptName, '/'));
    $projectPath = '';
    
    // Encontrar "Ressonance" no caminho
    foreach ($pathParts as $i => $part) {
        if (strtolower($part) === 'ressonance') {
            $projectPath = '/' . implode('/', array_slice($pathParts, 0, $i + 2));
            break;
        }
    }
    
    if (empty($projectPath)) {
        $projectPath = '/Ressonance/Ressonance';
    }
    
    // Corrigir paths.php
    $pathsFile = $basePath . '/app/config/paths.php';
    if (file_exists($pathsFile)) {
        $pathsContent = file_get_contents($pathsFile);
        if (strpos($pathsContent, "define('BASE_URL', '$projectPath');") === false) {
            $pathsContent = preg_replace(
                "/define\('BASE_URL', '[^']*'\);/",
                "define('BASE_URL', '$projectPath');",
                $pathsContent
            );
            file_put_contents($pathsFile, $pathsContent);
        }
    }
    
    // Corrigir config.js
    $configJsFile = $basePath . '/public/assets/js/config.js';
    $configContent = "// Configurações JavaScript\nwindow.APP_CONFIG = {\n    API_BASE_URL: '$projectPath/app/controllers/api',\n    BASE_URL: '$projectPath',\n    IMAGE_URL: '$projectPath/image.php'\n};";
    file_put_contents($configJsFile, $configContent);
    
    // Corrigir caminhos de imagem no banco de dados
    fixImagePaths($projectPath);
    
    // Verificar estrutura de pastas essenciais
    checkEssentialFolders($basePath);
    
    // Verificar arquivos essenciais
    checkEssentialFiles($basePath, $projectPath);
    
    // Forçar execução na primeira vez
    $isFirstRun = !file_exists($flagFile);
    
    // Criar/atualizar arquivo de flag
    $flagContent = date('Y-m-d H:i:s') . ' - ' . $projectPath;
    if ($isFirstRun) {
        $flagContent .= ' - FIRST_RUN';
    }
    file_put_contents($flagFile, $flagContent);
}

function fixImagePaths($projectPath) {
    try {
        require_once dirname(dirname(dirname(__FILE__))) . '/app/config/database.php';
        
        // Corrigir imagens de artistas
        $stmt = $pdo->prepare("UPDATE artists SET image = CONCAT(?, '/image.php?f=', SUBSTRING_INDEX(image, '/', -1)) WHERE image IS NOT NULL AND image != '' AND image NOT LIKE '%image.php%'");
        $stmt->execute([$projectPath]);
        
        // Corrigir imagens de álbuns
        $stmt = $pdo->prepare("UPDATE albums SET image = CONCAT(?, '/image.php?f=', SUBSTRING_INDEX(image, '/', -1)) WHERE image IS NOT NULL AND image != '' AND image NOT LIKE '%image.php%'");
        $stmt->execute([$projectPath]);
        
        // Corrigir imagens de músicas
        $stmt = $pdo->prepare("UPDATE songs SET image = CONCAT(?, '/image.php?f=', SUBSTRING_INDEX(image, '/', -1)) WHERE image IS NOT NULL AND image != '' AND image NOT LIKE '%image.php%'");
        $stmt->execute([$projectPath]);
        
    } catch (Exception $e) {
        // Ignorar erros de banco se ainda não estiver configurado
    }
}

function checkEssentialFolders($basePath) {
    $folders = [
        '/public/assets/css',
        '/public/assets/js', 
        '/public/assets/images',
        '/storage/uploads/audio',
        '/audio'
    ];
    
    foreach ($folders as $folder) {
        $fullPath = $basePath . $folder;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
    }
}

function checkEssentialFiles($basePath, $projectPath) {
    $essentialFiles = [
        '/audio.php',
        '/image.php', 
        '/index.php',
        '/public/assets/css/styles.css'
    ];
    
    foreach ($essentialFiles as $file) {
        if (!file_exists($basePath . $file)) {
            error_log("Arquivo essencial não encontrado: $file");
        }
    }
}

function systemHealthCheck() {
    $basePath = dirname(dirname(dirname(__FILE__)));
    $issues = [];
    
    $essentialFiles = [
        '/audio.php' => 'Servidor de áudio',
        '/image.php' => 'Servidor de imagens',
        '/index.php' => 'Página inicial',
        '/public/assets/css/styles.css' => 'CSS principal'
    ];
    
    foreach ($essentialFiles as $file => $desc) {
        if (!file_exists($basePath . $file)) {
            $issues[] = "$desc não encontrado: $file";
        }
    }
    
    return $issues;
}

// Executar verificação
if (!defined('SKIP_PATH_CHECK')) {
    checkAndFixPaths();
}
?>