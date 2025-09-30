# 🗄️ Configuração do Banco de Dados - Inicialização Automática

## 🔍 O que é

O sistema de banco de dados do Ressonance é **completamente automático**. Na primeira vez que alguém acessa o site, ele cria automaticamente todas as tabelas, relacionamentos e configurações necessárias. É como ter um **instalador mágico**!

## 📂 Onde está

### Arquivos Principais
- **📄 `app/config/database.php`** - Conexão e configurações
- **📄 `app/config/init-database.php`** - Criação automática das tabelas
- **📄 `app/views/components/database-queries.php`** - Consultas organizadas

### Configurações
```php
// 📄 app/config/database.php
define('DB_HOST', 'localhost');      // 🏠 Servidor do banco
define('DB_USER', 'root');           // 👤 Usuário
define('DB_PASS', '');               // 🔐 Senha (vazia no XAMPP)
define('DB_NAME', 'ressonance_music'); // 🗄️ Nome do banco
```

## ⚙️ Como Funciona (Explicação Simples)

### 🎯 Fluxo de Inicialização

```
1. 👤 Usuário acessa o site pela primeira vez
   ↓
2. 🔍 Sistema verifica: "O banco existe?"
   ❌ Não existe
   ↓
3. 🏗️ Cria automaticamente:
   - Banco de dados: ressonance_music
   - Tabela: artists (artistas)
   - Tabela: albums (álbuns)
   - Tabela: songs (músicas)
   - Tabela: song_files (arquivos de música)
   - Tabela: playlists (playlists)
   ↓
4. 🔗 Configura relacionamentos entre tabelas
   ↓
5. ✅ Tudo pronto! Site funciona perfeitamente
```

### 🎭 Analogia Simples
É como **montar uma estante automaticamente**:
- Você compra uma estante desmontada (código do site)
- Na primeira vez que precisa usar, ela se monta sozinha
- Cria todas as prateleiras (tabelas) necessárias
- Organiza tudo nos lugares certos
- Você só precisa começar a usar!

## 🔧 Detalhes Técnicos

### 📄 database.php - Conexão Principal

```php
<?php
// ⚙️ Configurações de conexão
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ressonance_music');

// 🚀 Inicializar banco na primeira execução
require_once __DIR__ . '/init-database.php';
initializeDatabase();  // 🎯 Chama a função mágica

// 🔌 Conexão com o banco
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}

// 🎵 Funções auxiliares para buscar dados
function getRecentAlbums($pdo, $limit = 4) {
    $stmt = $pdo->prepare("
        SELECT a.*, ar.name as artist_name 
        FROM albums a 
        JOIN artists ar ON a.artist_id = ar.id 
        ORDER BY a.created_at DESC 
        LIMIT :limit
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
```

### 📄 init-database.php - O Criador Automático

```php
<?php
function initializeDatabase() {
    try {
        // 🔌 Conecta SEM especificar o banco (para criar se não existir)
        $pdo_init = new PDO("mysql:host=" . DB_HOST . ";charset=utf8", DB_USER, DB_PASS);
        $pdo_init->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // 🏗️ Cria o banco se não existir
        $pdo_init->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8 COLLATE utf8_general_ci");
        $pdo_init->exec("USE " . DB_NAME);
        
        // 🔍 Verifica se as tabelas já existem
        $tables = $pdo_init->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            return "Database já inicializado";  // ✅ Já existe, não faz nada
        }
        
        // 🏗️ Cria todas as tabelas de uma vez
        $sql = "
        CREATE TABLE artists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            image VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE albums (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            artist_id INT,
            image VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE
        );
        
        CREATE TABLE songs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            artist_id INT NOT NULL,
            album_id INT,
            duration INT,
            file_path VARCHAR(500),
            image VARCHAR(500),
            plays INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
            FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL
        );
        
        CREATE TABLE song_files (
            id INT AUTO_INCREMENT PRIMARY KEY,
            song_id INT NOT NULL,
            file_path VARCHAR(500) NOT NULL,
            file_format VARCHAR(10) NOT NULL,
            file_size BIGINT,
            uploaded_by VARCHAR(100),
            is_verified BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE
        );
        
        CREATE TABLE playlists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            image VARCHAR(500),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        ";
        
        // 🚀 Executa todas as criações
        $pdo_init->exec($sql);
        
        return "Database inicializado com sucesso";
        
    } catch(PDOException $e) {
        return "Erro ao inicializar database: " . $e->getMessage();
    }
}
?>
```

## 🗄️ Estrutura das Tabelas

### 🎤 Tabela: artists (Artistas)
```sql
CREATE TABLE artists (
    id INT AUTO_INCREMENT PRIMARY KEY,    -- 🆔 ID único do artista
    name VARCHAR(255) NOT NULL,           -- 🎤 Nome do artista
    image VARCHAR(500),                   -- 🖼️ Foto do artista
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- 📅 Quando foi criado
);
```

**Exemplo de dados**:
```
id | name           | image                    | created_at
1  | Taylor Swift   | image.php?f=taylor.jpg   | 2024-01-15 10:30:00
2  | Ed Sheeran     | image.php?f=ed.jpg       | 2024-01-15 11:00:00
```

### 💿 Tabela: albums (Álbuns)
```sql
CREATE TABLE albums (
    id INT AUTO_INCREMENT PRIMARY KEY,    -- 🆔 ID único do álbum
    title VARCHAR(255) NOT NULL,          -- 💿 Nome do álbum
    artist_id INT,                        -- 🔗 Liga ao artista
    image VARCHAR(500),                   -- 🖼️ Capa do álbum
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 📅 Quando foi criado
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE  -- 🔗 Relacionamento
);
```

**Exemplo de dados**:
```
id | title      | artist_id | image                     | created_at
1  | 1989       | 1         | image.php?f=1989.jpg     | 2024-01-15 12:00:00
2  | ÷ (Divide) | 2         | image.php?f=divide.jpg   | 2024-01-15 12:30:00
```

### 🎵 Tabela: songs (Músicas)
```sql
CREATE TABLE songs (
    id INT AUTO_INCREMENT PRIMARY KEY,    -- 🆔 ID único da música
    title VARCHAR(255) NOT NULL,          -- 🎵 Nome da música
    artist_id INT NOT NULL,               -- 🔗 Liga ao artista
    album_id INT,                         -- 🔗 Liga ao álbum (opcional)
    duration INT,                         -- ⏱️ Duração em segundos
    file_path VARCHAR(500),               -- 📂 Caminho do arquivo (legado)
    image VARCHAR(500),                   -- 🖼️ Capa da música
    plays INT DEFAULT 0,                  -- 📊 Quantas vezes foi tocada
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,  -- 📅 Quando foi criada
    FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE,
    FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL
);
```

### 📁 Tabela: song_files (Arquivos de Música)
```sql
CREATE TABLE song_files (
    id INT AUTO_INCREMENT PRIMARY KEY,    -- 🆔 ID único do arquivo
    song_id INT NOT NULL,                 -- 🔗 Liga à música
    file_path VARCHAR(500) NOT NULL,      -- 📂 Nome do arquivo
    file_format VARCHAR(10) NOT NULL,     -- 🎼 Formato (mp3, flac, etc.)
    file_size BIGINT,                     -- 📏 Tamanho em bytes
    uploaded_by VARCHAR(100),             -- 👤 Quem fez upload
    is_verified BOOLEAN DEFAULT TRUE,     -- ✅ Arquivo verificado
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE
);
```

**Exemplo de dados**:
```
id | song_id | file_path           | file_format | file_size | is_verified
1  | 1       | 13_1758990614.mp3   | mp3         | 5242880   | 1
2  | 1       | 13_1758990614.flac  | flac        | 25165824  | 1
3  | 2       | 14_1759003598.mp3   | mp3         | 4194304   | 1
```

### 🎼 Tabela: playlists (Playlists)
```sql
CREATE TABLE playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,    -- 🆔 ID único da playlist
    name VARCHAR(255) NOT NULL,           -- 🎼 Nome da playlist
    description TEXT,                     -- 📝 Descrição
    image VARCHAR(500),                   -- 🖼️ Capa da playlist
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP  -- 📅 Quando foi criada
);
```

## 🔗 Relacionamentos Entre Tabelas

### 🎯 Como as Tabelas se Conectam

```
👤 ARTISTS (Artistas)
    ↓ (1 artista pode ter vários álbuns)
💿 ALBUMS (Álbuns)
    ↓ (1 álbum pode ter várias músicas)
🎵 SONGS (Músicas)
    ↓ (1 música pode ter vários arquivos)
📁 SONG_FILES (Arquivos)
```

### 🔍 Consulta Completa (Exemplo)
```sql
-- 🎯 Buscar música com todas as informações
SELECT 
    s.title as song_title,           -- 🎵 Nome da música
    ar.name as artist_name,          -- 🎤 Nome do artista
    al.title as album_title,         -- 💿 Nome do álbum
    GROUP_CONCAT(sf.file_format) as formats  -- 🎼 Formatos disponíveis
FROM songs s
JOIN artists ar ON s.artist_id = ar.id
LEFT JOIN albums al ON s.album_id = al.id
LEFT JOIN song_files sf ON s.id = sf.song_id
WHERE s.id = 1
GROUP BY s.id;
```

**Resultado**:
```
song_title | artist_name  | album_title | formats
Shake It Off | Taylor Swift | 1989       | mp3,flac
```

## 🔒 Segurança e Integridade

### 🛡️ Chaves Estrangeiras (Foreign Keys)

**O que fazem**: Garantem que os dados sejam consistentes

```sql
-- 🔗 Se deletar um artista, deleta todos os álbuns dele
FOREIGN KEY (artist_id) REFERENCES artists(id) ON DELETE CASCADE

-- 🔗 Se deletar um álbum, as músicas ficam sem álbum (não são deletadas)
FOREIGN KEY (album_id) REFERENCES albums(id) ON DELETE SET NULL
```

**Exemplo prático**:
```sql
-- ❌ Isso NÃO funciona (artista não existe):
INSERT INTO albums (title, artist_id) VALUES ('Novo Álbum', 999);

-- ✅ Isso funciona (artista existe):
INSERT INTO albums (title, artist_id) VALUES ('Novo Álbum', 1);
```

### 🔐 Validações Automáticas

1. **NOT NULL**: Campos obrigatórios
2. **AUTO_INCREMENT**: IDs únicos automáticos
3. **DEFAULT**: Valores padrão
4. **CHARSET utf8**: Suporte a acentos e emojis

## 🚀 Performance

### ⚡ Otimizações Implementadas

1. **📊 Índices Automáticos**: PRIMARY KEY cria índices
2. **🔗 Relacionamentos Eficientes**: JOINs otimizados
3. **📅 TIMESTAMP**: Controle automático de datas
4. **🎯 Consultas Preparadas**: Proteção contra SQL Injection

### 📈 Monitoramento
```sql
-- 📊 Ver quantas músicas cada artista tem
SELECT ar.name, COUNT(s.id) as total_songs
FROM artists ar
LEFT JOIN songs s ON ar.id = s.artist_id
GROUP BY ar.id, ar.name
ORDER BY total_songs DESC;
```

## 🐛 Problemas Comuns e Soluções

### ❌ "Erro na conexão"
**Possíveis Causas**:
1. MySQL não está rodando
2. Usuário/senha incorretos
3. Banco não existe

**Soluções**:
```bash
# 1. Verificar se MySQL está rodando (XAMPP)
# Abrir XAMPP Control Panel → Start MySQL

# 2. Testar conexão manual
mysql -u root -p
# (senha vazia no XAMPP)

# 3. Verificar se banco existe
SHOW DATABASES;
```

### ❌ "Tabela não existe"
**Causa**: Inicialização não executou
**Solução**:
```php
// Forçar reinicialização
// Acesse: http://localhost/Ressonance/fix-paths.php
```

### ❌ "Dados não aparecem"
**Causa**: Tabelas vazias
**Solução**:
```sql
-- Verificar se tem dados
SELECT COUNT(*) FROM artists;
SELECT COUNT(*) FROM songs;

-- Adicionar dados de teste
INSERT INTO artists (name, image) VALUES ('Artista Teste', 'test.jpg');
```

## 📊 Consultas Úteis

### 🔍 Verificar Status do Banco
```sql
-- 📋 Listar todas as tabelas
SHOW TABLES;

-- 📊 Ver estrutura de uma tabela
DESCRIBE songs;

-- 📈 Contar registros
SELECT 
    'artists' as tabela, COUNT(*) as total FROM artists
UNION ALL
SELECT 'albums', COUNT(*) FROM albums
UNION ALL
SELECT 'songs', COUNT(*) FROM songs;
```

### 🧹 Limpeza e Manutenção
```sql
-- 🗑️ Limpar músicas sem arquivos
DELETE FROM songs 
WHERE id NOT IN (SELECT DISTINCT song_id FROM song_files);

-- 🔄 Resetar contador de plays
UPDATE songs SET plays = 0;

-- 📊 Atualizar estatísticas
ANALYZE TABLE songs, artists, albums;
```

## 🔗 Arquivos Relacionados

- [queries-centralizadas.md](queries-centralizadas.md) - Sistema de consultas
- [estrutura-banco.md](estrutura-banco.md) - Detalhes das tabelas
- [sistema-caminhos.md](sistema-caminhos.md) - Como os caminhos funcionam
- [apis-endpoints.md](apis-endpoints.md) - APIs que usam o banco

## 💡 Dicas Pro

### 🗄️ Para Backup
```bash
# Fazer backup completo
mysqldump -u root ressonance_music > backup.sql

# Restaurar backup
mysql -u root ressonance_music < backup.sql
```

### 🔧 Para Desenvolvimento
```sql
-- Criar dados de teste rapidamente
INSERT INTO artists (name) VALUES 
('Artista 1'), ('Artista 2'), ('Artista 3');

INSERT INTO songs (title, artist_id) VALUES 
('Música 1', 1), ('Música 2', 1), ('Música 3', 2);
```

### 📈 Para Monitoramento
```sql
-- Ver músicas mais tocadas
SELECT title, plays FROM songs ORDER BY plays DESC LIMIT 10;

-- Ver artistas com mais músicas
SELECT ar.name, COUNT(s.id) as total
FROM artists ar
LEFT JOIN songs s ON ar.id = s.artist_id
GROUP BY ar.id
ORDER BY total DESC;
```