// Variáveis globais do player
window.isPlaying = false;
window.currentSong = null;
window.currentPlaylist = [];
window.currentIndex = 0;
window.currentAlbum = null;
window.isMuted = false;
window.previousVolume = 70;
window.hasSongLoaded = false;

// Elementos do DOM
let audioPlayer, playBtn, progressFill, currentTimeEl, totalTimeEl, volumeSlider, volumeBtn;

// Inicializar elementos quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    audioPlayer = document.getElementById('audioPlayer');
    playBtn = document.getElementById('playBtn');
    progressFill = document.getElementById('progressFill');
    currentTimeEl = document.getElementById('currentTime');
    totalTimeEl = document.getElementById('totalTime');
    volumeSlider = document.getElementById('volumeSlider');
    volumeBtn = document.getElementById('volumeBtn');
    
    initializePlayer();
});

// Tornar funções globais
window.togglePlay = function() {
    if (!window.hasSongLoaded || !audioPlayer) {
        showNotification('Nenhuma música carregada', 'error');
        return;
    }
    
    const icon = playBtn.querySelector('i');
    
    if (audioPlayer.paused) {
        console.log('Tentando reproduzir:', audioPlayer.src);
        audioPlayer.play()
            .then(() => {
                console.log('Reprodução iniciada com sucesso');
                icon.className = 'fas fa-pause';
                window.isPlaying = true;
            })
            .catch(error => {
                console.error('Erro ao reproduzir:', error);
                showNotification('Erro ao reproduzir: ' + error.message, 'error');
                icon.className = 'fas fa-play';
                window.isPlaying = false;
            });
    } else {
        audioPlayer.pause();
        icon.className = 'fas fa-play';
        window.isPlaying = false;
    }
};

window.previousSong = function() {
    if (window.currentPlaylist && window.currentPlaylist.length > 0 && window.currentIndex > 0) {
        window.currentIndex--;
        window.playFromPlaylist(window.currentIndex);
    }
};

window.nextSong = function() {
    if (window.currentPlaylist && window.currentPlaylist.length > 0 && window.currentIndex < window.currentPlaylist.length - 1) {
        window.currentIndex++;
        window.playFromPlaylist(window.currentIndex);
    }
};

window.rewind5s = function() {
    if (audioPlayer) audioPlayer.currentTime = Math.max(0, audioPlayer.currentTime - 5);
};

window.forward5s = function() {
    if (audioPlayer) audioPlayer.currentTime = Math.min(audioPlayer.duration, audioPlayer.currentTime + 5);
};

window.setVolume = function(value) {
    if (audioPlayer) {
        audioPlayer.volume = value / 100;
        updateVolumeIcon(value);
    }
};

window.toggleMute = function() {
    if (!audioPlayer || !volumeSlider || !volumeBtn) return;
    
    if (volumeSlider.value > 0) {
        window.previousVolume = volumeSlider.value;
        audioPlayer.volume = 0;
        volumeSlider.value = 0;
        window.isMuted = true;
        volumeBtn.classList.add('muted');
    } else {
        audioPlayer.volume = window.previousVolume / 100;
        volumeSlider.value = window.previousVolume;
        window.isMuted = false;
        volumeBtn.classList.remove('muted');
    }
    updateVolumeIcon(volumeSlider.value);
};

function updateVolumeIcon(value) {
    if (!volumeBtn) return;
    
    const icon = volumeBtn.querySelector('i');
    
    if (value == 0) {
        icon.className = 'fas fa-volume-mute';
        volumeBtn.classList.add('muted');
    } else {
        volumeBtn.classList.remove('muted');
        if (value < 30) {
            icon.className = 'fas fa-volume-off';
        } else if (value < 70) {
            icon.className = 'fas fa-volume-down';
        } else {
            icon.className = 'fas fa-volume-up';
        }
    }
}

window.seekTo = function(event) {
    if (!audioPlayer) return;
    const progressBar = event.currentTarget;
    const rect = progressBar.getBoundingClientRect();
    const percent = (event.clientX - rect.left) / rect.width;
    audioPlayer.currentTime = percent * audioPlayer.duration;
};

function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function loadSong(song) {
    console.log('Carregando música:', song);
    
    window.currentSong = song;
    
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
    
    if (song.file_path && audioPlayer) {
        console.log('URL do áudio:', song.file_path);
        
        audioPlayer.src = song.file_path;
        audioPlayer.load();
        window.hasSongLoaded = true;
        
        if (playBtn) {
            playBtn.classList.remove('disabled');
            
            // Mostrar indicador de carregamento
            const playIcon = playBtn.querySelector('i');
            if (playIcon) {
                playIcon.className = 'fas fa-spinner fa-spin';
                
                // Remover indicador quando carregar
                audioPlayer.addEventListener('canplay', function() {
                    playIcon.className = 'fas fa-play';
                    console.log('Áudio pronto para reprodução');
                }, { once: true });
                
                // Timeout para indicador de carregamento
                setTimeout(() => {
                    if (playIcon.className.includes('fa-spinner')) {
                        playIcon.className = 'fas fa-play';
                        console.warn('Timeout no carregamento do áudio');
                    }
                }, 10000);
            }
            
            // Atualizar UI da playlist se existir
            updatePlaylistUI();
        }
        
    } else {
        window.hasSongLoaded = false;
        if (playBtn) playBtn.classList.add('disabled');
        showNotification('Arquivo de áudio não encontrado', 'error');
        console.error('Caminho do arquivo não fornecido ou player não inicializado');
    }
}

window.playSong = function(title, artist, audioFiles = null, image = null) {
    console.log('playSong chamada:', { title, artist, audioFiles, image });
    
    if (Array.isArray(audioFiles) && audioFiles.length > 0) {
        if (audioFiles.length > 1) {
            showFormatSelector(title, artist, audioFiles, image);
            return;
        } else {
            const filePath = getAudioUrl(audioFiles[0].path);
            const song = { title, artist, file_path: filePath, image };
            loadSong(song);
            // Não reproduzir automaticamente, deixar o usuário clicar em play
            showNotification(`${title} carregada. Clique em Play para reproduzir.`, 'success');
        }
    } else if (typeof audioFiles === 'string') {
        const filePath = getAudioUrl(audioFiles);
        const song = { title, artist, file_path: filePath, image };
        loadSong(song);
        showNotification(`${title} carregada. Clique em Play para reproduzir.`, 'success');
    } else {
        const song = { title, artist, file_path: null, image };
        loadSong(song);
    }
};

function getAudioUrl(filePath) {
    // Se já é uma URL completa, retorna como está
    if (filePath.startsWith('http') || filePath.startsWith('/')) {
        return filePath;
    }
    
    // Se é apenas o nome do arquivo, usa o sistema de streaming
    return window.APP_CONFIG.BASE_URL + '/audio.php?f=' + encodeURIComponent(filePath);
}

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
    setTimeout(() => notification.remove(), 3000);
}

function initializePlayer() {
    if (!audioPlayer) {
        console.warn('Elemento audioPlayer não encontrado');
        return;
    }
    
    console.log('Inicializando player de áudio...');
    
    audioPlayer.addEventListener('timeupdate', () => {
        if (audioPlayer.duration && progressFill && currentTimeEl) {
            const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
            progressFill.style.width = percent + '%';
            currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
        }
    });
    
    audioPlayer.addEventListener('loadedmetadata', () => {
        if (totalTimeEl) {
            totalTimeEl.textContent = formatTime(audioPlayer.duration);
        }
    });
    
    audioPlayer.addEventListener('ended', () => {
        window.nextSong();
    });
    
    audioPlayer.addEventListener('play', () => {
        if (playBtn) {
            const icon = playBtn.querySelector('i');
            if (icon) icon.className = 'fas fa-pause';
        }
        window.isPlaying = true;
    });
    
    audioPlayer.addEventListener('pause', () => {
        if (playBtn) {
            const icon = playBtn.querySelector('i');
            if (icon) icon.className = 'fas fa-play';
        }
        window.isPlaying = false;
    });
    
    audioPlayer.addEventListener('error', (e) => {
        console.error('Erro no player:', e);
        console.error('Erro detalhado:', audioPlayer.error);
        
        let errorMessage = 'Erro ao carregar o áudio';
        if (audioPlayer.error) {
            switch(audioPlayer.error.code) {
                case 1:
                    errorMessage = 'Reprodução abortada';
                    break;
                case 2:
                    errorMessage = 'Erro de rede';
                    break;
                case 3:
                    errorMessage = 'Erro de decodificação';
                    break;
                case 4:
                    errorMessage = 'Formato não suportado';
                    break;
            }
        }
        
        showNotification(errorMessage, 'error');
        if (playBtn) {
            const icon = playBtn.querySelector('i');
            if (icon) icon.className = 'fas fa-play';
        }
        window.hasSongLoaded = false;
    });
    
    audioPlayer.addEventListener('loadstart', () => {
        console.log('Iniciando carregamento:', audioPlayer.src);
    });
    
    audioPlayer.addEventListener('loadeddata', () => {
        console.log('Áudio carregado com sucesso');
    });
    
    audioPlayer.addEventListener('canplaythrough', () => {
        console.log('Áudio pode ser reproduzido completamente');
    });
    
    audioPlayer.addEventListener('stalled', () => {
        console.warn('Carregamento do áudio travado');
    });
    
    audioPlayer.addEventListener('waiting', () => {
        console.log('Aguardando mais dados...');
    });
    
    // Configurar volume inicial
    if (volumeSlider) {
        audioPlayer.volume = volumeSlider.value / 100;
    }
    
    console.log('Player inicializado com sucesso');
    
    // Tornar funções disponíveis globalmente para debug
    window.playerReady = true;
    console.log('Funções do player disponíveis:', {
        playSong: typeof window.playSong,
        togglePlay: typeof window.togglePlay,
        playerReady: window.playerReady
    });
}

function showFormatSelector(title, artist, audioFiles) {
    const modal = document.createElement('div');
    modal.className = 'format-modal';
    modal.innerHTML = `
        <div class="format-modal-content">
            <h3>Escolha o formato de áudio</h3>
            <p><strong>${title}</strong> - ${artist}</p>
            <div class="format-options">
                ${audioFiles.map(file => `
                    <button class="format-option" onclick="selectFormat('${title.replace(/'/g, "\\'").replace(/"/g, '\\"')}', '${artist.replace(/'/g, "\\'").replace(/"/g, '\\"')}', '${file.path.replace(/'/g, "\\'").replace(/"/g, '\\"')}')">
                        ${file.format.toUpperCase()}
                    </button>
                `).join('')}
            </div>
            <button onclick="closeFormatModal()" class="close-modal">Cancelar</button>
        </div>
    `;
    document.body.appendChild(modal);
}

window.selectFormat = function(title, artist, filePath) {
    const audioUrl = getAudioUrl(filePath);
    const song = { title, artist, file_path: audioUrl };
    loadSong(song);
    closeFormatModal();
    showNotification(`${title} carregada. Clique em Play para reproduzir.`, 'success');
};

window.closeFormatModal = function() {
    const modal = document.querySelector('.format-modal');
    if (modal) modal.remove();
};

// Funções de playlist
window.togglePlaylist = function() {
    const panel = document.getElementById('playlistPanel');
    const btn = document.getElementById('playlistBtn');
    
    if (panel && btn) {
        panel.classList.toggle('open');
        btn.classList.toggle('active');
    }
};

window.loadAlbumPlaylist = function(albumData) {
    window.currentPlaylist = albumData.songs;
    window.currentAlbum = albumData.album;
    window.currentIndex = 0;
    
    updatePlaylistUI();
    
    // Mostrar painel de playlist
    const panel = document.getElementById('playlistPanel');
    const btn = document.getElementById('playlistBtn');
    if (panel && btn) {
        panel.classList.add('open');
        btn.classList.add('active');
    }
};

function updatePlaylistUI() {
    const titleEl = document.getElementById('playlistTitle');
    const contentEl = document.getElementById('playlistContent');
    
    if (!titleEl || !contentEl) return;
    
    if (window.currentPlaylist.length === 0) {
        titleEl.textContent = 'Playlist';
        contentEl.innerHTML = '<p class="no-playlist">Nenhuma playlist ativa</p>';
        return;
    }
    
    titleEl.textContent = window.currentAlbum ? window.currentAlbum.title : 'Playlist';
    
    contentEl.innerHTML = window.currentPlaylist.map((song, index) => `
        <div class="playlist-song ${index === window.currentIndex ? 'current' : ''}" onclick="playFromPlaylist(${index})">
            <div class="playlist-song-number">${index + 1}</div>
            <div class="playlist-song-info">
                <div class="playlist-song-title">${song.title}</div>
                <div class="playlist-song-artist">${song.artist_name || song.artist}</div>
            </div>
        </div>
    `).join('');
}

window.playFromPlaylist = function(index) {
    if (index >= 0 && index < window.currentPlaylist.length) {
        window.currentIndex = index;
        const song = window.currentPlaylist[index];
        loadSong({
            title: song.title,
            artist: song.artist_name || song.artist,
            file_path: getAudioUrl(song.audio_files[0].path),
            image: song.image
        });
        updatePlaylistUI();
        audioPlayer.play();
    }
};