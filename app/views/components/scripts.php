<script src="<?= JS_URL ?>/player-core.js"></script>
<?php if (basename($_SERVER['PHP_SELF']) === 'index.php'): ?>
<script src="<?= JS_URL ?>/lazy-loader.js"></script>
<?php endif; ?>
<?php if (basename($_SERVER['PHP_SELF']) === 'all-songs.php'): ?>
<script src="<?= JS_URL ?>/all-songs-loader.js"></script>
<?php endif; ?>

<script>
// Função para reproduzir música (aguarda carregamento do player)
function playMusic(title, artist, audioFiles, image = null) {
    console.log('playMusic chamada:', { title, artist, audioFiles, image });
    
    try {
        if (typeof audioFiles === 'string') {
            audioFiles = JSON.parse(audioFiles);
        }
        
        if (typeof window.playSong === 'function') {
            console.log('Chamando window.playSong diretamente');
            window.playSong(title, artist, audioFiles, image);
        } else {
            console.log('window.playSong não disponível, aguardando...');
            // Aguardar carregamento do player com múltiplas tentativas
            let attempts = 0;
            const maxAttempts = 10;
            
            const tryPlay = () => {
                attempts++;
                if (typeof window.playSong === 'function') {
                    console.log('window.playSong carregado, reproduzindo...');
                    window.playSong(title, artist, audioFiles, image);
                } else if (attempts < maxAttempts) {
                    console.log(`Tentativa ${attempts}/${maxAttempts} - aguardando player...`);
                    setTimeout(tryPlay, 200);
                } else {
                    console.error('Player não carregado após', maxAttempts, 'tentativas');
                    alert('Erro: Player de música não carregado. Recarregue a página.');
                }
            };
            
            tryPlay();
        }
    } catch (error) {
        console.error('Erro em playMusic:', error);
        alert('Erro ao reproduzir música: ' + error.message);
    }
}

// Scripts comuns para modal de álbum
async function openAlbumModal(albumId) {
    const modal = document.getElementById('albumModal');
    const songsContainer = document.getElementById('albumSongs');
    
    modal.style.display = 'flex';
    songsContainer.innerHTML = '<div class="loading-placeholder"><div class="loading-spinner"></div><p>Carregando músicas...</p></div>';
    
    try {
        const response = await fetch(`/Ressonance/app/controllers/api/songs.php?action=get_album_songs&album_id=${albumId}`);
        const data = await response.json();
        
        if (data.success && data.album) {
            document.getElementById('albumTitle').textContent = data.album.title;
            document.getElementById('albumArtist').textContent = data.album.artist_name;
            document.getElementById('albumCover').src = data.album.image;
            
            songsContainer.innerHTML = '';
            
            // Adicionar botão para reproduzir álbum completo
            const playAlbumButton = document.createElement('div');
            playAlbumButton.className = 'play-album-container';
            const playAlbumBtn = document.createElement('button');
            playAlbumBtn.style.cssText = 'background: linear-gradient(135deg, #1db954, #1ed760); color: #000; border: none; padding: 0.75rem 2rem; border-radius: 25px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; transition: all 0.3s; font-size: 1rem;';
            playAlbumBtn.innerHTML = '<i class="fas fa-play"></i> Reproduzir Álbum';
            playAlbumBtn.onclick = function() {
                console.log('Tentando reproduzir álbum', data);
                console.log('Funções disponíveis:', {
                    loadAlbumPlaylist: typeof window.loadAlbumPlaylist,
                    playFromPlaylist: typeof window.playFromPlaylist
                });
                
                if (data && data.songs.length > 0) {
                    if (typeof window.loadAlbumPlaylist === 'function') {
                        window.loadAlbumPlaylist(data);
                        
                        setTimeout(() => {
                            if (typeof window.playFromPlaylist === 'function') {
                                window.playFromPlaylist(0);
                            } else {
                                console.error('playFromPlaylist não encontrada');
                            }
                        }, 100);
                    } else {
                        console.error('loadAlbumPlaylist não encontrada');
                    }
                    closeAlbumModal();
                }
            };
            
            playAlbumButton.appendChild(playAlbumBtn);
            songsContainer.appendChild(playAlbumButton);
            
            // Armazenar dados do álbum para uso nas funções
            window.currentAlbumData = data;
            
            if (data.songs.length > 0) {
                data.songs.forEach((song, index) => {
                    const songElement = document.createElement('div');
                    songElement.className = 'album-song-item';
                    songElement.onclick = () => {
                        // Carregar playlist do álbum
                        window.loadAlbumPlaylist(data);
                        
                        // Encontrar índice da música clicada
                        const songIndex = data.songs.findIndex(s => s.id === song.id);
                        if (songIndex !== -1) {
                            window.playFromPlaylist(songIndex);
                        }
                        
                        closeAlbumModal();
                    };
                    
                    const formatBadges = song.audio_files.length > 1 ? 
                        `<div class="format-badges">
                            ${song.audio_files.map(file => `<span class="format-badge">${file.format.toUpperCase()}</span>`).join('')}
                        </div>` : '';
                    
                    songElement.innerHTML = `
                        <div class="song-number">${index + 1}</div>
                        <div class="song-details">
                            <h4>${song.title}</h4>
                            ${formatBadges}
                        </div>
                        <div class="song-duration">${song.duration || ''}</div>
                        <button style="background: #1db954; color: #000; border: none; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; transition: all 0.3s; opacity: 0;" class="play-song-btn" title="Reproduzir esta música">
                            <i class="fas fa-play"></i>
                        </button>
                    `;
                    
                    // Adicionar event listener para o botão de play
                    const playBtn = songElement.querySelector('.play-song-btn');
                    if (playBtn) {
                        playBtn.onmouseover = function() { this.style.opacity = '1'; };
                        playBtn.onmouseout = function() { this.style.opacity = '0'; };
                        playBtn.onclick = function(e) {
                            e.stopPropagation();
                            console.log('Tentando reproduzir música', index, data.songs[index]);
                            
                            if (typeof window.loadAlbumPlaylist === 'function') {
                                window.loadAlbumPlaylist(data);
                                
                                setTimeout(() => {
                                    if (typeof window.playFromPlaylist === 'function') {
                                        window.playFromPlaylist(index);
                                    }
                                }, 100);
                            }
                            closeAlbumModal();
                        };
                    }
                    
                    // Hover para mostrar botão
                    songElement.onmouseover = function() {
                        const btn = this.querySelector('.play-song-btn');
                        if (btn) btn.style.opacity = '1';
                    };
                    songElement.onmouseout = function() {
                        const btn = this.querySelector('.play-song-btn');
                        if (btn) btn.style.opacity = '0';
                    };
                    
                    songsContainer.appendChild(songElement);
                });
            } else {
                songsContainer.innerHTML = '<p class="no-songs">Nenhuma música encontrada neste álbum.</p>';
            }
        } else {
            songsContainer.innerHTML = '<p class="error">Erro ao carregar álbum.</p>';
        }
    } catch (error) {
        console.error('Erro ao carregar álbum:', error);
        songsContainer.innerHTML = '<p class="error">Erro ao carregar álbum.</p>';
    }
}

function closeAlbumModal() {
    document.getElementById('albumModal').style.display = 'none';
}



// Funções de playlist
window.loadAlbumPlaylist = function(albumData) {
    window.currentPlaylist = albumData.songs;
    window.currentAlbum = albumData.album;
    window.currentIndex = 0;
    
    console.log('Playlist carregada:', window.currentPlaylist);
    updatePlaylistUI();
    
    // Mostrar painel de playlist
    const panel = document.getElementById('playlistPanel');
    const btn = document.getElementById('playlistBtn');
    if (panel && btn) {
        panel.classList.add('open');
        btn.classList.add('active');
    }
};

window.playFromPlaylist = function(index) {
    console.log('playFromPlaylist chamada:', index, window.currentPlaylist);
    if (index >= 0 && index < window.currentPlaylist.length) {
        window.currentIndex = index;
        const song = window.currentPlaylist[index];
        
        console.log('Reproduzindo música:', song);
        
        if (song.audio_files && song.audio_files.length > 0) {
            // Carregar a música diretamente
            const audioUrl = '/Ressonance/audio.php?f=' + encodeURIComponent(song.audio_files[0].path);
            
            if (typeof window.loadSong === 'function') {
                window.loadSong({
                    title: song.title,
                    artist: song.artist_name,
                    file_path: audioUrl,
                    image: song.image
                });
                
                // Reproduzir automaticamente após carregar
                setTimeout(() => {
                    if (typeof window.togglePlay === 'function' && window.hasSongLoaded) {
                        const audioPlayer = document.getElementById('audioPlayer');
                        if (audioPlayer && audioPlayer.paused) {
                            window.togglePlay();
                        }
                    }
                }, 500);
            } else {
                // Fallback para playMusic
                playMusic(song.title, song.artist_name, JSON.stringify(song.audio_files), song.image);
            }
        }
        
        updatePlaylistUI();
    }
};

function updatePlaylistUI() {
    const titleEl = document.getElementById('playlistTitle');
    const contentEl = document.getElementById('playlistContent');
    
    if (!titleEl || !contentEl) return;
    
    if (!window.currentPlaylist || window.currentPlaylist.length === 0) {
        titleEl.textContent = 'Playlist';
        contentEl.innerHTML = '<p class="no-playlist">Nenhuma playlist ativa</p>';
        return;
    }
    
    console.log('Dados da playlist:', window.currentPlaylist[0]); // Debug
    
    titleEl.textContent = window.currentAlbum ? window.currentAlbum.title : 'Playlist';
    
    contentEl.innerHTML = window.currentPlaylist.map((song, index) => {
        const artistName = song.artist_name || window.currentAlbum?.artist_name || 'Artista Desconhecido';
        return `
            <div class="playlist-song ${index === window.currentIndex ? 'current' : ''}" onclick="window.playFromPlaylist(${index})">
                <div class="playlist-song-number">${index + 1}</div>
                <div class="playlist-song-info">
                    <div class="playlist-song-title">${song.title}</div>
                    <div class="playlist-song-artist">${artistName}</div>
                </div>
            </div>
        `;
    }).join('');
}

window.onclick = function(event) {
    const albumModal = document.getElementById('albumModal');
    if (event.target === albumModal) {
        albumModal.style.display = 'none';
    }
}
</script>