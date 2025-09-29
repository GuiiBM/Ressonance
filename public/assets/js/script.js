let isPlaying = false;
let currentSong = null;
let currentPlaylist = [];
let currentIndex = 0;
let isMuted = false;
let previousVolume = 70;
let hasSongLoaded = false;

const audioPlayer = document.getElementById('audioPlayer');
const playBtn = document.getElementById('playBtn');
const progressFill = document.getElementById('progressFill');
const currentTimeEl = document.getElementById('currentTime');
const totalTimeEl = document.getElementById('totalTime');
const volumeSlider = document.getElementById('volumeSlider');
const volumeBtn = document.getElementById('volumeBtn');

// Player controls
function togglePlay() {
    if (!hasSongLoaded) return;
    
    const icon = playBtn.querySelector('i');
    
    if (audioPlayer.paused) {
        audioPlayer.play();
        icon.className = 'fas fa-pause';
        isPlaying = true;
    } else {
        audioPlayer.pause();
        icon.className = 'fas fa-play';
        isPlaying = false;
    }
}

function previousSong() {
    if (currentIndex > 0) {
        currentIndex--;
        loadSong(currentPlaylist[currentIndex]);
    }
}

function nextSong() {
    if (currentIndex < currentPlaylist.length - 1) {
        currentIndex++;
        loadSong(currentPlaylist[currentIndex]);
    }
}

function rewind5s() {
    audioPlayer.currentTime = Math.max(0, audioPlayer.currentTime - 5);
}

function forward5s() {
    audioPlayer.currentTime = Math.min(audioPlayer.duration, audioPlayer.currentTime + 5);
}

function setVolume(value) {
    audioPlayer.volume = value / 100;
    updateVolumeIcon(value);
}

function toggleMute() {
    const volumeBtn = document.getElementById('volumeBtn');
    
    if (volumeSlider.value > 0) {
        previousVolume = volumeSlider.value;
        audioPlayer.volume = 0;
        volumeSlider.value = 0;
        isMuted = true;
        volumeBtn.classList.add('muted');
    } else {
        audioPlayer.volume = previousVolume / 100;
        volumeSlider.value = previousVolume;
        isMuted = false;
        volumeBtn.classList.remove('muted');
    }
    updateVolumeIcon(volumeSlider.value);
}

function updateVolumeIcon(value) {
    if (!volumeBtn) return;
    
    const icon = volumeBtn.querySelector('i');
    const volumeBtnEl = document.getElementById('volumeBtn');
    
    if (value == 0) {
        icon.className = 'fas fa-volume-mute';
        if (volumeBtnEl) volumeBtnEl.classList.add('muted');
    } else {
        if (volumeBtnEl) volumeBtnEl.classList.remove('muted');
        if (value < 30) {
            icon.className = 'fas fa-volume-off';
        } else if (value < 70) {
            icon.className = 'fas fa-volume-down';
        } else {
            icon.className = 'fas fa-volume-up';
        }
    }
}

function seekTo(event) {
    const progressBar = event.currentTarget;
    const rect = progressBar.getBoundingClientRect();
    const percent = (event.clientX - rect.left) / rect.width;
    audioPlayer.currentTime = percent * audioPlayer.duration;
}

function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function loadSong(song) {
    currentSong = song;
    document.getElementById('currentTitle').textContent = song.title;
    document.getElementById('currentArtist').textContent = song.artist;
    
    if (song.file_path) {
        audioPlayer.src = song.file_path;
        audioPlayer.load();
        hasSongLoaded = true;
        playBtn.classList.remove('disabled');
    } else {
        hasSongLoaded = false;
        playBtn.classList.add('disabled');
    }
}

function playSong(title, artist, audioFiles = null) {
    if (Array.isArray(audioFiles) && audioFiles.length > 0) {
        if (audioFiles.length > 1) {
            showFormatSelector(title, artist, audioFiles);
            return;
        } else {
            const song = { title, artist, file_path: audioFiles[0].path };
            loadSong(song);
            audioPlayer.play();
        }
    } else if (typeof audioFiles === 'string') {
        const song = { title, artist, file_path: audioFiles };
        loadSong(song);
        audioPlayer.play();
    } else {
        const song = { title, artist, file_path: null };
        loadSong(song);
    }
}

// Audio events
if (audioPlayer) {
    audioPlayer.addEventListener('timeupdate', () => {
        if (audioPlayer.duration) {
            const percent = (audioPlayer.currentTime / audioPlayer.duration) * 100;
            progressFill.style.width = percent + '%';
            currentTimeEl.textContent = formatTime(audioPlayer.currentTime);
        }
    });
    
    audioPlayer.addEventListener('loadedmetadata', () => {
        totalTimeEl.textContent = formatTime(audioPlayer.duration);
    });
    
    audioPlayer.addEventListener('ended', () => {
        nextSong();
    });
    
    audioPlayer.addEventListener('play', () => {
        playBtn.querySelector('i').className = 'fas fa-pause';
        isPlaying = true;
    });
    
    audioPlayer.addEventListener('pause', () => {
        playBtn.querySelector('i').className = 'fas fa-play';
        isPlaying = false;
    });
}

// Comments modal
function toggleComments() {
    const modal = document.getElementById('commentModal');
    if (modal.style.display === 'block') {
        modal.style.display = 'none';
    } else {
        modal.style.display = 'block';
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('commentModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Calculate and show artists that fit on screen
function showArtistsThatFit() {
    const container = document.getElementById('artistsContainer');
    if (!container) return;
    
    const artistCards = container.querySelectorAll('.artist-card');
    if (artistCards.length === 0) return;
    
    // Reset all cards to hidden
    artistCards.forEach(card => card.style.display = 'none');
    
    const containerWidth = container.offsetWidth;
    const cardWidth = 160; // artist-card width
    const gap = 24; // 1.5rem gap
    
    const maxCards = Math.floor((containerWidth + gap) / (cardWidth + gap));
    
    // Show only cards that fit
    for (let i = 0; i < Math.min(maxCards, artistCards.length); i++) {
        artistCards[i].style.display = 'block';
    }
}

// Add click events to playlist items
document.addEventListener('DOMContentLoaded', function() {
    const playlistItems = document.querySelectorAll('.playlist-item');
    playlistItems.forEach(item => {
        item.addEventListener('click', function(e) {
            const title = this.querySelector('h4').textContent;
            const artist = this.querySelector('p').textContent;
            const audioFiles = JSON.parse(this.dataset.audioFiles || '[]');
            
            playSong(title, artist, audioFiles);
        });
    });
    
    const artistCards = document.querySelectorAll('.artist-card');
    artistCards.forEach(card => {
        card.addEventListener('click', function() {
            const artist = this.querySelector('h4').textContent;
            playSong('Música Popular', artist);
        });
    });
    
    // Inicializar player
    if (audioPlayer) {
        audioPlayer.volume = 0.7;
        playBtn.classList.add('disabled');
    }
});

// Seletor de formato
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
                    </button>
                `).join('')}
            </div>
            <button onclick="closeFormatModal()" class="close-modal">Cancelar</button>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function selectFormat(title, artist, filePath) {
    playSong(title, artist, filePath);
    closeFormatModal();
}

function closeFormatModal() {
    const modal = document.querySelector('.format-modal');
    if (modal) {
        modal.remove();
    }
}

function openSong(songId) {
    window.location.href = `song.php?id=${songId}`;
}

// Inicializar elementos do player
document.addEventListener('DOMContentLoaded', function() {
    // Garantir que os elementos existem
    const audioPlayerEl = document.getElementById('audioPlayer');
    const playBtnEl = document.getElementById('playBtn');
    const progressFillEl = document.getElementById('progressFill');
    const currentTimeEl = document.getElementById('currentTime');
    const totalTimeEl = document.getElementById('totalTime');
    const volumeSliderEl = document.getElementById('volumeSlider');
    const volumeBtnEl = document.getElementById('volumeBtn');
    
    if (audioPlayerEl) {
        audioPlayer = audioPlayerEl;
    }
    if (playBtnEl) {
        playBtn = playBtnEl;
    }
    if (progressFillEl) {
        progressFill = progressFillEl;
    }
    if (currentTimeEl) {
        currentTimeEl = currentTimeEl;
    }
    if (totalTimeEl) {
        totalTimeEl = totalTimeEl;
    }
    if (volumeSliderEl) {
        volumeSlider = volumeSliderEl;
    }
    if (volumeBtnEl) {
        volumeBtn = volumeBtnEl;
    }
});
    
    // Calculate artists on load and resize
    showArtistsThatFit();
    window.addEventListener('resize', showArtistsThatFit);
});