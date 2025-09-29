class AllSongsLoader {
    constructor() {
        this.currentView = 'grid';
        this.currentPage = 1;
        this.loading = false;
        this.hasMore = true;
        this.observer = null;
        this.init();
    }
    
    init() {
        this.setupIntersectionObserver();
        this.loadAllSongs();
    }
    
    setupIntersectionObserver() {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.loading && this.hasMore) {
                    this.loadMoreSongs();
                }
            });
        }, { threshold: 0.1 });
    }
    
    async loadAllSongs() {
        if (this.loading) return;
        this.loading = true;
        
        try {
            const response = await fetch('api/songs.php?action=get_all_songs&page=1&limit=20');
            const data = await response.json();
            
            if (data.success) {
                this.renderSongs(data.data, true);
                this.setupSentinel();
            }
        } catch (error) {
            console.error('Erro ao carregar músicas:', error);
        } finally {
            this.loading = false;
        }
    }
    
    async loadMoreSongs() {
        this.currentPage++;
        if (this.loading) return;
        this.loading = true;
        
        try {
            const response = await fetch(`api/songs.php?action=get_all_songs&page=${this.currentPage}&limit=20`);
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                this.renderSongs(data.data);
            } else {
                this.hasMore = false;
            }
        } catch (error) {
            console.error('Erro ao carregar mais músicas:', error);
        } finally {
            this.loading = false;
        }
    }
    
    setupSentinel() {
        const container = document.getElementById('songsContainer');
        const sentinel = document.createElement('div');
        sentinel.id = 'songs-sentinel';
        sentinel.style.height = '1px';
        container.appendChild(sentinel);
        this.observer.observe(sentinel);
    }
    
    renderSongs(songs, isInitial = false) {
        const container = document.getElementById('songsContainer');
        
        if (isInitial) {
            const placeholder = container.querySelector('.loading-placeholder');
            if (placeholder) placeholder.remove();
            container.className = `songs-container ${this.currentView}-view`;
        }
        
        const sentinel = document.getElementById('songs-sentinel');
        
        songs.forEach(song => {
            const songElement = this.createSongElement(song);
            if (sentinel) {
                container.insertBefore(songElement, sentinel);
            } else {
                container.appendChild(songElement);
            }
        });
    }
    
    createSongElement(song) {
        const div = document.createElement('div');
        div.className = 'song-item';
        div.onclick = () => playSong(song.title, song.artist_name, song.audio_files);
        
        const formatBadges = song.audio_files && song.audio_files.length > 1 ? 
            `<div class="format-badges">
                ${song.audio_files.map(file => `<span class="format-badge">${file.format.toUpperCase()}</span>`).join('')}
            </div>` : '';
        
        const duration = song.duration ? this.formatDuration(song.duration) : '';
        const albumInfo = song.album_title ? `<span class="album-info">${song.album_title}</span>` : '';
        
        if (this.currentView === 'grid') {
            const songImage = song.image && song.image !== 'NULL' ? song.image : 'https://via.placeholder.com/200x200/1db954/ffffff?text=♪';
            div.innerHTML = `
                <div class="song-cover">
                    <img src="${songImage}" alt="${song.title}">
                    <div class="play-overlay">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
                <div class="song-info">
                    <h4 class="song-title">${song.title}</h4>
                    <p class="artist-name">${song.artist_name}</p>
                    ${albumInfo}
                    ${formatBadges}
                </div>
            `;
        } else {
            const songImage = song.image && song.image !== 'NULL' ? song.image : 'https://via.placeholder.com/60x60/1db954/ffffff?text=♪';
            div.innerHTML = `
                <div class="song-cover-small">
                    <img src="${songImage}" alt="${song.title}">
                    <div class="play-overlay-small">
                        <i class="fas fa-play"></i>
                    </div>
                </div>
                <div class="song-details">
                    <div class="song-main-info">
                        <h4 class="song-title">${song.title}</h4>
                        <p class="artist-name">${song.artist_name}</p>
                    </div>
                    <div class="song-meta">
                        ${albumInfo}
                        <span class="duration">${duration}</span>
                        ${formatBadges}
                    </div>
                </div>
            `;
        }
        
        return div;
    }
    
    formatDuration(duration) {
        if (!duration) return '';
        const parts = duration.split(':');
        return `${parts[0]}:${parts[1]}`;
    }
}

function toggleView(view) {
    const loader = window.allSongsLoader;
    const container = document.getElementById('songsContainer');
    const gridBtn = document.getElementById('gridBtn');
    const listBtn = document.getElementById('listBtn');
    
    loader.currentView = view;
    container.className = `songs-container ${view}-view`;
    
    gridBtn.classList.toggle('active', view === 'grid');
    listBtn.classList.toggle('active', view === 'list');
    
    // Re-render existing songs with new view
    const songs = container.querySelectorAll('.song-item');
    songs.forEach(song => {
        const songData = {
            title: song.querySelector('.song-title').textContent,
            artist_name: song.querySelector('.artist-name').textContent,
            album_title: song.querySelector('.album-info')?.textContent || '',
            duration: song.querySelector('.duration')?.textContent || '',
            audio_files: []
        };
        
        const newElement = loader.createSongElement(songData);
        newElement.onclick = song.onclick;
        song.replaceWith(newElement);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    window.allSongsLoader = new AllSongsLoader();
});