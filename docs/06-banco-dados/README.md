# 💾 Sistema de Banco de Dados - Ressonance

## 📋 Visão Geral

O sistema de banco de dados do Ressonance é o coração da aplicação, armazenando todas as informações sobre músicas, artistas, álbuns, usuários e configurações. Utiliza MySQL com estrutura otimizada e queries eficientes.

## 🎯 Funcionalidades Principais

### 📊 **Estrutura de Dados**
- **Músicas** - Metadados e arquivos
- **Artistas** - Informações dos músicos
- **Álbuns** - Coleções organizadas
- **Usuários** - Perfis e preferências
- **Playlists** - Listas personalizadas
- **Sistema** - Configurações globais

### 🔄 **Operações Avançadas**
- Queries otimizadas com índices
- Relacionamentos complexos
- Transações seguras
- Cache de consultas
- Backup automático

### 🚀 **Auto-Configuração**
- Detecção automática de MySQL
- Criação automática de database
- Inicialização de schema
- Dados de exemplo
- Migração automática

## 🏗️ Arquitetura do Banco

### **Estrutura Principal**

```sql
Ressonance Database
├── artists          # Artistas/Músicos
├── albums           # Álbuns/Coleções
├── songs            # Músicas individuais
├── song_files       # Arquivos de áudio
├── playlists        # Listas de reprodução
├── playlist_songs   # Músicas nas playlists
├── users           # Usuários do sistema
└── system_config   # Configurações globais
```

### **Relacionamentos**

```
[Artists] 1---N [Albums] 1---N [Songs] 1---N [Song_Files]
    |                               |
    └─────────────1---N─────────────┘
    
[Users] 1---N [Playlists] N---N [Songs]
                    |
                    └── [Playlist_Songs]
```

## 📋 Schema Detalhado

### **Tabela: artists**
```sql
CREATE TABLE artists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(500),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Campos:**
- `id` - Identificador único
- `name` - Nome do artista
- `image` - URL da foto do artista
- `bio` - Biografia/descrição
- `created_at` - Data de criação
- `updated_at` - Última atualização

### **Tabela: albums**
```sql
CREATE TABLE albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist_id INT,
    image VARCHAR(500),
    release_date DATE,
    genre VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
    INDEX idx_title (title),
    INDEX idx_artist (artist_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Campos:**
- `id` - Identificador único
- `title` - Título do álbum
- `artist_id` - Referência ao artista
- `image` - Capa do álbum
- `release_date` - Data de lançamento
- `genre` - Gênero musical

### **Tabela: songs**
```sql
CREATE TABLE songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist_id INT NOT NULL,
    album_id INT,
    duration INT DEFAULT 0,
    file_path VARCHAR(500),
    image VARCHAR(500),
    plays INT DEFAULT 0,
    genre VARCHAR(100),
    track_number INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL,
    INDEX idx_title (title),
    INDEX idx_artist (artist_id),
    INDEX idx_album (album_id),
    INDEX idx_plays (plays)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Campos:**
- `id` - Identificador único
- `title` - Título da música
- `artist_id` - Referência ao artista
- `album_id` - Referência ao álbum (opcional)
- `duration` - Duração em segundos
- `file_path` - Caminho do arquivo
- `plays` - Contador de reproduções
- `track_number` - Número da faixa

### **Tabela: song_files**
```sql
CREATE TABLE song_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    song_id INT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_format VARCHAR(10) NOT NULL,
    file_size BIGINT,
    bitrate INT,
    uploaded_by VARCHAR(100),
    is_verified BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
    INDEX idx_song (song_id),
    INDEX idx_format (file_format)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**Campos:**
- `song_id` - Referência à música
- `file_path` - Caminho do arquivo
- `file_format` - Formato (mp3, flac, etc.)
- `file_size` - Tamanho em bytes
- `bitrate` - Taxa de bits
- `is_verified` - Arquivo verificado

## 🔧 Configuração e Inicialização

### **Auto-Detecção de MySQL**
```php
// database.php - Detecção automática
$hosts = ['localhost', '127.0.0.1', 'mysql'];
$users = ['root', 'mysql'];
$passes = ['', 'root', 'password'];

foreach ($hosts as $host) {
    foreach ($users as $user) {
        foreach ($passes as $pass) {
            try {
                $pdo = new PDO("mysql:host=$host", $user, $pass);
                // Conexão bem-sucedida
                break 3;
            } catch (Exception $e) {
                continue;
            }
        }
    }
}
```

### **Criação Automática do Database**
```php
// Criar database se não existir
$pdo->exec("CREATE DATABASE IF NOT EXISTS ressonance_music CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE ressonance_music");

// Verificar se precisa inicializar
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
if (count($tables) < 5) {
    initializeSchema();
}
```

### **Inicialização do Schema**
```php
function initializeSchema() {
    global $pdo;
    
    // Carregar schema SQL
    $schemaFile = __DIR__ . '/../../database/schema.sql';
    $sql = file_get_contents($schemaFile);
    
    // Executar statements
    $statements = explode(';', $sql);
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            $pdo->exec($statement);
        }
    }
}
```

## 📊 Queries Otimizadas

### **Classe DatabaseQueries**
```php
class DatabaseQueries {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    // Buscar músicas com informações completas
    public function getInitialSongs($limit = 6) {
        $stmt = $this->pdo->prepare("
            SELECT s.*, ar.name as artist_name,
                   GROUP_CONCAT(DISTINCT CONCAT(sf.file_format, ':', SUBSTRING_INDEX(sf.file_path, '/', -1)) SEPARATOR '|') as audio_files
            FROM songs s 
            JOIN artists ar ON s.artist_id = ar.id 
            LEFT JOIN song_files sf ON s.id = sf.song_id AND sf.is_verified = TRUE
            WHERE sf.file_path IS NOT NULL
            GROUP BY s.id
            ORDER BY s.plays DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Buscar álbuns com contagem de músicas
    public function getAllAlbums() {
        return $this->pdo->query("
            SELECT al.*, ar.name as artist_name,
                   COUNT(s.id) as song_count
            FROM albums al 
            JOIN artists ar ON al.artist_id = ar.id 
            LEFT JOIN songs s ON s.album_id = al.id
            GROUP BY al.id
            ORDER BY al.title ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

### **Queries com Performance**
```sql
-- Buscar top músicas com cache
SELECT s.*, ar.name as artist_name, 
       COALESCE(s.image, al.image, ar.image) as display_image
FROM songs s
JOIN artists ar ON s.artist_id = ar.id
LEFT JOIN albums al ON s.album_id = al.id
WHERE s.plays > 0
ORDER BY s.plays DESC, s.created_at DESC
LIMIT 10;

-- Buscar músicas por gênero com índice
SELECT s.*, ar.name as artist_name
FROM songs s
JOIN artists ar ON s.artist_id = ar.id
WHERE s.genre = 'Rock'
ORDER BY s.plays DESC;

-- Estatísticas do sistema
SELECT 
    (SELECT COUNT(*) FROM songs) as total_songs,
    (SELECT COUNT(*) FROM artists) as total_artists,
    (SELECT COUNT(*) FROM albums) as total_albums,
    (SELECT SUM(plays) FROM songs) as total_plays;
```

## 🔄 Operações CRUD

### **Adicionar Música**
```php
public function addSong($title, $artistId, $albumId, $duration, $filePath) {
    try {
        $this->pdo->beginTransaction();
        
        // Inserir música
        $stmt = $this->pdo->prepare("
            INSERT INTO songs (title, artist_id, album_id, duration, file_path) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $artistId, $albumId, $duration, $filePath]);
        $songId = $this->pdo->lastInsertId();
        
        // Adicionar arquivo
        $this->addSongFile($songId, $filePath, 'mp3', filesize($filePath), 'system');
        
        $this->pdo->commit();
        return $songId;
        
    } catch (Exception $e) {
        $this->pdo->rollback();
        throw $e;
    }
}
```

### **Atualizar Estatísticas**
```php
public function incrementPlayCount($songId) {
    $stmt = $this->pdo->prepare("
        UPDATE songs 
        SET plays = plays + 1, 
            updated_at = CURRENT_TIMESTAMP 
        WHERE id = ?
    ");
    return $stmt->execute([$songId]);
}
```

### **Busca Avançada**
```php
public function searchSongs($query, $filters = []) {
    $sql = "
        SELECT s.*, ar.name as artist_name, al.title as album_title
        FROM songs s
        JOIN artists ar ON s.artist_id = ar.id
        LEFT JOIN albums al ON s.album_id = al.id
        WHERE (s.title LIKE ? OR ar.name LIKE ?)
    ";
    
    $params = ["%$query%", "%$query%"];
    
    // Adicionar filtros
    if (!empty($filters['genre'])) {
        $sql .= " AND s.genre = ?";
        $params[] = $filters['genre'];
    }
    
    if (!empty($filters['artist_id'])) {
        $sql .= " AND s.artist_id = ?";
        $params[] = $filters['artist_id'];
    }
    
    $sql .= " ORDER BY s.plays DESC LIMIT 50";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

## 📈 Índices e Performance

### **Índices Estratégicos**
```sql
-- Índices para busca rápida
CREATE INDEX idx_songs_title ON songs(title);
CREATE INDEX idx_songs_artist ON songs(artist_id);
CREATE INDEX idx_songs_plays ON songs(plays DESC);
CREATE INDEX idx_songs_genre ON songs(genre);

-- Índices compostos
CREATE INDEX idx_songs_artist_album ON songs(artist_id, album_id);
CREATE INDEX idx_songs_plays_created ON songs(plays DESC, created_at DESC);

-- Índices para texto
CREATE FULLTEXT INDEX idx_songs_search ON songs(title);
CREATE FULLTEXT INDEX idx_artists_search ON artists(name);
```

### **Otimização de Queries**
```sql
-- Usar EXPLAIN para analisar queries
EXPLAIN SELECT s.*, ar.name 
FROM songs s 
JOIN artists ar ON s.artist_id = ar.id 
WHERE s.plays > 100 
ORDER BY s.plays DESC;

-- Otimizar com LIMIT
SELECT s.* FROM songs s 
WHERE s.genre = 'Rock' 
ORDER BY s.plays DESC 
LIMIT 20;

-- Usar EXISTS ao invés de IN
SELECT ar.* FROM artists ar 
WHERE EXISTS (
    SELECT 1 FROM songs s 
    WHERE s.artist_id = ar.id 
    AND s.plays > 1000
);
```

## 🔒 Segurança e Integridade

### **Prepared Statements**
```php
// SEMPRE usar prepared statements
$stmt = $pdo->prepare("SELECT * FROM songs WHERE artist_id = ? AND genre = ?");
$stmt->execute([$artistId, $genre]);

// NUNCA fazer isso:
// $sql = "SELECT * FROM songs WHERE artist_id = $artistId"; // VULNERÁVEL!
```

### **Validação de Dados**
```php
function validateSongData($data) {
    $errors = [];
    
    if (empty($data['title']) || strlen($data['title']) > 255) {
        $errors[] = 'Título inválido';
    }
    
    if (!is_numeric($data['artist_id']) || $data['artist_id'] <= 0) {
        $errors[] = 'Artista inválido';
    }
    
    if (!empty($data['duration']) && (!is_numeric($data['duration']) || $data['duration'] < 0)) {
        $errors[] = 'Duração inválida';
    }
    
    return $errors;
}
```

### **Transações Seguras**
```php
function transferSongToAlbum($songId, $newAlbumId) {
    try {
        $this->pdo->beginTransaction();
        
        // Verificar se música existe
        $stmt = $this->pdo->prepare("SELECT id FROM songs WHERE id = ?");
        $stmt->execute([$songId]);
        if (!$stmt->fetch()) {
            throw new Exception('Música não encontrada');
        }
        
        // Verificar se álbum existe
        $stmt = $this->pdo->prepare("SELECT id FROM albums WHERE id = ?");
        $stmt->execute([$newAlbumId]);
        if (!$stmt->fetch()) {
            throw new Exception('Álbum não encontrado');
        }
        
        // Atualizar música
        $stmt = $this->pdo->prepare("UPDATE songs SET album_id = ? WHERE id = ?");
        $stmt->execute([$newAlbumId, $songId]);
        
        $this->pdo->commit();
        return true;
        
    } catch (Exception $e) {
        $this->pdo->rollback();
        throw $e;
    }
}
```

## 📊 Backup e Manutenção

### **Backup Automático**
```php
function createBackup() {
    $backupFile = 'backups/ressonance_' . date('Y-m-d_H-i-s') . '.sql';
    
    $command = sprintf(
        'mysqldump -h%s -u%s -p%s %s > %s',
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME,
        $backupFile
    );
    
    exec($command, $output, $returnCode);
    
    if ($returnCode === 0) {
        // Comprimir backup
        exec("gzip $backupFile");
        return $backupFile . '.gz';
    }
    
    throw new Exception('Falha no backup');
}
```

### **Limpeza de Dados**
```php
function cleanupOldData() {
    // Remover logs antigos (30 dias)
    $this->pdo->exec("
        DELETE FROM system_logs 
        WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)
    ");
    
    // Limpar cache de thumbnails
    $this->pdo->exec("
        DELETE FROM image_cache 
        WHERE last_accessed < DATE_SUB(NOW(), INTERVAL 7 DAY)
    ");
    
    // Otimizar tabelas
    $tables = ['songs', 'artists', 'albums', 'playlists'];
    foreach ($tables as $table) {
        $this->pdo->exec("OPTIMIZE TABLE $table");
    }
}
```

## 📊 Monitoramento e Estatísticas

### **Métricas do Sistema**
```php
function getDatabaseStats() {
    $stats = [];
    
    // Contadores básicos
    $stats['songs'] = $this->pdo->query("SELECT COUNT(*) FROM songs")->fetchColumn();
    $stats['artists'] = $this->pdo->query("SELECT COUNT(*) FROM artists")->fetchColumn();
    $stats['albums'] = $this->pdo->query("SELECT COUNT(*) FROM albums")->fetchColumn();
    
    // Estatísticas de uso
    $stats['total_plays'] = $this->pdo->query("SELECT SUM(plays) FROM songs")->fetchColumn();
    $stats['avg_plays'] = $this->pdo->query("SELECT AVG(plays) FROM songs")->fetchColumn();
    
    // Top artistas
    $stats['top_artists'] = $this->pdo->query("
        SELECT ar.name, SUM(s.plays) as total_plays
        FROM artists ar
        JOIN songs s ON ar.id = s.artist_id
        GROUP BY ar.id
        ORDER BY total_plays DESC
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    return $stats;
}
```

### **Performance Monitoring**
```php
function logSlowQuery($query, $executionTime) {
    if ($executionTime > 1.0) { // Queries > 1 segundo
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'query' => $query,
            'execution_time' => $executionTime,
            'memory_usage' => memory_get_usage(true)
        ];
        
        file_put_contents('logs/slow_queries.log', json_encode($logEntry) . "\n", FILE_APPEND);
    }
}
```

## 🔮 Funcionalidades Futuras

- **Sharding** - Distribuição de dados
- **Read Replicas** - Réplicas de leitura
- **Full-Text Search** - Busca avançada
- **Data Analytics** - Análise de dados
- **Real-time Sync** - Sincronização em tempo real
- **GraphQL API** - API moderna

---

**📚 Próximos Passos:**
- [Configuração](../03-configuracao/) - Setup do banco
- [Sistema de Áudio](../04-sistema-audio/) - Integração com músicas
- [Segurança](../08-seguranca/) - Proteção de dados