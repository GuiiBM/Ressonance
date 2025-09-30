# 🎵 Player de Música - Funcionamento Completo

## 🔍 O que é

O player de música do Ressonance é o **coração do sistema**. É ele que toca as músicas, controla volume, gerencia playlists e oferece uma experiência similar ao Spotify. Funciona 100% no navegador, sem precisar instalar nada!

## 📂 Onde está

### Arquivos Principais
- **📄 `public/assets/js/player-core.js`** - Cérebro do player
- **📄 `app/views/components/player.php`** - Interface visual
- **📄 `public/assets/js/script.js`** - Funções auxiliares
- **📄 `app/views/components/scripts.php`** - Integração e modais

### Interface Visual
```
🎵 Player (parte inferior da tela)
├── 🖼️ Capa da música atual
├── 📝 Título e artista
├── 🎛️ Controles (play, pause, anterior, próxima)
├── 📊 Barra de progresso
├── 🔊 Controle de volume
└── 🎼 Botão de playlist
```

## ⚙️ Como Funciona (Explicação Simples)

### 🎯 Fluxo Básico

```
1. 👤 Usuário clica em uma música
   ↓
2. 🧠 JavaScript chama: playMusic('Título', 'Artista', arquivos)
   ↓
3. 🔍 Sistema verifica: "Tem múltiplos formatos?"
   ✅ Sim → Mostra seletor (MP3, FLAC, etc.)
   ❌ Não → Usa o formato disponível
   ↓
4. 🎵 Carrega no player HTML5: <audio src="audio.php?f=musica.mp3">
   ↓
5. ▶️ Usuário clica Play → Música toca
   ↓
6. 🎛️ Controles ficam disponíveis: pause, volume, progresso
```

### 🎭 Analogia Simples
É como um **toca-discos moderno**:
- Você escolhe o disco (música)
- Coloca na vitrola (carrega no player)
- Aperta play (▶️)
- Controla volume, pula faixas, etc.
- A diferença é que tudo acontece no navegador!

## 🔧 Detalhes Técnicos

### 📄 player-core.js - O Cérebro

```javascript
// 🎵 Função principal para tocar música
window.playSong = function(title, artist, audioFiles = null, image = null) {
    console.log('playSong chamada:', { title, artist, audioFiles, image });
    
    // 🔍 Verifica se tem arquivos de áudio
    if (Array.isArray(audioFiles) && audioFiles.length > 0) {
        // 🎯 Múltiplos formatos? Mostra seletor
        if (audioFiles.length > 1) {
            showFormatSelector(title, artist, audioFiles, image);
            return;
        } else {
            // 🎵 Usa o primeiro formato
            const filePath = getAudioUrl(audioFiles[0].path);
            const song = { title, artist, file_path: filePath, image };
            loadSong(song);
            showNotification(`${title} carregada. Clique em Play para reproduzir.`, 'success');
        }
    }
};

// 🛣️ Gera URL correta para o áudio
function getAudioUrl(filePath) {
    // ✅ Se já é URL completa
    if (filePath.startsWith('http') || filePath.startsWith('/')) {
        return filePath;
    }
    
    // 🎯 Usa o sistema de streaming
    return window.APP_CONFIG.BASE_URL + '/audio.php?f=' + encodeURIComponent(filePath);
}

// 🎵 Carrega música no player
function loadSong(song) {
    console.log('Carregando música:', song);
    
    // 💾 Salva música atual
    window.currentSong = song;
    
    // 🏷️ Atualiza interface
    const titleEl = document.getElementById('currentTitle');
    const artistEl = document.getElementById('currentArtist');
    const coverEl = document.getElementById('currentCover');
    
    if (titleEl) titleEl.textContent = song.title;
    if (artistEl) artistEl.textContent = song.artist;
    if (coverEl && song.image) {
        coverEl.src = song.image;
        coverEl.onerror = function() {
            this.src = 'https://via.placeholder.com/60x60/8a2be2/ffffff?text=%E2%99%AA';
        };
    }
    
    // 🎵 Carrega no player HTML5
    if (song.file_path && audioPlayer) {
        console.log('URL do áudio:', song.file_path);
        
        audioPlayer.src = song.file_path;
        audioPlayer.load();  // 🔄 Força recarregamento
        window.hasSongLoaded = true;
        
        // ✅ Habilita botão play
        if (playBtn) {
            playBtn.classList.remove('disabled');
            
            // 🔄 Mostra indicador de carregamento
            const playIcon = playBtn.querySelector('i');
            if (playIcon) {
                playIcon.className = 'fas fa-spinner fa-spin';
                
                // ✅ Remove indicador quando carregar
                audioPlayer.addEventListener('canplay', function() {
                    playIcon.className = 'fas fa-play';
                    console.log('Áudio pronto para reprodução');
                }, { once: true });
            }
        }
    }
}
```

### 🎛️ Controles do Player

```javascript
// ▶️⏸️ Play/Pause
window.togglePlay = function() {
    if (!window.hasSongLoaded || !audioPlayer) {
        showNotification('Nenhuma música carregada', 'error');
        return;
    }
    
    const icon = playBtn.querySelector('i');
    
    if (audioPlayer.paused) {
        // ▶️ Reproduzir
        audioPlayer.play()
            .then(() => {
                console.log('Reprodução iniciada com sucesso');
                icon.className = 'fas fa-pause';
                window.isPlaying = true;
            })
            .catch(error => {
                console.error('Erro ao reproduzir:', error);
                showNotification('Erro ao reproduzir: ' + error.message, 'error');
            });
    } else {
        // ⏸️ Pausar
        audioPlayer.pause();
        icon.className = 'fas fa-play';
        window.isPlaying = false;
    }
};

// ⏮️ Música anterior
window.previousSong = function() {
    if (window.currentPlaylist && window.currentIndex > 0) {
        window.currentIndex--;
        window.playFromPlaylist(window.currentIndex);
    }
};

// ⏭️ Próxima música
window.nextSong = function() {
    if (window.currentPlaylist && window.currentIndex < window.currentPlaylist.length - 1) {
        window.currentIndex++;
        window.playFromPlaylist(window.currentIndex);
    }
};

// ⏪ Voltar 5 segundos
window.rewind5s = function() {
    if (audioPlayer) {
        audioPlayer.currentTime = Math.max(0, audioPlayer.currentTime - 5);
    }
};

// ⏩ Avançar 5 segundos
window.forward5s = function() {
    if (audioPlayer) {
        audioPlayer.currentTime = Math.min(audioPlayer.duration, audioPlayer.currentTime + 5);
    }
};
```

### 🔊 Controle de Volume

```javascript
// 🔊 Ajustar volume
window.setVolume = function(value) {
    if (audioPlayer) {
        audioPlayer.volume = value / 100;  // HTML5 usa 0-1, slider usa 0-100
        updateVolumeIcon(value);
    }
};

// 🔇 Mute/Unmute
window.toggleMute = function() {
    if (!audioPlayer || !volumeSlider || !volumeBtn) return;
    
    if (volumeSlider.value > 0) {
        // 🔇 Mutar
        window.previousVolume = volumeSlider.value;  // Salva volume atual
        audioPlayer.volume = 0;
        volumeSlider.value = 0;
        window.isMuted = true;
        volumeBtn.classList.add('muted');
    } else {
        // 🔊 Desmutar
        audioPlayer.volume = window.previousVolume / 100;
        volumeSlider.value = window.previousVolume;
        window.isMuted = false;
        volumeBtn.classList.remove('muted');
    }
    updateVolumeIcon(volumeSlider.value);
};

// 🎚️ Atualizar ícone do volume
function updateVolumeIcon(value) {
    if (!volumeBtn) return;
    
    const icon = volumeBtn.querySelector('i');
    
    if (value == 0) {
        icon.className = 'fas fa-volume-mute';      // 🔇
        volumeBtn.classList.add('muted');
    } else {
        volumeBtn.classList.remove('muted');
        if (value < 30) {
            icon.className = 'fas fa-volume-off';   // 🔈
        } else if (value < 70) {
            icon.className = 'fas fa-volume-down';  // 🔉
        } else {
            icon.className = 'fas fa-volume-up';    // 🔊
        }
    }
}
```

### 📊 Barra de Progresso

```javascript
// 🎯 Pular para posição específica
window.seekTo = function(event) {
    if (!audioPlayer) return;
    
    const progressBar = event.currentTarget;
    const rect = progressBar.getBoundingClientRect();
    const percent = (event.clientX - rect.left) / rect.width;
    audioPlayer.currentTime = percent * audioPlayer.duration;
};

// 📊 Atualizar progresso automaticamente
audioPlayer.addEventListener('timeupdate', () => {
    if (audioPlayer.duration && progressFill && currentTimeEl) {
        // 📊 Calcular porcentagem
        const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
        progressFill.style.width = percent + '%';
        
        // ⏰ Mostrar tempo atual
        currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
    }
});

// ⏰ Formatar tempo (segundos → mm:ss)
function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}
```

## 🎼 Sistema de Playlists

### 📋 Carregamento de Playlist

```javascript
// 🎼 Carregar playlist de álbum
window.loadAlbumPlaylist = function(albumData) {
    window.currentPlaylist = albumData.songs;  // 💾 Salva músicas
    window.currentAlbum = albumData.album;     // 💾 Salva info do álbum
    window.currentIndex = 0;                   // 🎯 Começa na primeira
    
    updatePlaylistUI();  // 🔄 Atualiza interface
    
    // 📱 Mostra painel de playlist
    const panel = document.getElementById('playlistPanel');
    const btn = document.getElementById('playlistBtn');
    if (panel && btn) {
        panel.classList.add('open');
        btn.classList.add('active');
    }
};

// 🎵 Tocar música da playlist
window.playFromPlaylist = function(index) {
    if (index >= 0 && index < window.currentPlaylist.length) {
        window.currentIndex = index;
        const song = window.currentPlaylist[index];
        
        // 🎵 Carrega e toca
        loadSong({
            title: song.title,
            artist: song.artist_name || song.artist,
            file_path: getAudioUrl(song.audio_files[0].path),
            image: song.image
        });
        
        updatePlaylistUI();  // 🔄 Atualiza interface
        audioPlayer.play();  // ▶️ Toca automaticamente
    }
};

// 🔄 Atualizar interface da playlist
function updatePlaylistUI() {
    const titleEl = document.getElementById('playlistTitle');
    const contentEl = document.getElementById('playlistContent');
    
    if (!titleEl || !contentEl) return;
    
    // 🏷️ Título da playlist
    titleEl.textContent = window.currentAlbum ? window.currentAlbum.title : 'Playlist';
    
    // 📋 Lista de músicas
    contentEl.innerHTML = window.currentPlaylist.map((song, index) => `
        <div class="playlist-song ${index === window.currentIndex ? 'current' : ''}" 
             onclick="window.playFromPlaylist(${index})">
            <div class="playlist-song-number">${index + 1}</div>
            <div class="playlist-song-info">
                <div class="playlist-song-title">${song.title}</div>
                <div class="playlist-song-artist">${song.artist_name || song.artist}</div>
            </div>
        </div>
    `).join('');
}
```

## 🎨 Interface Visual

### 📄 player.php - HTML do Player

```php
<div class="player" id="musicPlayer">
    <!-- 🎵 Elemento de áudio HTML5 -->
    <audio id="audioPlayer" preload="metadata"></audio>
    
    <!-- 📱 Informações da música atual -->
    <div class="player-info">
        <img id="currentCover" src="https://via.placeholder.com/60x60/8a2be2/ffffff?text=%E2%99%AA" alt="Capa">
        <div class="song-details">
            <div id="currentTitle">Selecione uma música</div>
            <div id="currentArtist">Artista</div>
        </div>
    </div>
    
    <!-- 🎛️ Controles principais -->
    <div class="player-controls">
        <button onclick="rewind5s()" title="Voltar 5s">
            <i class="fas fa-backward"></i>
        </button>
        <button onclick="previousSong()" title="Anterior">
            <i class="fas fa-step-backward"></i>
        </button>
        <button id="playBtn" onclick="togglePlay()" class="play-btn disabled" title="Play/Pause">
            <i class="fas fa-play"></i>
        </button>
        <button onclick="nextSong()" title="Próxima">
            <i class="fas fa-step-forward"></i>
        </button>
        <button onclick="forward5s()" title="Avançar 5s">
            <i class="fas fa-forward"></i>
        </button>
    </div>
    
    <!-- 📊 Barra de progresso -->
    <div class="progress-section">
        <span id="currentTime">0:00</span>
        <div class="progress-bar" onclick="seekTo(event)">
            <div id="progressFill" class="progress-fill"></div>
        </div>
        <span id="totalTime">0:00</span>
    </div>
    
    <!-- 🔊 Controle de volume -->
    <div class="volume-section">
        <button id="volumeBtn" onclick="toggleMute()" title="Mute/Unmute">
            <i class="fas fa-volume-up"></i>
        </button>
        <input type="range" id="volumeSlider" min="0" max="100" value="70" 
               oninput="setVolume(this.value)" title="Volume">
    </div>
    
    <!-- 🎼 Botão de playlist -->
    <div class="playlist-section">
        <button id="playlistBtn" onclick="togglePlaylist()" title="Playlist">
            <i class="fas fa-list"></i>
        </button>
    </div>
</div>
```

## 🎯 Seletor de Formatos

### 🎼 Modal para Múltiplos Formatos

```javascript
// 🎼 Mostra seletor quando há múltiplos formatos
function showFormatSelector(title, artist, audioFiles) {
    const modal = document.createElement('div');
    modal.className = 'format-modal';
    modal.innerHTML = `
        <div class="format-modal-content">
            <h3>Escolha o formato de áudio</h3>
            <p><strong>${title}</strong> - ${artist}</p>
            <div class="format-options">
                ${audioFiles.map(file => `
                    <button class="format-option" onclick="selectFormat('${title}', '${artist}', '${file.path}')">
                        ${file.format.toUpperCase()}
                        <small>Qualidade ${getQualityLabel(file.format)}</small>
                    </button>
                `).join('')}
            </div>
            <button onclick="closeFormatModal()" class="close-modal">Cancelar</button>
        </div>
    `;
    document.body.appendChild(modal);
}

// 🏷️ Rótulos de qualidade
function getQualityLabel(format) {
    const labels = {
        'mp3': 'Padrão',
        'flac': 'Alta',
        'wav': 'Máxima',
        'ogg': 'Boa',
        'm4a': 'Boa'
    };
    return labels[format.toLowerCase()] || 'Padrão';
}

// ✅ Selecionar formato
window.selectFormat = function(title, artist, filePath) {
    const audioUrl = getAudioUrl(filePath);
    const song = { title, artist, file_path: audioUrl };
    loadSong(song);
    closeFormatModal();
    showNotification(`${title} carregada. Clique em Play para reproduzir.`, 'success');
};
```

## 🔔 Sistema de Notificações

```javascript
// 🔔 Mostrar notificação para o usuário
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 4px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        animation: slideIn 0.3s ease;
        background: ${type === 'error' ? '#e74c3c' : type === 'success' ? '#27ae60' : '#3498db'};
    `;
    
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);  // Remove após 3 segundos
}
```

## 🎧 Eventos do Player HTML5

```javascript
// 🎵 Inicializar eventos do player
function initializePlayer() {
    if (!audioPlayer) return;
    
    // ⏰ Atualizar progresso
    audioPlayer.addEventListener('timeupdate', () => {
        if (audioPlayer.duration && progressFill && currentTimeEl) {
            const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
            progressFill.style.width = percent + '%';
            currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
        }
    });
    
    // 📊 Duração carregada
    audioPlayer.addEventListener('loadedmetadata', () => {
        if (totalTimeEl) {
            totalTimeEl.textContent = formatTime(audioPlayer.duration);
        }
    });
    
    // 🔚 Música terminou
    audioPlayer.addEventListener('ended', () => {
        window.nextSong();  // Toca próxima automaticamente
    });
    
    // ▶️ Começou a tocar
    audioPlayer.addEventListener('play', () => {
        if (playBtn) {
            const icon = playBtn.querySelector('i');
            if (icon) icon.className = 'fas fa-pause';
        }
        window.isPlaying = true;
    });
    
    // ⏸️ Pausou
    audioPlayer.addEventListener('pause', () => {
        if (playBtn) {
            const icon = playBtn.querySelector('i');
            if (icon) icon.className = 'fas fa-play';
        }
        window.isPlaying = false;
    });
    
    // ❌ Erro ao carregar
    audioPlayer.addEventListener('error', (e) => {
        console.error('Erro no player:', e);
        
        let errorMessage = 'Erro ao carregar o áudio';
        if (audioPlayer.error) {
            switch(audioPlayer.error.code) {
                case 1: errorMessage = 'Reprodução abortada'; break;
                case 2: errorMessage = 'Erro de rede'; break;
                case 3: errorMessage = 'Erro de decodificação'; break;
                case 4: errorMessage = 'Formato não suportado'; break;
            }
        }
        
        showNotification(errorMessage, 'error');
        window.hasSongLoaded = false;
    });
}
```

## 🐛 Problemas Comuns e Soluções

### ❌ "Player não funciona"
**Possíveis Causas**:
1. JavaScript não carregou
2. Elementos HTML não encontrados
3. Arquivo de áudio não existe

**Soluções**:
```javascript
// 1. Verificar no console (F12):
console.log(typeof window.playSong);        // Deve ser 'function'
console.log(document.getElementById('audioPlayer')); // Deve existir

// 2. Verificar se elementos existem:
console.log(!!playBtn, !!audioPlayer, !!progressFill);

// 3. Testar URL de áudio diretamente:
// http://localhost/Ressonance/audio.php?f=musica.mp3
```

### ❌ "Música não toca"
**Causas Comuns**:
1. Arquivo não encontrado (404)
2. Formato não suportado
3. Bloqueio de autoplay do navegador

**Soluções**:
```javascript
// Verificar se carregou
audioPlayer.addEventListener('canplay', () => {
    console.log('Áudio pronto!');
});

// Verificar erros
audioPlayer.addEventListener('error', (e) => {
    console.error('Erro:', audioPlayer.error);
});
```

### ❌ "Volume não funciona"
**Causa**: Slider não conectado
**Solução**:
```javascript
// Verificar conexão
console.log(volumeSlider.value);  // Deve mostrar 0-100
audioPlayer.volume = 0.5;         // Deve mudar volume
```

## 🚀 Performance

### ⚡ Otimizações Implementadas

1. **🔄 Preload Metadata**: Carrega info da música rapidamente
2. **💾 Variáveis Globais**: Evita buscar elementos repetidamente
3. **🎯 Event Listeners Únicos**: Não duplica eventos
4. **📱 Lazy Loading**: Só carrega quando necessário

### 📊 Monitoramento
```javascript
// 📈 Estatísticas do player
console.log({
    currentSong: window.currentSong?.title,
    isPlaying: window.isPlaying,
    currentTime: audioPlayer?.currentTime,
    duration: audioPlayer?.duration,
    volume: audioPlayer?.volume
});
```

## 🔗 Arquivos Relacionados

- [sistema-audio.md](sistema-audio.md) - Como o áudio é servido
- [javascript-frontend.md](javascript-frontend.md) - Outros scripts JS
- [componentes-reutilizaveis.md](componentes-reutilizaveis.md) - Interface do player
- [apis-endpoints.md](apis-endpoints.md) - APIs de música

## 💡 Dicas Pro

### 🎵 Para Adicionar Novos Controles
```javascript
// Exemplo: Botão de repetir
window.toggleRepeat = function() {
    audioPlayer.loop = !audioPlayer.loop;
    // Atualizar visual do botão
};
```

### 🔧 Para Debugar
```javascript
// Ver estado completo do player
window.debugPlayer = function() {
    console.log({
        hasSongLoaded: window.hasSongLoaded,
        currentSong: window.currentSong,
        audioSrc: audioPlayer?.src,
        isPlaying: !audioPlayer?.paused,
        currentTime: audioPlayer?.currentTime,
        duration: audioPlayer?.duration
    });
};
```

### 📱 Para Melhorar UX
```javascript
// Salvar posição da música no localStorage
audioPlayer.addEventListener('timeupdate', () => {
    if (window.currentSong) {
        localStorage.setItem('lastPosition', audioPlayer.currentTime);
    }
});
```