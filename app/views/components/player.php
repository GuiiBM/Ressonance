<footer class="music-player">
    <div class="song-info">
        <img src="https://via.placeholder.com/50x50/333/fff?text=%E2%99%AA" alt="Capa" class="song-cover" id="currentCover">
        <div class="song-details">
            <div class="song-title" id="currentTitle">Selecione uma música</div>
            <div class="artist-name" id="currentArtist">Artista</div>
        </div>
    </div>
    
    <div class="player-center">
        <div class="controls">
            <button onclick="rewind5s()" class="btn-small btn-seek" title="Retroceder 5s">
                <i class="fas fa-backward"></i>
                <span class="seek-text">5</span>
            </button>
            <button onclick="previousSong()" class="btn-small"><i class="fas fa-step-backward"></i></button>
            <button onclick="togglePlay()" id="playBtn" class="btn-main"><i class="fas fa-play"></i></button>
            <button onclick="nextSong()" class="btn-small"><i class="fas fa-step-forward"></i></button>
            <button onclick="forward5s()" class="btn-small btn-seek" title="Avançar 5s">
                <i class="fas fa-forward"></i>
                <span class="seek-text">5</span>
            </button>
        </div>
        <div class="progress-container">
            <span class="progress-time" id="currentTime">0:00</span>
            <div class="progress-bar" onclick="seekTo(event)">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <span class="progress-time" id="totalTime">0:00</span>
        </div>
    </div>
    
    <div class="player-right">
        <div class="volume-container">
            <button class="volume-btn" onclick="toggleMute()" id="volumeBtn"><i class="fas fa-volume-up"></i></button>
            <input type="range" min="0" max="100" value="70" class="volume-slider" id="volumeSlider" oninput="setVolume(this.value)">
        </div>
        <button class="playlist-toggle" onclick="togglePlaylist()" id="playlistBtn" title="Mostrar playlist">
            <i class="fas fa-list"></i>
        </button>
    </div>
    
    <!-- Painel de Playlist -->
    <div class="playlist-panel" id="playlistPanel">
        <div class="playlist-header">
            <h4 id="playlistTitle">Playlist</h4>
            <button onclick="togglePlaylist()" class="close-playlist">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="playlist-content" id="playlistContent">
            <p class="no-playlist">Nenhuma playlist ativa</p>
        </div>
    </div>
    
    <audio id="audioPlayer" preload="metadata"></audio>
</footer>