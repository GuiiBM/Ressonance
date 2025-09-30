# 🎵 Sistema de Áudio - Ressonance

## 📋 Visão Geral

O sistema de áudio do Ressonance é responsável por toda a reprodução, streaming e gerenciamento de arquivos musicais. Ele oferece suporte a múltiplos formatos e streaming otimizado.

## 🎯 Funcionalidades Principais

### 🎶 **Reprodução de Música**
- Player HTML5 avançado
- Controles completos (play, pause, volume, progresso)
- Suporte a playlists
- Reprodução contínua

### 📁 **Formatos Suportados**
- **MP3** - Formato principal
- **FLAC** - Alta qualidade
- **WAV** - Sem compressão
- **OGG** - Código aberto
- **M4A/AAC** - Apple/iTunes

### 🌐 **Streaming Inteligente**
- Streaming por chunks
- Cache automático
- Otimização de bandwidth
- Suporte a range requests

## 🏗️ Arquitetura do Sistema

### **Componentes Principais**

```
Sistema de Áudio/
├── audio.php              # Servidor de streaming
├── player-core.js         # Player JavaScript
├── upload_song.php        # Upload de arquivos
└── song.php              # Metadados de músicas
```

### **Fluxo de Dados**

```
[Arquivo MP3] → [audio.php] → [Streaming] → [Player] → [Usuário]
     ↓              ↓            ↓           ↓
[Validação] → [Headers HTTP] → [Chunks] → [Controles]
```

## 🔧 Configuração

### **Limites de Upload**
```php
// Em config-common.php
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_AUDIO_FORMATS', ['mp3', 'wav', 'ogg', 'flac']);
```

### **Diretórios**
- `audio/` - Arquivos de música
- `storage/uploads/audio/` - Uploads temporários
- `public/assets/audio/` - Cache público

## 🎵 Como Funciona

### **1. Upload de Música**
```php
// upload_song.php
$allowedTypes = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
$maxSize = 50 * 1024 * 1024; // 50MB

if (in_array($_FILES['audio']['type'], $allowedTypes)) {
    move_uploaded_file($_FILES['audio']['tmp_name'], $destination);
}
```

### **2. Streaming de Áudio**
```php
// audio.php
header('Content-Type: audio/mpeg');
header('Accept-Ranges: bytes');
header('Content-Length: ' . filesize($file));

// Stream por chunks
$handle = fopen($file, 'rb');
while (!feof($handle)) {
    echo fread($handle, 8192);
    flush();
}
```

### **3. Player JavaScript**
```javascript
// player-core.js
const audio = new Audio();
audio.src = 'audio.php?f=' + filename;
audio.play();

// Controles
audio.addEventListener('timeupdate', updateProgress);
audio.addEventListener('ended', playNext);
```

## 🎛️ Controles do Player

### **Controles Básicos**
- ▶️ **Play/Pause** - Reproduzir/pausar música
- ⏭️ **Next** - Próxima música
- ⏮️ **Previous** - Música anterior
- 🔊 **Volume** - Controle de volume
- 🔀 **Shuffle** - Reprodução aleatória
- 🔁 **Repeat** - Repetir música/playlist

### **Controles Avançados**
- 📊 **Equalizer** - Ajuste de frequências
- ⏩ **Speed** - Velocidade de reprodução
- 📱 **Mobile** - Controles touch
- ⌨️ **Keyboard** - Atalhos de teclado

## 📊 Formatos e Qualidade

### **Comparação de Formatos**

| Formato | Qualidade | Tamanho | Compatibilidade |
|---------|-----------|---------|-----------------|
| MP3     | Boa       | Pequeno | Universal       |
| FLAC    | Excelente | Grande  | Limitada        |
| WAV     | Perfeita  | Muito Grande | Boa        |
| OGG     | Boa       | Pequeno | Moderna         |

### **Configurações de Qualidade**
```javascript
// Detecção automática de qualidade
const formats = ['flac', 'wav', 'mp3', 'ogg'];
for (let format of formats) {
    if (audio.canPlayType(`audio/${format}`)) {
        preferredFormat = format;
        break;
    }
}
```

## 🔄 Sistema de Cache

### **Cache de Arquivos**
- Cache automático de músicas frequentes
- Limpeza automática de cache antigo
- Otimização por uso

### **Cache de Metadados**
```php
// Cache de informações da música
$cacheFile = "cache/song_{$songId}.json";
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 3600) {
    return json_decode(file_get_contents($cacheFile), true);
}
```

## 🎵 Playlists

### **Criação de Playlists**
```javascript
// Adicionar música à playlist
function addToPlaylist(songId, playlistId) {
    fetch('api/playlist.php', {
        method: 'POST',
        body: JSON.stringify({
            action: 'add_song',
            song_id: songId,
            playlist_id: playlistId
        })
    });
}
```

### **Reprodução de Playlists**
```javascript
// Reproduzir playlist completa
function playPlaylist(playlistId) {
    fetch(`api/playlist.php?id=${playlistId}`)
        .then(response => response.json())
        .then(songs => {
            currentPlaylist = songs;
            playCurrentSong(0);
        });
}
```

## 📱 Responsividade

### **Mobile Player**
- Controles touch otimizados
- Interface adaptativa
- Gestos de swipe
- Lock screen controls

### **Desktop Player**
- Atalhos de teclado
- Controles de mouse
- Visualizações avançadas
- Multi-janela

## 🔧 Troubleshooting

### **Problemas Comuns**

#### **Música não toca**
```bash
# Verificar arquivo
curl -I http://localhost/Ressonance/audio.php?f=musica.mp3

# Verificar permissões
ls -la audio/musica.mp3

# Verificar logs
tail -f storage/logs/audio.log
```

#### **Upload falha**
```php
// Verificar limites PHP
echo ini_get('upload_max_filesize');
echo ini_get('post_max_size');
echo ini_get('max_execution_time');
```

#### **Player não carrega**
```javascript
// Debug do player
console.log('Audio support:', audio.canPlayType('audio/mpeg'));
console.log('Current src:', audio.src);
console.log('Network state:', audio.networkState);
```

## 🚀 Otimizações

### **Performance**
- Lazy loading de músicas
- Preload de próxima música
- Compression de áudio
- CDN para arquivos grandes

### **Bandwidth**
- Streaming adaptativo
- Qualidade automática
- Compression dinâmica
- Cache inteligente

## 📈 Monitoramento

### **Métricas**
- Tempo de carregamento
- Taxa de reprodução
- Erros de streaming
- Uso de bandwidth

### **Logs**
```php
// Log de reprodução
error_log("Playing: {$songTitle} by {$artist} - User: {$userId}");

// Log de erros
error_log("Audio error: {$error} - File: {$filename}");
```

## 🔮 Funcionalidades Futuras

- **Streaming adaptativo** por conexão
- **Offline mode** com cache local
- **Visualizador de áudio** em tempo real
- **Crossfade** entre músicas
- **Normalização** automática de volume
- **Suporte a podcasts** e audiobooks

---

**📚 Próximos Passos:**
- [Player de Música](02-player-musica.md) - Detalhes do player
- [Servidor de Áudio](01-servidor-audio.md) - Configuração do servidor
- [Sistema de Imagens](../05-sistema-imagens/) - Capas de álbuns