<?php
// Inicialização garantida na primeira execução
function ensureFirstRunSetup() {
    $basePath = dirname(dirname(dirname(__FILE__)));
    $flagFile = $basePath . '/.paths_checked';
    
    // Se é primeira execução (arquivo não existe)
    if (!file_exists($flagFile)) {
        // Forçar execução completa
        $_GET['force'] = true;
        
        // Log da primeira execução
        error_log("Ressonance: Primeira execução detectada - configurando sistema");
        
        // Garantir que path-checker seja executado
        require_once __DIR__ . '/path-checker.php';
        
        // Garantir que database seja inicializado
        require_once __DIR__ . '/init-database.php';
        initializeDatabase();
        
        // Criar estruturas essenciais
        createEssentialStructure($basePath);
        
        return true;
    }
    
    return false;
}

function createEssentialStructure($basePath) {
    // Criar pastas essenciais se não existirem
    $essentialFolders = [
        '/audio',
        '/public/assets/css',
        '/public/assets/js',
        '/public/assets/images',
        '/storage/uploads/audio',
        '/app/config',
        '/app/views/components',
        '/app/views/pages'
    ];
    
    foreach ($essentialFolders as $folder) {
        $fullPath = $basePath . $folder;
        if (!is_dir($fullPath)) {
            mkdir($fullPath, 0755, true);
        }
    }
    
    // Criar arquivo .htaccess para proteção se não existir
    $htaccessContent = "# Proteção de arquivos sensíveis\n<Files \"*.php\">\n    Order Allow,Deny\n    Allow from all\n</Files>";
    $htaccessFile = $basePath . '/app/.htaccess';
    if (!file_exists($htaccessFile)) {
        file_put_contents($htaccessFile, $htaccessContent);
    }
}

// Executar verificação de primeira execução
ensureFirstRunSetup();
?>