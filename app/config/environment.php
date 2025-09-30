<?php
// Sistema de detecção automática de ambiente
class Environment {
    private static $config = null;
    
    public static function detect() {
        if (self::$config !== null) {
            return self::$config;
        }
        
        $projectRoot = self::findProjectRoot();
        $webPath = self::detectWebPath();
        
        self::$config = [
            'ROOT_PATH' => $projectRoot,
            'BASE_URL' => $webPath,
            'DB_HOST' => self::detectDbHost(),
            'DB_USER' => self::detectDbUser(),
            'DB_PASS' => self::detectDbPass(),
            'DB_NAME' => 'ressonance_music'
        ];
        
        self::saveConfig();
        return self::$config;
    }
    
    private static function findProjectRoot() {
        $current = dirname(dirname(dirname(__FILE__)));
        $indicators = ['fix-paths.php', 'audio.php', 'app/config'];
        
        while ($current !== dirname($current)) {
            foreach ($indicators as $indicator) {
                if (file_exists($current . '/' . $indicator)) {
                    return $current;
                }
            }
            $current = dirname($current);
        }
        
        return dirname(dirname(dirname(__FILE__)));
    }
    
    private static function detectWebPath() {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
        
        // Método 1: Baseado no script atual
        if ($scriptName) {
            $pathParts = explode('/', trim($scriptName, '/'));
            foreach ($pathParts as $i => $part) {
                if (strtolower($part) === 'ressonance') {
                    return '/' . implode('/', array_slice($pathParts, 0, $i + 2));
                }
            }
        }
        
        // Método 2: Baseado no diretório físico
        $projectRoot = self::findProjectRoot();
        if ($documentRoot && $projectRoot) {
            $webPath = str_replace($documentRoot, '', $projectRoot);
            $webPath = str_replace('\\', '/', $webPath);
            return $webPath;
        }
        
        return '/Ressonance/Ressonance';
    }
    
    private static function detectDbHost() {
        $hosts = ['localhost', '127.0.0.1', 'mysql', 'db', 'mariadb'];
        foreach ($hosts as $host) {
            if (self::testConnection($host, 'root', '')) {
                return $host;
            }
        }
        return 'localhost';
    }
    
    private static function detectDbUser() {
        $users = ['root', 'mysql', 'admin'];
        foreach ($users as $user) {
            if (self::testConnection(self::detectDbHost(), $user, '')) {
                return $user;
            }
        }
        return 'root';
    }
    
    private static function detectDbPass() {
        $passwords = ['', 'root', 'password', 'admin'];
        $host = self::detectDbHost();
        $user = self::detectDbUser();
        
        foreach ($passwords as $pass) {
            if (self::testConnection($host, $user, $pass)) {
                return $pass;
            }
        }
        return '';
    }
    
    private static function testConnection($host, $user, $pass) {
        try {
            $pdo = new PDO("mysql:host=$host", $user, $pass, [
                PDO::ATTR_TIMEOUT => 3,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    private static function saveConfig() {
        $configFile = self::$config['ROOT_PATH'] . '/.ressonance_config';
        file_put_contents($configFile, json_encode(self::$config, JSON_PRETTY_PRINT));
    }
}
?>