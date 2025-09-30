# 🎵 Sistema de Áudio - Streaming de Música

## 🔍 O que é

O sistema de áudio do Ressonance é responsável por **servir músicas via streaming**, permitindo que os usuários ouçam música diretamente no navegador sem precisar baixar os arquivos. É como o Spotify, mas no seu próprio site!

## 📂 Onde está

### Arquivos Principais
- **📄 `audio.php`** - Servidor de streaming (raiz do projeto)
- **📁 `audio/`** - Pasta com arquivos de música
- **📄 `public/assets/js/player-core.js`** - Player de música
- **📄 `app/views/components/player.php`** - Interface do player

### Estrutura de Arquivos de Áudio
```
📁 audio/
├── 🎵 13_1758990614.mp3
├── 🎵 14_1759003598.mp3
├── 🎵 15_1759003748.mp3
└── 🎵 ... outros arquivos
```

## ⚙️ Como Funciona (Explicação Simples)

### 🎯 Fluxo Completo

```
1. 👤 Usuário clica em "Play" numa música
   ↓
2. 🌐 JavaScript chama: audio.php?f=nome_da_musica.mp3
   ↓
3. 🔍 audio.php procura o arquivo na pasta /audio/
   ↓
4. ✅ Arquivo encontrado? Envia para o navegador
   ❌ Não encontrado? Retorna erro 404
   ↓
5. 🎵 Navegador recebe o áudio e toca no player
```

### 🎭 Analogia Simples
Imagine que o `audio.php` é como um **garçom em um restaurante**:
- Você pede uma música (faz o pedido)
- O garçom vai na cozinha (pasta audio/) buscar
- Se tem a música, ele traz para você
- Se não tem, ele diz "não temos esse prato"

## 🔧 Detalhes Técnicos

### 📄 audio.php - O Servidor de Streaming

```php
<?php
// 🎯 Recebe o nome do arquivo
$file = $_GET['f'] ?? '';  // Ex: "13_1758990614.mp3"

// 📂 Monta o caminho completo
$audioPath = 'audio/' . basename($file);  // "audio/13_1758990614.mp3"

// ✅ Verifica se o arquivo existe
if (!file_exists($audioPath)) {
    http_response_code(404);  // 🚫 Não encontrado
    echo 'Arquivo não encontrado';
    exit;
}

// 🔒 Verifica se é um formato permitido
$extension = strtolower(pathinfo($audioPath, PATHINFO_EXTENSION));
$allowedFormats = ['mp3', 'wav', 'ogg', 'flac', 'm4a'];

if (!in_array($extension, $allowedFormats)) {
    http_response_code(403);  // 🚫 Formato não permitido
    exit;
}

// 📡 Configura headers para streaming
header('Content-Type: audio/mpeg');  // Diz ao navegador que é áudio
header('Accept-Ranges: bytes');       // Permite "pular" partes da música
header('Content-Length: ' . filesize($audioPath));  // Tamanho do arquivo

// 🎵 Envia o arquivo para o navegador
readfile($audioPath);
?>
```

### 🎵 Suporte a Range Requests (Streaming Avançado)

O sistema suporta **Range Requests**, que permite:
- ⏩ Pular para qualquer parte da música
- 📱 Economizar dados móveis
- ⚡ Carregamento mais rápido

```php
// 🎯 Se o navegador pede uma parte específica
if (isset($_SERVER['HTTP_RANGE'])) {
    $range = $_SERVER['HTTP_RANGE'];  // Ex: "bytes=1000-2000"
    
    // 🧮 Calcula qual parte enviar
    $ranges = explode('=', $range);
    $offsets = explode('-', $ranges[1]);
    $offset = intval($offsets[0]);    // Início: 1000
    $length = intval($offsets[1]);    // Fim: 2000
    
    // 📡 Envia apenas a parte solicitada
    header('HTTP/1.1 206 Partial Content');
    header('Content-Range: bytes ' . $offset . '-' . $length . '/' . $fileSize);
    
    // 🎵 Lê apenas a parte necessária
    $file = fopen($audioPath, 'rb');
    fseek($file, $offset);
    echo fread($file, $contentLength);
    fclose($file);
}
```

## 🎮 Player de Música

### 📄 player-core.js - O Cérebro do Player

```javascript
// 🎵 Função principal para tocar música
window.playSong = function(title, artist, audioFiles, image) {
    // 🔍 Processa os arquivos de áudio disponíveis
    if (Array.isArray(audioFiles) && audioFiles.length > 0) {
        // 🎯 Se tem múltiplos formatos, mostra seletor
        if (audioFiles.length > 1) {
            showFormatSelector(title, artist, audioFiles, image);
            return;
        } else {
            // 🎵 Usa o primeiro formato disponível
            const filePath = getAudioUrl(audioFiles[0].path);
            const song = { title, artist, file_path: filePath, image };
            loadSong(song);
        }
    }
};

// 🛣️ Gera URL correta para o áudio
function getAudioUrl(filePath) {
    // 🌐 Se já é uma URL completa, usa como está
    if (filePath.startsWith('http') || filePath.startsWith('/')) {
        return filePath;
    }
    
    // 🎯 Senão, usa o sistema de streaming
    return window.APP_CONFIG.BASE_URL + '/audio.php?f=' + encodeURIComponent(filePath);
}
```

### 🎛️ Controles do Player

```javascript
// ▶️ Play/Pause
window.togglePlay = function() {
    if (audioPlayer.paused) {
        audioPlayer.play();  // ▶️ Toca
        icon.className = 'fas fa-pause';
    } else {
        audioPlayer.pause(); // ⏸️ Pausa
        icon.className = 'fas fa-play';
    }
};

// ⏮️ Música anterior
window.previousSong = function() {
    if (window.currentIndex > 0) {
        window.currentIndex--;
        window.playFromPlaylist(window.currentIndex);
    }
};

// ⏭️ Próxima música
window.nextSong = function() {
    if (window.currentIndex < window.currentPlaylist.length - 1) {
        window.currentIndex++;
        window.playFromPlaylist(window.currentIndex);
    }
};
```

## 🗄️ Integração com Banco de Dados

### 📊 Tabela song_files
```sql
CREATE TABLE song_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    song_id INT NOT NULL,                    -- 🎵 ID da música
    file_path VARCHAR(500) NOT NULL,         -- 📂 Caminho do arquivo
    file_format VARCHAR(10) NOT NULL,        -- 🎼 Formato (mp3, flac, etc.)
    file_size BIGINT,                        -- 📏 Tamanho em bytes
    uploaded_by VARCHAR(100),                -- 👤 Quem fez upload
    is_verified BOOLEAN DEFAULT TRUE,        -- ✅ Arquivo verificado
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 🔍 Como as Músicas são Buscadas

```php
// 🎯 Query para buscar músicas com arquivos de áudio
$stmt = $pdo->prepare("
    SELECT s.*, ar.name as artist_name,
           GROUP_CONCAT(DISTINCT CONCAT(sf.file_format, ':', SUBSTRING_INDEX(sf.file_path, '/', -1)) SEPARATOR '|') as audio_files
    FROM songs s 
    JOIN artists ar ON s.artist_id = ar.id 
    LEFT JOIN song_files sf ON s.id = sf.song_id AND sf.is_verified = TRUE
    WHERE sf.file_path IS NOT NULL
    GROUP BY s.id
    ORDER BY s.plays DESC
");
```

**Resultado da Query**:
```
audio_files = "mp3:13_1758990614.mp3|flac:13_1758990614.flac"
```

**Processamento no PHP**:
```php
$audioFiles = [];
foreach (explode('|', $song['audio_files']) as $fileData) {
    list($format, $path) = explode(':', $fileData, 2);
    $audioFiles[] = ['format' => $format, 'path' => $path];
}
// Resultado: [['format' => 'mp3', 'path' => '13_1758990614.mp3'], ...]
```

## 🎼 Formatos Suportados

### 📋 Lista de Formatos
- **🎵 MP3** - Mais comum, boa compressão
- **🎼 FLAC** - Alta qualidade, sem perda
- **🎶 WAV** - Qualidade máxima, arquivo grande
- **🎵 OGG** - Código aberto, boa compressão
- **🎼 M4A** - Formato Apple, boa qualidade
- **🎶 AAC** - Sucessor do MP3

### 🎯 Content-Types Corretos
```php
$contentTypes = [
    'mp3' => 'audio/mpeg',
    'wav' => 'audio/wav',
    'ogg' => 'audio/ogg',
    'flac' => 'audio/flac',
    'm4a' => 'audio/mp4'
];
```

## 🔒 Segurança

### 🛡️ Validações Implementadas

1. **📂 Basename Only**: `basename($file)` impede acesso a outras pastas
2. **🎼 Formato Permitido**: Só formatos de áudio são aceitos
3. **✅ Arquivo Existe**: Verifica se o arquivo realmente existe
4. **📏 Tamanho Limitado**: Evita arquivos muito grandes

```php
// 🚫 Tentativa de hack: audio.php?f=../../../etc/passwd
$audioPath = 'audio/' . basename('../../../etc/passwd');
// ✅ Resultado seguro: 'audio/passwd' (não existe)

// 🚫 Tentativa de hack: audio.php?f=virus.exe
$extension = pathinfo('virus.exe', PATHINFO_EXTENSION); // 'exe'
if (!in_array('exe', $allowedFormats)) {
    // ✅ Bloqueado!
}
```

## 🐛 Problemas Comuns e Soluções

### ❌ "Música não toca"
**Possíveis Causas**:
1. Arquivo não existe na pasta `audio/`
2. Formato não suportado
3. Arquivo corrompido
4. Caminhos incorretos

**Soluções**:
```bash
# 1. Verificar se arquivo existe
ls audio/nome_da_musica.mp3

# 2. Testar diretamente no navegador
http://localhost/Ressonance/audio.php?f=nome_da_musica.mp3

# 3. Verificar console do navegador (F12)
# Procurar por erros 404 ou 403
```

### ❌ "Player não carrega"
**Causa**: JavaScript não inicializado
**Solução**:
```javascript
// Verificar no console (F12):
console.log(typeof window.playSong);  // Deve ser 'function'
console.log(window.APP_CONFIG);       // Deve mostrar configurações
```

### ❌ "Música para no meio"
**Causa**: Problema de streaming
**Solução**: Verificar se o servidor suporta Range Requests

## ⚡ Performance

### 🚀 Otimizações Implementadas

1. **📡 Range Requests**: Permite pular partes da música
2. **💾 Cache Headers**: Navegador guarda arquivos em cache
3. **🔄 Streaming**: Não precisa baixar tudo antes de tocar
4. **📏 Content-Length**: Navegador sabe o tamanho total

### 📊 Monitoramento
```php
// 📈 Log de acessos (pode ser implementado)
error_log("Audio accessed: $file by " . $_SERVER['REMOTE_ADDR']);

// ⏱️ Tempo de resposta
$start = microtime(true);
readfile($audioPath);
$end = microtime(true);
error_log("Audio served in " . ($end - $start) . " seconds");
```

## 🔗 Arquivos Relacionados

- [player-musica.md](player-musica.md) - Detalhes do player
- [estrutura-banco.md](estrutura-banco.md) - Tabelas relacionadas
- [sistema-caminhos.md](sistema-caminhos.md) - Como os caminhos funcionam
- [apis-endpoints.md](apis-endpoints.md) - APIs de música

## 💡 Dicas Pro

### 🎵 Para Adicionar Nova Música
1. Coloque o arquivo na pasta `audio/`
2. Adicione registro na tabela `songs`
3. Adicione arquivo na tabela `song_files`
4. A música aparecerá automaticamente no site

### 🔧 Para Debugar Problemas
1. Teste a URL diretamente: `audio.php?f=arquivo.mp3`
2. Verifique o console do navegador (F12)
3. Olhe os logs do servidor web
4. Teste com diferentes formatos de arquivo