class LazyLoader {
    constructor() {
        this.loading = false;
        this.currentPage = 1;
        this.hasMore = true;
        this.observer = null;
        this.init();
    }
    
    init() {
        this.setupIntersectionObserver();
        this.loadInitialContent();
    }
    
    setupIntersectionObserver() {
        this.observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !this.loading && this.hasMore) {
                    this.loadMoreContent();
                }
            });
        }, { threshold: 0.1 });
    }
    
    async loadInitialContent() {
        // Pular as 6 primeiras músicas já carregadas no PHP
        this.currentPage = 1;
        await this.loadSongs(2); // Começar da página 2
        await this.loadArtists(1);
    }
    
    async loadMoreContent() {
        this.currentPage++;
        await this.loadSongs(this.currentPage);
    }
    
    async loadSongs(page) {
        if (this.loading) return;
        this.loading = true;
        
        try {
            // Ajustar offset para pular as 6 músicas já carregadas
            const offset = page === 2 ? 6 : (page - 1) * 12;
            const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/songs.php?action=get_songs&page=${page}&limit=12&offset=${offset}`);
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                this.renderSongs(data.data);
            } else {
                this.hasMore = false;
            }
        } catch (error) {
            console.error('Erro ao carregar músicas:', error);
        } finally {
            this.loading = false;
        }
    }
    
    async loadArtists(page) {
        try {
            const response = await fetch(`${window.APP_CONFIG.API_BASE_URL}/songs.php?action=get_artists&page=${page}&limit=10`);
            const data = await response.json();
            
            if (data.success) {
                this.renderArtists(data.data);
            }
        } catch (error) {
            console.error('Erro ao carregar artistas:', error);
        }
    }
    
    renderSongs(songs, isPreload = false) {
        const grid = document.querySelector('.playlist-grid');
        let sentinel = document.getElementById('load-sentinel');
        
        if (!sentinel) {
            sentinel = document.createElement('div');
            sentinel.id = 'load-sentinel';
            sentinel.style.height = '1px';
            grid.appendChild(sentinel);
            this.observer.observe(sentinel);
        }
        
        songs.forEach(song => {
            const songElement = this.createSongElement(song);
            grid.insertBefore(songElement, sentinel);
        });
    }
    
    createSongElement(song) {
        const div = document.createElement('div');
        div.className = 'playlist-item';
        div.onclick = () => playMusic(song.title, song.artist_name, JSON.stringify(song.audio_files));
        
        const formatBadges = song.audio_files.length > 1 ? 
            `<div class="format-selector-mini">
                ${song.audio_files.map(file => `<span class="format-badge">${file.format.toUpperCase()}</span>`).join('')}
            </div>` : '';
        
        const songImage = song.image && song.image !== 'NULL' ? song.image : 'https://via.placeholder.com/160x160/1db954/ffffff?text=%E2%99%AA';
        div.innerHTML = `
            <img src="${songImage}" alt="${song.title}" onerror="this.style.display='none'">
            <div class="playlist-item-content">
                <h4>${song.title}</h4>
                <p>${song.artist_name}</p>
                ${formatBadges}
            </div>
            <div class="play-overlay">
                <i class="fas fa-play"></i>
            </div>
        `;
        
        return div;
    }
    
    renderArtists(artists) {
        const container = document.querySelector('.artists-scroll');
        
        artists.forEach(artist => {
            const artistElement = this.createArtistElement(artist);
            container.appendChild(artistElement);
        });
    }
    
    createArtistElement(artist) {
        const div = document.createElement('div');
        div.className = 'artist-card';
        div.innerHTML = `
            <img src="${artist.image}" alt="${artist.name}">
            <h4>${artist.name}</h4>
        `;
        return div;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new LazyLoader();
});