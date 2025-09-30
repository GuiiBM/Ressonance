<div id="albumModal" class="album-modal">
    <div class="album-modal-content">
        <span class="close" onclick="closeAlbumModal()">&times;</span>
        <div class="album-header">
            <img id="albumCover" src="" alt="Capa do Álbum" onerror="this.src='https://via.placeholder.com/120x120/8a2be2/ffffff?text=%E2%99%AA'">
            <div class="album-info">
                <h2 id="albumTitle"></h2>
                <p id="albumArtist"></p>
            </div>
        </div>
        <div class="album-songs" id="albumSongs">
            <div class="loading-placeholder">
                <div class="loading-spinner"></div>
                <p>Carregando músicas...</p>
            </div>
        </div>
    </div>
</div>