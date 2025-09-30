<?php
// Bootstrap universal - funciona em qualquer ambiente
class UniversalBootstrap {
    private static $errors = [];
    
    public static function init() {
        try {
            self::checkPHPVersion();
            self::checkExtensions();
            self::setupEnvironment();
            self::initializeDatabase();
            self::createStructure();
            self::configureSystem();
            return true;
        } catch (Exception $e) {
            self::$errors[] = $e->getMessage();
            return false;
        }
    }
    
    private static function checkPHPVersion() {
        if (version_compare(PHP_VERSION, '7.4.0') < 0) {
            throw new Exception('PHP 7.4+ necessário. Atual: ' . PHP_VERSION);
        }
    }
    
    private static function checkExtensions() {
        $required = ['pdo', 'json', 'mbstring'];
        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                throw new Exception("Extensão '$ext' não encontrada");
            }
        }
    }
    
    private static function setupEnvironment() {
        $root = self::findRoot();
        $webPath = self::detectWebPath();
        $dbConfig = self::detectDatabase();
        
        define('RESSONANCE_ROOT', $root);
        define('RESSONANCE_URL', $webPath);
        define('RESSONANCE_DB_HOST', $dbConfig['host']);
        define('RESSONANCE_DB_USER', $dbConfig['user']);
        define('RESSONANCE_DB_PASS', $dbConfig['pass']);
        define('RESSONANCE_DB_NAME', 'ressonance_music');
    }
    
    private static function findRoot() {
        $current = __DIR__;
        $indicators = ['audio.php', 'image.php', 'app'];
        
        while ($current !== dirname($current)) {
            $found = 0;
            foreach ($indicators as $indicator) {
                if (file_exists($current . DIRECTORY_SEPARATOR . $indicator)) {
                    $found++;
                }
            }
            if ($found >= 2) return $current;
            $current = dirname($current);
        }
        return __DIR__;
    }
    
    private static function detectWebPath() {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        if (preg_match('/(.+)\/[^\/]*\.php$/', $script, $matches)) {
            return $matches[1];
        }
        return '/Ressonance/Ressonance';
    }
    
    private static function detectDatabase() {
        $configs = [
            ['localhost', 'root', ''],
            ['127.0.0.1', 'root', ''],
            ['localhost', 'mysql', ''],
            ['mysql', 'root', ''],
            ['db', 'root', '']
        ];
        
        foreach ($configs as $config) {
            if (self::testDB($config[0], $config[1], $config[2])) {
                return ['host' => $config[0], 'user' => $config[1], 'pass' => $config[2]];
            }
        }
        
        // Se não encontrar, usar padrão
        return ['host' => 'localhost', 'user' => 'root', 'pass' => ''];
    }
    
    private static function testDB($host, $user, $pass) {
        try {
            $pdo = new PDO("mysql:host=$host", $user, $pass, [
                PDO::ATTR_TIMEOUT => 2,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private static function initializeDatabase() {
        try {
            $pdo = new PDO("mysql:host=" . RESSONANCE_DB_HOST, RESSONANCE_DB_USER, RESSONANCE_DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Criar database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . RESSONANCE_DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE " . RESSONANCE_DB_NAME);
            
            // Verificar se precisa criar tabelas
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            if (count($tables) < 5) {
                self::createTables($pdo);
            }
            
        } catch (Exception $e) {
            throw new Exception('Erro no banco: ' . $e->getMessage());
        }
    }
    
    private static function createTables($pdo) {
        $sql = "
        CREATE TABLE IF NOT EXISTS artists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            image VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS albums (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            artist_id INT,
            image VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS songs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            artist_id INT NOT NULL,
            album_id INT,
            duration INT DEFAULT 0,
            file_path VARCHAR(500),
            image VARCHAR(500),
            plays INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
            FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS playlists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            image VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        CREATE TABLE IF NOT EXISTS system_config (
            id INT AUTO_INCREMENT PRIMARY KEY,
            config_key VARCHAR(100) NOT NULL UNIQUE,
            config_value TEXT,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        
        INSERT IGNORE INTO system_config (config_key, config_value) VALUES
        ('site_name', 'Ressonance'),
        ('version', '1.0.0'),
        ('installed_at', NOW());
        
        INSERT IGNORE INTO artists (id, name) VALUES (1, 'Artista Exemplo');
        INSERT IGNORE INTO albums (id, title, artist_id) VALUES (1, 'Álbum Demo', 1);
        INSERT IGNORE INTO playlists (id, name, description) VALUES (1, 'Favoritas', 'Músicas favoritas');
        ";
        
        $statements = array_filter(explode(';', $sql));
        foreach ($statements as $stmt) {
            if (trim($stmt)) $pdo->exec($stmt);
        }
    }
    
    private static function createStructure() {
        $dirs = [
            'storage/uploads/audio',
            'storage/logs',
            'audio',
            'images',
            'public/assets/css',
            'public/assets/js',
            'public/assets/images'
        ];
        
        foreach ($dirs as $dir) {
            $path = RESSONANCE_ROOT . DIRECTORY_SEPARATOR . $dir;
            if (!is_dir($path)) {
                @mkdir($path, 0755, true);
            }
        }
    }
    
    private static function configureSystem() {
        // Atualizar paths.php
        $pathsFile = RESSONANCE_ROOT . '/app/config/paths.php';
        if (file_exists($pathsFile)) {
            $content = file_get_contents($pathsFile);
            $content = preg_replace("/define\('BASE_URL', '[^']*'\);/", "define('BASE_URL', '" . RESSONANCE_URL . "');", $content);
            file_put_contents($pathsFile, $content);
        }
        
        // Criar config.js
        $configJs = 'window.APP_CONFIG = ' . json_encode([
            'API_BASE_URL' => RESSONANCE_URL . '/app/controllers/api',
            'BASE_URL' => RESSONANCE_URL,
            'IMAGE_URL' => RESSONANCE_URL . '/image.php'
        ]) . ';';
        
        $jsPath = RESSONANCE_ROOT . '/public/assets/js';
        if (!is_dir($jsPath)) @mkdir($jsPath, 0755, true);
        file_put_contents($jsPath . '/config.js', $configJs);
        
        // Marcar como instalado
        file_put_contents(RESSONANCE_ROOT . '/.ressonance_ready', json_encode([
            'timestamp' => time(),
            'version' => '1.0.0',
            'url' => RESSONANCE_URL
        ]));
    }
    
    public static function getErrors() {
        return self::$errors;
    }
}
?>