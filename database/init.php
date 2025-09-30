<?php
// Sistema completo de inicialização do banco
class DatabaseInitializer {
    private $pdo;
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
    }
    
    public function initialize() {
        try {
            // Conectar sem database
            $this->pdo = new PDO(
                "mysql:host={$this->config['DB_HOST']};charset=utf8mb4", 
                $this->config['DB_USER'], 
                $this->config['DB_PASS'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
            
            // Criar database
            $this->createDatabase();
            
            // Usar database
            $this->pdo->exec("USE {$this->config['DB_NAME']}");
            
            // Executar schema
            $this->executeSchema();
            
            // Verificar integridade
            $this->verifyTables();
            
            return ['success' => true, 'message' => 'Database inicializado com sucesso'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
    
    private function createDatabase() {
        $sql = "CREATE DATABASE IF NOT EXISTS {$this->config['DB_NAME']} 
                CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        $this->pdo->exec($sql);
    }
    
    private function executeSchema() {
        $schemaFile = dirname(__FILE__) . '/schema.sql';
        if (!file_exists($schemaFile)) {
            throw new Exception('Schema SQL não encontrado');
        }
        
        $sql = file_get_contents($schemaFile);
        $statements = explode(';', $sql);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement) && !preg_match('/^--/', $statement)) {
                $this->pdo->exec($statement);
            }
        }
    }
    
    private function verifyTables() {
        $requiredTables = ['artists', 'albums', 'songs', 'song_files', 'playlists', 'playlist_songs', 'system_config'];
        $existingTables = $this->pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($requiredTables as $table) {
            if (!in_array($table, $existingTables)) {
                throw new Exception("Tabela '$table' não foi criada");
            }
        }
    }
    
    public function getConnection() {
        if (!$this->pdo) {
            $this->pdo = new PDO(
                "mysql:host={$this->config['DB_HOST']};dbname={$this->config['DB_NAME']};charset=utf8mb4",
                $this->config['DB_USER'],
                $this->config['DB_PASS'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return $this->pdo;
    }
}
?>