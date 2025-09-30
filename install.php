<?php
// Instalador completo do Ressonance
require_once 'app/config/environment.php';
require_once 'database/init.php';

class RessonanceInstaller {
    private $errors = [];
    private $warnings = [];
    private $config = [];
    private $dbInit;
    
    public function run() {
        echo "<!DOCTYPE html><html><head><title>Instalador Ressonance</title><style>body{font-family:Arial;margin:40px;} .success{color:green;} .error{color:red;} .warning{color:orange;}</style></head><body>";
        echo "<h1>🎵 Instalador Ressonance v1.0</h1>";
        
        $this->checkRequirements();
        $this->detectEnvironment();
        $this->setupDatabase();
        $this->createStructure();
        $this->configureFiles();
        $this->testSystem();
        $this->showReport();
        
        echo "</body></html>";
    }
    
    private function checkRequirements() {
        echo "<h2>1. Verificando Requisitos</h2>";
        
        if (version_compare(PHP_VERSION, '7.4.0') < 0) {
            $this->errors[] = "PHP 7.4+ necessário. Atual: " . PHP_VERSION;
        } else {
            echo "<div class='success'>✅ PHP " . PHP_VERSION . "</div>";
        }
        
        $required = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'fileinfo'];
        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                $this->errors[] = "Extensão '$ext' não encontrada";
            } else {
                echo "<div class='success'>✅ Extensão $ext</div>";
            }
        }
        
        $dirs = ['.', 'app/config', 'storage', 'public/assets', 'audio'];
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            if (!is_writable($dir)) {
                $this->warnings[] = "Diretório '$dir' sem permissão de escrita";
            }
        }
    }
    
    private function detectEnvironment() {
        echo "<h2>2. Detectando Ambiente</h2>";
        
        $this->config = Environment::detect();
        
        echo "<div class='success'>✅ Caminho: {$this->config['BASE_URL']}</div>";
        echo "<div class='success'>✅ Raiz: {$this->config['ROOT_PATH']}</div>";
        echo "<div class='success'>✅ DB Host: {$this->config['DB_HOST']}</div>";
        echo "<div class='success'>✅ DB User: {$this->config['DB_USER']}</div>";
        
        $server = $_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido';
        echo "<div>ℹ️ Servidor: $server</div>";
        echo "<div>ℹ️ Sistema: " . PHP_OS_FAMILY . "</div>";
    }
    
    private function setupDatabase() {
        echo "<h2>3. Configurando Banco de Dados</h2>";
        
        try {
            $this->dbInit = new DatabaseInitializer($this->config);
            $result = $this->dbInit->initialize();
            
            if ($result['success']) {
                echo "<div class='success'>✅ {$result['message']}</div>";
                $this->verifyDatabaseStructure();
            } else {
                $this->errors[] = $result['message'];
            }
            
        } catch (Exception $e) {
            $this->errors[] = "Erro no banco: " . $e->getMessage();
        }
    }
    
    private function verifyDatabaseStructure() {
        try {
            $pdo = $this->dbInit->getConnection();
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            $required = ['artists', 'albums', 'songs', 'playlists', 'system_config'];
            foreach ($required as $table) {
                if (in_array($table, $tables)) {
                    echo "<div class='success'>✅ Tabela $table</div>";
                } else {
                    $this->errors[] = "Tabela '$table' não encontrada";
                }
            }
            
            // Verificar dados iniciais
            $count = $pdo->query("SELECT COUNT(*) FROM system_config")->fetchColumn();
            if ($count > 0) {
                echo "<div class='success'>✅ Configurações iniciais carregadas</div>";
            }
            
        } catch (Exception $e) {
            $this->warnings[] = "Erro ao verificar estrutura: " . $e->getMessage();
        }
    }
    
    private function createStructure() {
        echo "<h2>4. Criando Estrutura de Pastas</h2>";
        
        $dirs = [
            'storage/uploads/audio',
            'storage/logs', 
            'storage/cache',
            'public/assets/css',
            'public/assets/js',
            'public/assets/images',
            'audio',
            'images'
        ];
        
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                if (mkdir($dir, 0755, true)) {
                    echo "<div class='success'>✅ Criado: $dir</div>";
                } else {
                    $this->warnings[] = "Não foi possível criar: $dir";
                }
            } else {
                echo "<div>ℹ️ Existe: $dir</div>";
            }
        }
    }
    
    private function configureFiles() {
        echo "<h2>5. Configurando Arquivos</h2>";
        
        // Atualizar paths.php
        $pathsFile = 'app/config/paths.php';
        if (file_exists($pathsFile)) {
            $content = file_get_contents($pathsFile);
            $content = preg_replace(
                "/define\('BASE_URL', '[^']*'\);/",
                "define('BASE_URL', '{$this->config['BASE_URL']}');",
                $content
            );
            file_put_contents($pathsFile, $content);
            echo "<div class='success'>✅ paths.php atualizado</div>";
        }
        
        // Criar config.js
        $configJs = "window.APP_CONFIG = {\n";
        $configJs .= "    API_BASE_URL: '{$this->config['BASE_URL']}/app/controllers/api',\n";
        $configJs .= "    BASE_URL: '{$this->config['BASE_URL']}',\n";
        $configJs .= "    IMAGE_URL: '{$this->config['BASE_URL']}/image.php'\n";
        $configJs .= "};";
        
        file_put_contents('public/assets/js/config.js', $configJs);
        echo "<div class='success'>✅ config.js criado</div>";
        
        // Criar arquivo de flag
        $flagContent = json_encode([
            'installed_at' => date('Y-m-d H:i:s'),
            'version' => '1.0.0',
            'config' => $this->config
        ], JSON_PRETTY_PRINT);
        
        file_put_contents('.ressonance_installed', $flagContent);
        echo "<div class='success'>✅ Instalação marcada como concluída</div>";
    }
    
    private function testSystem() {
        echo "<h2>6. Testando Sistema</h2>";
        
        // Testar conexão com banco
        try {
            $pdo = $this->dbInit->getConnection();
            $result = $pdo->query("SELECT COUNT(*) FROM artists")->fetchColumn();
            echo "<div class='success'>✅ Conexão com banco funcionando</div>";
        } catch (Exception $e) {
            $this->errors[] = "Erro ao testar banco: " . $e->getMessage();
        }
        
        // Testar arquivos essenciais
        $files = ['index.php', 'audio.php', 'image.php'];
        foreach ($files as $file) {
            if (file_exists($file)) {
                echo "<div class='success'>✅ Arquivo $file</div>";
            } else {
                $this->errors[] = "Arquivo essencial '$file' não encontrado";
            }
        }
    }
    
    private function showReport() {
        echo "<h2>7. Relatório Final</h2>";
        
        if (!empty($this->errors)) {
            echo "<h3 class='error'>❌ Erros Encontrados:</h3>";
            foreach ($this->errors as $error) {
                echo "<div class='error'>• $error</div>";
            }
        }
        
        if (!empty($this->warnings)) {
            echo "<h3 class='warning'>⚠️ Avisos:</h3>";
            foreach ($this->warnings as $warning) {
                echo "<div class='warning'>• $warning</div>";
            }
        }
        
        if (empty($this->errors)) {
            echo "<h3 class='success'>🎉 Instalação Concluída com Sucesso!</h3>";
            echo "<p><strong><a href='index.php'>🎵 Acessar Ressonance</a></strong></p>";
            echo "<p><a href='health-check.php'>Verificar Saúde do Sistema</a></p>";
        } else {
            echo "<h3 class='error'>❌ Instalação Falhou</h3>";
            echo "<p>Corrija os erros acima e execute novamente.</p>";
        }
    }
}

// Executar instalador
$installer = new RessonanceInstaller();
$installer->run();
?>