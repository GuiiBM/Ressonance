<?php
// Verificador completo do sistema Ressonance
require_once 'app/config/environment.php';

class SystemChecker {
    private $config;
    private $issues = [];
    private $warnings = [];
    private $success = [];
    
    public function __construct() {
        $this->config = Environment::detect();
    }
    
    public function runFullCheck() {
        echo "<!DOCTYPE html><html><head><title>Verificação do Sistema</title>";
        echo "<style>body{font-family:Arial;margin:40px;} .ok{color:green;} .error{color:red;} .warning{color:orange;} .info{color:blue;}</style></head><body>";
        echo "<h1>🔍 Verificação Completa do Sistema Ressonance</h1>";
        
        $this->checkEnvironment();
        $this->checkDatabase();
        $this->checkFiles();
        $this->checkPermissions();
        $this->checkConfiguration();
        $this->showSummary();
        
        echo "</body></html>";
    }
    
    private function checkEnvironment() {
        echo "<h2>1. Ambiente</h2>";
        
        echo "<div class='ok'>✅ PHP " . PHP_VERSION . "</div>";
        echo "<div class='info'>ℹ️ Sistema: " . PHP_OS_FAMILY . "</div>";
        echo "<div class='info'>ℹ️ Servidor: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido') . "</div>";
        echo "<div class='ok'>✅ Caminho Base: {$this->config['BASE_URL']}</div>";
        echo "<div class='ok'>✅ Diretório Raiz: {$this->config['ROOT_PATH']}</div>";
        
        $extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring', 'fileinfo'];
        foreach ($extensions as $ext) {
            if (extension_loaded($ext)) {
                echo "<div class='ok'>✅ Extensão $ext</div>";
            } else {
                echo "<div class='error'>❌ Extensão $ext não encontrada</div>";
                $this->issues[] = "Extensão PHP '$ext' necessária";
            }
        }
    }
    
    private function checkDatabase() {
        echo "<h2>2. Banco de Dados</h2>";
        
        try {
            $pdo = new PDO(
                "mysql:host={$this->config['DB_HOST']};charset=utf8mb4",
                $this->config['DB_USER'],
                $this->config['DB_PASS']
            );
            echo "<div class='ok'>✅ Conexão MySQL: {$this->config['DB_HOST']}</div>";
            
            // Verificar database
            $databases = $pdo->query("SHOW DATABASES")->fetchAll(PDO::FETCH_COLUMN);
            if (in_array($this->config['DB_NAME'], $databases)) {
                echo "<div class='ok'>✅ Database: {$this->config['DB_NAME']}</div>";
                
                // Conectar ao database específico
                $pdo->exec("USE {$this->config['DB_NAME']}");\n                \n                // Verificar tabelas\n                $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);\n                $required = ['artists', 'albums', 'songs', 'playlists', 'system_config'];\n                \n                foreach ($required as $table) {\n                    if (in_array($table, $tables)) {\n                        $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();\n                        echo "<div class='ok'>✅ Tabela $table ($count registros)</div>";\n                    } else {\n                        echo "<div class='error'>❌ Tabela $table não encontrada</div>";\n                        $this->issues[] = "Tabela '$table' não existe";\n                    }\n                }\n                \n            } else {\n                echo "<div class='error'>❌ Database {$this->config['DB_NAME']} não encontrado</div>";\n                $this->issues[] = "Database não existe";\n            }\n            \n        } catch (Exception $e) {\n            echo "<div class='error'>❌ Erro de conexão: " . $e->getMessage() . "</div>";\n            $this->issues[] = "Não foi possível conectar ao banco";\n        }\n    }\n    \n    private function checkFiles() {\n        echo "<h2>3. Arquivos Essenciais</h2>";\n        \n        $files = [\n            'index.php' => 'Página inicial',\n            'audio.php' => 'Servidor de áudio',\n            'image.php' => 'Servidor de imagens',\n            'app/config/database.php' => 'Configuração do banco',\n            'app/config/paths.php' => 'Configuração de caminhos',\n            'public/assets/css/styles.css' => 'CSS principal',\n            'public/assets/js/player-core.js' => 'Player de música'\n        ];\n        \n        foreach ($files as $file => $desc) {\n            if (file_exists($file)) {\n                $size = filesize($file);\n                echo "<div class='ok'>✅ $desc ($file) - " . number_format($size) . " bytes</div>";\n            } else {\n                echo "<div class='error'>❌ $desc ($file) não encontrado</div>";\n                $this->issues[] = "Arquivo '$file' não encontrado";\n            }\n        }\n    }\n    \n    private function checkPermissions() {\n        echo "<h2>4. Permissões</h2>";\n        \n        $dirs = [\n            'storage' => 'Armazenamento',\n            'storage/uploads' => 'Uploads',\n            'storage/uploads/audio' => 'Áudio',\n            'audio' => 'Pasta de áudio',\n            'images' => 'Imagens',\n            'public/assets' => 'Assets públicos'\n        ];\n        \n        foreach ($dirs as $dir => $desc) {\n            if (is_dir($dir)) {\n                if (is_writable($dir)) {\n                    echo "<div class='ok'>✅ $desc ($dir) - Gravável</div>";\n                } else {\n                    echo "<div class='warning'>⚠️ $desc ($dir) - Sem permissão de escrita</div>";\n                    $this->warnings[] = "Diretório '$dir' pode precisar de permissão de escrita";\n                }\n            } else {\n                echo "<div class='warning'>⚠️ $desc ($dir) - Não existe</div>";\n                $this->warnings[] = "Diretório '$dir' não existe";\n            }\n        }\n    }\n    \n    private function checkConfiguration() {\n        echo "<h2>5. Configuração</h2>";\n        \n        // Verificar se foi instalado\n        if (file_exists('.ressonance_installed')) {\n            $install_info = json_decode(file_get_contents('.ressonance_installed'), true);\n            echo "<div class='ok'>✅ Sistema instalado em: " . $install_info['installed_at'] . "</div>";\n            echo "<div class='ok'>✅ Versão: " . $install_info['version'] . "</div>";\n        } else {\n            echo "<div class='warning'>⚠️ Sistema não foi instalado via install.php</div>";\n            $this->warnings[] = "Execute install.php para configuração completa";\n        }\n        \n        // Verificar paths.php\n        if (file_exists('app/config/paths.php')) {\n            $content = file_get_contents('app/config/paths.php');\n            if (strpos($content, $this->config['BASE_URL']) !== false) {\n                echo "<div class='ok'>✅ Caminhos configurados corretamente</div>";\n            } else {\n                echo "<div class='warning'>⚠️ Caminhos podem estar desatualizados</div>";\n                $this->warnings[] = "Execute fix-paths.php para atualizar caminhos";\n            }\n        }\n        \n        // Verificar config.js\n        if (file_exists('public/assets/js/config.js')) {\n            echo "<div class='ok'>✅ Configuração JavaScript existe</div>";\n        } else {\n            echo "<div class='warning'>⚠️ config.js não encontrado</div>";\n            $this->warnings[] = "Configuração JavaScript pode estar faltando";\n        }\n    }\n    \n    private function showSummary() {\n        echo "<h2>📊 Resumo</h2>";\n        \n        if (empty($this->issues)) {\n            echo "<div class='ok'><h3>🎉 Sistema Funcionando Perfeitamente!</h3></div>";\n            echo "<p><strong><a href='index.php'>🎵 Acessar Ressonance</a></strong></p>";\n        } else {\n            echo "<div class='error'><h3>❌ Problemas Encontrados (" . count($this->issues) . ")</h3></div>";\n            foreach ($this->issues as $issue) {\n                echo "<div class='error'>• $issue</div>";\n            }\n            echo "<p><strong><a href='install.php'>🔧 Executar Instalador</a></strong></p>";\n        }\n        \n        if (!empty($this->warnings)) {\n            echo "<div class='warning'><h3>⚠️ Avisos (" . count($this->warnings) . ")</h3></div>";\n            foreach ($this->warnings as $warning) {\n                echo "<div class='warning'>• $warning</div>";\n            }\n        }\n        \n        echo "<hr><p><a href='fix-paths.php'>🔧 Corrigir Caminhos</a> | ";\n        echo "<a href='health-check.php'>❤️ Verificar Saúde</a> | ";\n        echo "<a href='verify-system.php'>🔍 Verificar Sistema</a></p>";\n    }\n}\n\n// Executar verificação\n$checker = new SystemChecker();\n$checker->runFullCheck();\n?>