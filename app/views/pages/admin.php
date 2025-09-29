<?php
require_once '../components/init.php';
requireAdmin();
$pageTitle = 'Admin';

// Processar ações CRUD
if ($_POST) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'add_artist':
            $image = $_POST['image'];
            
            // Se enviou arquivo, fazer upload
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
                $uploadDir = dirname(dirname(dirname(__DIR__))) . '/public/assets/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileInfo = pathinfo($_FILES['image_file']['name']);
                $extension = strtolower($fileInfo['extension']);
                $fileName = 'artist_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadPath)) {
                    $image = '/Ressonance/public/assets/images/' . $fileName;
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO artists (name, image) VALUES (?, ?)");
            $stmt->execute([$_POST['name'], $image]);
            break;
            
        case 'add_song':
            $album_id = $_POST['album_id'] ?: null;
            $duration = $_POST['duration'] ?: null;
            $image = $_POST['image'];
            
            // Se enviou arquivo de imagem, fazer upload
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
                $uploadDir = dirname(dirname(dirname(__DIR__))) . '/public/assets/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileInfo = pathinfo($_FILES['image_file']['name']);
                $extension = strtolower($fileInfo['extension']);
                $fileName = 'song_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadPath)) {
                    $image = '/Ressonance/public/assets/images/' . $fileName;
                }
            }
            
            // Inserir música primeiro
            $stmt = $pdo->prepare("INSERT INTO songs (title, artist_id, album_id, duration, image) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['title'], $_POST['artist_id'], $album_id, $duration, $image]);
            $songId = $pdo->lastInsertId();
            
            // Upload de arquivo de áudio
            if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === 0) {
                $uploadDir = dirname(dirname(dirname(__DIR__))) . '/audio/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileInfo = pathinfo($_FILES['audio_file']['name']);
                $format = strtolower($fileInfo['extension']);
                $fileName = $songId . '_' . time() . '.' . $format;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $uploadPath)) {
                    $stmt = $pdo->prepare("INSERT INTO song_files (song_id, file_path, file_format, file_size, uploaded_by, is_verified) VALUES (?, ?, ?, ?, ?, TRUE)");
                    $stmt->execute([$songId, $fileName, $format, $_FILES['audio_file']['size'], $_SESSION['user_id']]);
                }
            }
            break;
            
        case 'delete_artist':
            $artistId = $_POST['id'];
            
            // Excluir arquivos de áudio das músicas do artista
            $stmt = $pdo->prepare("DELETE FROM song_files WHERE song_id IN (SELECT id FROM songs WHERE artist_id = ?)");
            $stmt->execute([$artistId]);
            
            // Excluir músicas do artista
            $stmt = $pdo->prepare("DELETE FROM songs WHERE artist_id = ?");
            $stmt->execute([$artistId]);
            
            // Excluir álbuns do artista
            $stmt = $pdo->prepare("DELETE FROM albums WHERE artist_id = ?");
            $stmt->execute([$artistId]);
            
            // Excluir artista
            $stmt = $pdo->prepare("DELETE FROM artists WHERE id = ?");
            $stmt->execute([$artistId]);
            break;
            
        case 'edit_artist':
            if (empty($_POST['artist_id'])) {
                header('Location: admin.php?error=missing_id');
                exit;
            }
            
            $image = $_POST['image'];
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
                $uploadDir = dirname(dirname(dirname(__DIR__))) . '/public/assets/images/';
                $fileInfo = pathinfo($_FILES['image_file']['name']);
                $extension = strtolower($fileInfo['extension']);
                $fileName = 'artist_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadPath)) {
                    $image = '/Ressonance/public/assets/images/' . $fileName;
                }
            }
            
            $stmt = $pdo->prepare("UPDATE artists SET name = ?, image = ? WHERE id = ?");
            $stmt->execute([$_POST['name'], $image, $_POST['artist_id']]);
            break;
            
        case 'delete_song':
            $songId = $_POST['id'];
            
            // Excluir arquivos de áudio da música
            $stmt = $pdo->prepare("DELETE FROM song_files WHERE song_id = ?");
            $stmt->execute([$songId]);
            
            // Excluir música
            $stmt = $pdo->prepare("DELETE FROM songs WHERE id = ?");
            $stmt->execute([$songId]);
            break;
            
        case 'edit_song':
            $album_id = $_POST['album_id'] ?: null;
            $duration = $_POST['duration'] ?: null;
            $stmt = $pdo->prepare("UPDATE songs SET title = ?, artist_id = ?, album_id = ?, duration = ? WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['artist_id'], $album_id, $duration, $_POST['song_id']]);
            break;
            
        case 'delete_audio_file':
            $stmt = $pdo->prepare("DELETE FROM song_files WHERE id = ?");
            $stmt->execute([$_POST['file_id']]);
            break;
            
        case 'add_audio_file':
            if (isset($_FILES['new_audio_file']) && $_FILES['new_audio_file']['error'] === 0) {
                $uploadDir = dirname(dirname(dirname(__DIR__))) . '/audio/';
                $fileInfo = pathinfo($_FILES['new_audio_file']['name']);
                $format = strtolower($fileInfo['extension']);
                $fileName = $_POST['song_id'] . '_' . time() . '.' . $format;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['new_audio_file']['tmp_name'], $uploadPath)) {
                    $stmt = $pdo->prepare("INSERT INTO song_files (song_id, file_path, file_format, file_size, uploaded_by, is_verified) VALUES (?, ?, ?, ?, ?, TRUE)");
                    $stmt->execute([$_POST['song_id'], $fileName, $format, $_FILES['new_audio_file']['size'], $_SESSION['user_id']]);
                }
            }
            break;
            
        case 'delete_album':
            $albumId = $_POST['id'];
            
            // Excluir arquivos de áudio das músicas do álbum
            $stmt = $pdo->prepare("DELETE FROM song_files WHERE song_id IN (SELECT id FROM songs WHERE album_id = ?)");
            $stmt->execute([$albumId]);
            
            // Excluir músicas do álbum
            $stmt = $pdo->prepare("DELETE FROM songs WHERE album_id = ?");
            $stmt->execute([$albumId]);
            
            // Excluir álbum
            $stmt = $pdo->prepare("DELETE FROM albums WHERE id = ?");
            $stmt->execute([$albumId]);
            break;
            
        case 'edit_album':
            if (empty($_POST['album_id'])) {
                header('Location: admin.php?error=missing_id');
                exit;
            }
            
            $image = $_POST['image'];
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
                $uploadDir = dirname(dirname(dirname(__DIR__))) . '/public/assets/images/';
                $fileInfo = pathinfo($_FILES['image_file']['name']);
                $extension = strtolower($fileInfo['extension']);
                $fileName = 'album_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadPath)) {
                    $image = '/Ressonance/public/assets/images/' . $fileName;
                }
            }
            
            $stmt = $pdo->prepare("UPDATE albums SET title = ?, artist_id = ?, image = ? WHERE id = ?");
            $stmt->execute([$_POST['title'], $_POST['artist_id'], $image, $_POST['album_id']]);
            break;
            
        case 'add_album':
            $image = $_POST['image'];
            
            // Se enviou arquivo, fazer upload
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === 0) {
                $uploadDir = dirname(dirname(dirname(__DIR__))) . '/public/assets/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileInfo = pathinfo($_FILES['image_file']['name']);
                $extension = strtolower($fileInfo['extension']);
                $fileName = 'album_' . time() . '.' . $extension;
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadPath)) {
                    $image = '/Ressonance/public/assets/images/' . $fileName;
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO albums (title, artist_id, image) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['title'], $_POST['artist_id'], $image]);
            break;
            
        case 'add_songs_to_album':
            $album_id = $_POST['album_id'];
            $song_ids = json_decode($_POST['song_ids'], true);
            
            foreach ($song_ids as $song_id) {
                $stmt = $pdo->prepare("UPDATE songs SET album_id = ? WHERE id = ?");
                $stmt->execute([$album_id, $song_id]);
            }
            break;
            
        case 'remove_song_from_album':
            $stmt = $pdo->prepare("UPDATE songs SET album_id = NULL WHERE id = ?");
            $stmt->execute([$_POST['song_id']]);
            break;
    }
    
    header('Location: admin.php?success=1');
    exit;
}

// Buscar dados
$artists = $db->getAllArtists();
$albums = $pdo->query("SELECT a.*, ar.name as artist_name FROM albums a JOIN artists ar ON a.artist_id = ar.id ORDER BY a.title")->fetchAll(PDO::FETCH_ASSOC);
$songs = $db->getAllSongs();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?php include '../components/head.php'; ?>
</head>
<body>
    <?php include '../components/admin-header.php'; ?>

    <main class="admin-main">
        <?php if (isset($_GET['success'])): ?>
            <div class="success-message">Operação realizada com sucesso!</div>
        <?php endif; ?>
        
        <?php if (isset($_GET['error'])): ?>
            <div style="background-color: #ff4b5a; color: #fff; padding: 1rem; border-radius: 4px; margin-bottom: 2rem; text-align: center; font-weight: 600;">
                <?php if ($_GET['error'] === 'cannot_delete_artist'): ?>
                    ⚠️ Não é possível excluir este artista pois há músicas ou álbuns vinculados a ele.
                <?php elseif ($_GET['error'] === 'cannot_delete_album'): ?>
                    ⚠️ Não é possível excluir este álbum pois há músicas vinculadas a ele.
                <?php elseif ($_GET['error'] === 'invalid_data'): ?>
                    ❌ Dados inválidos fornecidos.
                <?php elseif ($_GET['error'] === 'artist_not_found'): ?>
                    ❌ Artista não encontrado.
                <?php elseif ($_GET['error'] === 'album_not_found'): ?>
                    ❌ Álbum não encontrado.
                <?php elseif ($_GET['error'] === 'database_error'): ?>
                    ❌ Erro no banco de dados.
                <?php else: ?>
                    ❌ Erro ao realizar operação.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="admin-tabs">
            <button class="tab-btn active" onclick="showTab('artists')">Artistas</button>
            <button class="tab-btn" onclick="showTab('songs')">Músicas</button>
            <button class="tab-btn" onclick="showTab('albums')">Álbuns</button>
        </div>

        <!-- Artistas -->
        <div id="artists" class="tab-content active">
            <div class="admin-section">
                <h2>Adicionar Artista</h2>
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <input type="hidden" name="action" value="add_artist">
                    <input type="text" name="name" placeholder="Nome do Artista" required>
                    <input type="url" name="image" placeholder="URL da Imagem">
                    <div class="file-upload">
                        <label for="artist_image_file">
                            <i class="fas fa-image"></i>
                            <span>OU envie arquivo de imagem</span>
                            <small>JPG, PNG, GIF</small>
                        </label>
                        <input type="file" id="artist_image_file" name="image_file" accept="image/*">
                    </div>
                    <button type="submit">Adicionar</button>
                </form>
            </div>

            <div class="admin-section">
                <h2>Artistas Cadastrados</h2>
                <div class="admin-grid">
                    <?php foreach ($artists as $artist): ?>
                        <div class="admin-item">
                            <img src="<?= htmlspecialchars($artist['image']) ?>" alt="<?= htmlspecialchars($artist['name']) ?>">
                            <h4><?= htmlspecialchars($artist['name']) ?></h4>
                            <button onclick="editArtist(<?= $artist['id'] ?>, '<?= htmlspecialchars($artist['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($artist['image'], ENT_QUOTES) ?>')" class="edit-btn" style="position: absolute; top: 0.5rem; left: 0.5rem;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete_artist">
                                <input type="hidden" name="id" value="<?= $artist['id'] ?>">
                                <button type="submit" class="delete-btn" onclick="return confirm('Deletar artista?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Músicas -->
        <div id="songs" class="tab-content">
            <div class="admin-section">
                <h2>Adicionar Música</h2>
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <input type="hidden" name="action" value="add_song">
                    <input type="text" name="title" placeholder="Título da Música" required>
                    <select name="artist_id" required>
                        <option value="">Selecione o Artista</option>
                        <?php foreach ($artists as $artist): ?>
                            <option value="<?= $artist['id'] ?>"><?= htmlspecialchars($artist['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="album_id">
                        <option value="">Selecione o Álbum (opcional)</option>
                        <?php foreach ($albums as $album): ?>
                            <option value="<?= $album['id'] ?>"><?= htmlspecialchars($album['title']) ?> - <?= htmlspecialchars($album['artist_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="time" name="duration" step="1" placeholder="Duração">
                    <input type="url" name="image" placeholder="URL da Imagem da Música">
                    <div class="file-upload">
                        <label for="song_image_file">
                            <i class="fas fa-image"></i>
                            <span>OU envie arquivo de imagem</span>
                            <small>JPG, PNG, GIF</small>
                        </label>
                        <input type="file" id="song_image_file" name="image_file" accept="image/*">
                    </div>

                    <div class="file-upload">
                        <label for="audio_file">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Arquivo de Áudio</span>
                            <small>MP3, WAV, OGG, M4A</small>
                        </label>
                        <input type="file" id="audio_file" name="audio_file" accept=".mp3,.wav,.ogg,.m4a" required>
                    </div>
                    <button type="submit">Adicionar</button>
                </form>
            </div>

            <div class="admin-section">
                <h2>Músicas Cadastradas</h2>
                <div class="admin-table">
                    <?php foreach ($songs as $song): ?>
                        <div class="table-row">
                            <div class="song-info">
                                <strong><?= htmlspecialchars($song['title']) ?></strong>
                                <span><?= htmlspecialchars($song['artist_name']) ?></span>
                                <?php if ($song['album_title']): ?>
                                    <small><?= htmlspecialchars($song['album_title']) ?></small>
                                <?php endif; ?>
                                <?php 
                                $audioFiles = [];
                                if ($song['audio_files']) {
                                    foreach (explode('|', $song['audio_files']) as $fileData) {
                                        if (strpos($fileData, ':') !== false) {
                                            $parts = explode(':', $fileData);
                                            if (count($parts) >= 3) {
                                                list($format, $path, $id) = $parts;
                                                $audioFiles[] = ['format' => $format, 'path' => $path, 'id' => $id];
                                            }
                                        }
                                    }
                                }
                                ?>
                                <?php if (!empty($audioFiles)): ?>
                                    <div class="audio-versions">
                                        <?php foreach ($audioFiles as $file): ?>
                                            <span class="audio-format" style="background: #1db954; color: #000; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; margin-right: 0.5rem;">
                                                <?= strtoupper($file['format']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="song-actions">
                                <?php if (!empty($audioFiles)): ?>
                                    <div class="song-controls-row">
                                        <select onchange="testPlayMultiple(this, '<?= htmlspecialchars($song['title']) ?>', '<?= htmlspecialchars($song['artist_name']) ?>')" class="format-selector">
                                            <option value="">Formato</option>
                                            <?php foreach ($audioFiles as $file): ?>
                                                <option value="<?= htmlspecialchars($file['path']) ?>"><?= strtoupper($file['format']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button onclick="togglePlayPause()" class="test-btn play-pause-btn" title="Reproduzir/Pausar">
                                            <i class="fas fa-play"></i>
                                        </button>
                                        <button onclick="stopTestAudio()" class="test-btn stop-btn" title="Parar">
                                            <i class="fas fa-stop"></i>
                                        </button>
                                        <button onclick="editSong(<?= $song['id'] ?>, '<?= htmlspecialchars($song['title'], ENT_QUOTES) ?>', <?= $song['artist_id'] ?>, <?= $song['album_id'] ?: 'null' ?>, '<?= $song['duration'] ?>')" class="edit-btn">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <button onclick="editSong(<?= $song['id'] ?>, '<?= htmlspecialchars($song['title'], ENT_QUOTES) ?>', <?= $song['artist_id'] ?>, <?= $song['album_id'] ?: 'null' ?>, '<?= $song['duration'] ?>')" class="edit-btn">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                <?php endif; ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete_song">
                                    <input type="hidden" name="id" value="<?= $song['id'] ?>">
                                    <button type="submit" class="delete-btn" onclick="return confirm('Deletar música?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Álbuns -->
        <div id="albums" class="tab-content">
            <div class="admin-section">
                <h2>Adicionar Álbum</h2>
                <form method="POST" enctype="multipart/form-data" class="admin-form">
                    <input type="hidden" name="action" value="add_album">
                    <input type="text" name="title" placeholder="Título do Álbum" required>
                    <select name="artist_id" required>
                        <option value="">Selecione o Artista</option>
                        <?php foreach ($artists as $artist): ?>
                            <option value="<?= $artist['id'] ?>"><?= htmlspecialchars($artist['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="url" name="image" placeholder="URL da Imagem do Álbum">
                    <div class="file-upload">
                        <label for="album_image_file">
                            <i class="fas fa-image"></i>
                            <span>OU envie arquivo de imagem</span>
                            <small>JPG, PNG, GIF</small>
                        </label>
                        <input type="file" id="album_image_file" name="image_file" accept="image/*">
                    </div>
                    <button type="submit">Adicionar</button>
                </form>
            </div>

            <div class="admin-section">
                <h2>Álbuns Cadastrados</h2>
                <div class="admin-grid">
                    <?php foreach ($albums as $album): ?>
                        <div class="admin-item">
                            <img src="<?= htmlspecialchars($album['image']) ?>" alt="<?= htmlspecialchars($album['title']) ?>">
                            <h4><?= htmlspecialchars($album['title']) ?></h4>
                            <p><?= htmlspecialchars($album['artist_name']) ?></p>
                            <button onclick="openAlbumModal(<?= $album['id'] ?>)" class="edit-btn" style="position: absolute; top: 0.5rem; left: 0.5rem;" title="Ver Músicas">
                                <i class="fas fa-music"></i>
                            </button>
                            <button onclick="editAlbum(<?= $album['id'] ?>, '<?= htmlspecialchars($album['title'], ENT_QUOTES) ?>', <?= $album['artist_id'] ?>, '<?= htmlspecialchars($album['image'], ENT_QUOTES) ?>')" class="edit-btn" style="position: absolute; top: 0.5rem; left: 3rem;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete_album">
                                <input type="hidden" name="id" value="<?= $album['id'] ?>">
                                <button type="submit" class="delete-btn" onclick="return confirm('Deletar álbum?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </main>



    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
        
        function testPlay(title, artist, filePath) {
            // Manter função original para compatibilidade
            currentTestPath = filePath;
            playTestAudio();
        }
        
        let currentTestAudio = null;
        let currentTestPath = null;
        
        function testPlayMultiple(select, title, artist) {
            const filePath = select.value;
            if (filePath) {
                currentTestPath = filePath;
                updateTestControls(select.closest('.test-controls'));
            }
        }
        
        function playTestAudio() {
            if (currentTestPath) {
                if (currentTestAudio) {
                    currentTestAudio.pause();
                }
                
                currentTestAudio = new Audio('../../../' + currentTestPath);
                currentTestAudio.volume = 0.5;
                currentTestAudio.play().catch(e => {
                    alert('Erro ao reproduzir: ' + e.message);
                });
                
                currentTestAudio.addEventListener('ended', () => {
                    updateTestControlsState('stopped');
                });
                
                updateTestControlsState('playing');
            } else {
                alert('Selecione um formato primeiro');
            }
        }
        
        function togglePlayPause() {
            if (!currentTestPath) {
                alert('Selecione um formato primeiro');
                return;
            }
            
            if (!currentTestAudio || currentTestAudio.ended) {
                // Iniciar nova reprodução
                if (currentTestAudio) {
                    currentTestAudio.pause();
                }
                
                currentTestAudio = new Audio('../../../' + currentTestPath);
                currentTestAudio.volume = 0.5;
                currentTestAudio.play().catch(e => {
                    alert('Erro ao reproduzir: ' + e.message);
                });
                
                currentTestAudio.addEventListener('ended', () => {
                    updateTestControlsState('stopped');
                });
                
                updateTestControlsState('playing');
            } else if (currentTestAudio.paused) {
                // Retomar reprodução
                currentTestAudio.play();
                updateTestControlsState('playing');
            } else {
                // Pausar reprodução
                currentTestAudio.pause();
                updateTestControlsState('paused');
            }
        }
        
        function pauseTestAudio() {
            // Manter função para compatibilidade
            togglePlayPause();
        }
        
        function stopTestAudio() {
            if (currentTestAudio) {
                currentTestAudio.pause();
                currentTestAudio.currentTime = 0;
                updateTestControlsState('stopped');
            }
        }
        
        function updateTestControls(container) {
            const playBtn = container.querySelector('.play-btn');
            const pauseBtn = container.querySelector('.pause-btn');
            const stopBtn = container.querySelector('.stop-btn');
            
            if (playBtn && pauseBtn && stopBtn) {
                playBtn.disabled = false;
                pauseBtn.disabled = true;
                pauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                stopBtn.disabled = true;
            }
        }
        
        function updateTestControlsState(state) {
            const allContainers = document.querySelectorAll('.song-controls-row');
            
            allContainers.forEach(container => {
                const playPauseBtn = container.querySelector('.play-pause-btn');
                const stopBtn = container.querySelector('.stop-btn');
                
                if (playPauseBtn && stopBtn) {
                    switch(state) {
                        case 'playing':
                            playPauseBtn.innerHTML = '<i class="fas fa-pause"></i>';
                            playPauseBtn.title = 'Pausar';
                            stopBtn.disabled = false;
                            break;
                        case 'paused':
                            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                            playPauseBtn.title = 'Retomar';
                            stopBtn.disabled = false;
                            break;
                        case 'stopped':
                            playPauseBtn.innerHTML = '<i class="fas fa-play"></i>';
                            playPauseBtn.title = 'Reproduzir';
                            stopBtn.disabled = true;
                            break;
                    }
                }
            });
        }
        
        function editSong(id, title, artistId, albumId, duration) {
            closeAllModals();
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('editSongId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editArtistId').value = artistId;
            document.getElementById('editAlbumId').value = albumId || '';
            document.getElementById('editDuration').value = duration || '';
            
            // Carregar arquivos de áudio
            loadAudioFiles(id);
        }
        
        function loadAudioFiles(songId) {
            document.getElementById('addAudioSongId').value = songId;
            
            fetch(`../../controllers/api/songs.php?action=get_audio_files&song_id=${songId}`)
                .then(response => response.json())
                .then(files => {
                    const container = document.getElementById('audioFilesContainer');
                    container.innerHTML = '';
                    
                    if (files.length === 0) {
                        container.innerHTML = '<p style="color: #b3b3b3; text-align: center; padding: 1rem;">Nenhum arquivo de áudio encontrado</p>';
                        return;
                    }
                    
                    files.forEach(file => {
                        const fileDiv = document.createElement('div');
                        fileDiv.className = 'audio-file-item';
                        fileDiv.innerHTML = `
                            <div class="file-info">
                                <strong>${file.file_format.toUpperCase()}</strong>
                                <span>${file.file_path.split('/').pop()}</span>
                                <small>${(file.file_size / 1024 / 1024).toFixed(2)} MB</small>
                            </div>
                            <div class="file-actions">
                                <button type="button" onclick="editAudioFile(${file.id}, '${file.file_format}', '${file.file_path}')" class="edit-file-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" onclick="deleteAudioFile(${file.id})" class="delete-file-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                        container.appendChild(fileDiv);
                    });
                });
        }
        
        function deleteAudioFile(fileId) {
            if (confirm('Deletar este arquivo de áudio?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_audio_file">
                    <input type="hidden" name="file_id" value="${fileId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
        
        function editAudioFile(fileId, format, path) {
            // Implementar edição de arquivo individual se necessário
            alert(`Editar arquivo ${format.toUpperCase()}: ${path.split('/').pop()}`);
        }
        
        function editArtist(id, name, image) {
            closeAllModals();
            
            // Criar formulário dinamicamente
            const form = document.querySelector('#editArtistModal form');
            form.innerHTML = `
                <input type="hidden" name="action" value="edit_artist">
                <input type="hidden" name="artist_id" value="${id}">
                
                <label>Nome:</label>
                <input type="text" name="name" value="${name}" required>
                
                <label>URL da Imagem:</label>
                <input type="url" name="image" value="${image || ''}" placeholder="https://exemplo.com/imagem.jpg">
                
                <label>OU envie arquivo:</label>
                <div class="file-upload" style="margin-bottom: 1rem;">
                    <label for="edit_artist_image_file" style="cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: #282828; border: 2px dashed #1db954; border-radius: 8px;">
                        <i class="fas fa-image" style="font-size: 1.5rem; color: #1db954;"></i>
                        <span style="font-weight: 600; color: #ffffff;">Clique para enviar imagem</span>
                        <small style="color: #b3b3b3; font-size: 0.8rem;">JPG, PNG, GIF</small>
                    </label>
                    <input type="file" id="edit_artist_image_file" name="image_file" accept="image/*" style="display: none;">
                </div>
                
                <button type="submit">Salvar Alterações</button>
                <button type="button" onclick="closeEditArtistModal()" style="background: #666; color: #fff;">Cancelar</button>
            `;
            
            document.getElementById('editArtistModal').style.display = 'block';
        }
        
        function editAlbum(id, title, artistId, image) {
            closeAllModals();
            
            // Criar formulário dinamicamente
            const form = document.querySelector('#editAlbumModal form');
            const artistOptions = <?= json_encode($artists) ?>;
            
            let artistSelect = '<select name="artist_id" required>';
            artistOptions.forEach(artist => {
                const selected = artist.id == artistId ? 'selected' : '';
                artistSelect += `<option value="${artist.id}" ${selected}>${artist.name}</option>`;
            });
            artistSelect += '</select>';
            
            form.innerHTML = `
                <input type="hidden" name="action" value="edit_album">
                <input type="hidden" name="album_id" value="${id}">
                
                <label>Título:</label>
                <input type="text" name="title" value="${title}" required>
                
                <label>Artista:</label>
                ${artistSelect}
                
                <label>URL da Imagem:</label>
                <input type="url" name="image" value="${image || ''}" placeholder="https://exemplo.com/imagem.jpg">
                
                <label>OU envie arquivo:</label>
                <div class="file-upload" style="margin-bottom: 1rem;">
                    <label for="edit_album_image_file" style="cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1rem; background: #282828; border: 2px dashed #1db954; border-radius: 8px;">
                        <i class="fas fa-image" style="font-size: 1.5rem; color: #1db954;"></i>
                        <span style="font-weight: 600; color: #ffffff;">Clique para enviar imagem</span>
                        <small style="color: #b3b3b3; font-size: 0.8rem;">JPG, PNG, GIF</small>
                    </label>
                    <input type="file" id="edit_album_image_file" name="image_file" accept="image/*" style="display: none;">
                </div>
                
                <button type="submit">Salvar Alterações</button>
                <button type="button" onclick="closeEditAlbumModal()" style="background: #666; color: #fff;">Cancelar</button>
            `;
            
            document.getElementById('editAlbumModal').style.display = 'block';
        }
        
        function closeEditArtistModal() {
            document.getElementById('editArtistModal').style.display = 'none';
        }
        
        function closeEditAlbumModal() {
            document.getElementById('editAlbumModal').style.display = 'none';
        }
        
        async function openAlbumModal(albumId) {
            closeAllModals();
            const modal = document.getElementById('albumModal');
            const songsContainer = document.getElementById('albumSongs');
            
            modal.style.display = 'flex';
            songsContainer.innerHTML = '<div class="loading-placeholder"><div class="loading-spinner"></div><p>Carregando músicas...</p></div>';
            
            try {
                const response = await fetch(`../../controllers/api/songs.php?action=get_album_songs&album_id=${albumId}`);
                const data = await response.json();
                
                if (data.success && data.album) {
                    document.getElementById('albumTitle').textContent = data.album.title;
                    document.getElementById('albumArtist').textContent = data.album.artist_name;
                    document.getElementById('albumCover').src = data.album.image;
                    
                    songsContainer.innerHTML = '';
                    
                    // Adicionar botão para adicionar músicas
                    const addSongButton = document.createElement('div');
                    addSongButton.className = 'add-song-to-album';
                    addSongButton.innerHTML = `
                        <button onclick="showAddSongToAlbum(${albumId})" class="add-song-btn">
                            <i class="fas fa-plus"></i> Adicionar Música ao Álbum
                        </button>
                    `;
                    songsContainer.appendChild(addSongButton);
                    
                    if (data.songs.length > 0) {
                        data.songs.forEach((song, index) => {
                            const songElement = document.createElement('div');
                            songElement.className = 'album-song-item';
                            
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
                                <div class="song-actions">
                                    <button onclick="editSong(${song.id}, '${song.title}', ${data.album.artist_id}, ${albumId}, '${song.duration}'); event.stopPropagation();" class="edit-btn-small">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="removeSongFromAlbum(${song.id}, ${albumId}); event.stopPropagation();" class="remove-btn-small" title="Remover do Álbum">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            `;
                            
                            songsContainer.appendChild(songElement);
                        });
                    } else {
                        const noSongsMsg = document.createElement('p');
                        noSongsMsg.className = 'no-songs';
                        noSongsMsg.textContent = 'Nenhuma música encontrada neste álbum.';
                        songsContainer.appendChild(noSongsMsg);
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
        
        async function showAddSongToAlbum(albumId) {
            try {
                const response = await fetch(`../../controllers/api/songs.php?action=get_available_songs&album_id=${albumId}`);
                const data = await response.json();
                
                if (data.success && data.songs.length > 0) {
                    let songsHtml = '<h3>Selecione as músicas para adicionar:</h3><div class="available-songs-list">';
                    
                    data.songs.forEach(song => {
                        songsHtml += `
                            <div class="available-song-item">
                                <input type="checkbox" id="song_${song.id}" value="${song.id}">
                                <label for="song_${song.id}">
                                    <strong>${song.title}</strong>
                                    <span>${song.artist_name}</span>
                                </label>
                            </div>
                        `;
                    });
                    
                    songsHtml += `
                        </div>
                        <div class="add-songs-actions">
                            <button onclick="addSelectedSongsToAlbum(${albumId})" class="add-selected-btn">
                                <i class="fas fa-plus"></i> Adicionar Selecionadas
                            </button>
                            <button onclick="closeAddSongsModal()" class="cancel-btn">
                                Cancelar
                            </button>
                        </div>
                    `;
                    
                    document.getElementById('addSongsModalContent').innerHTML = songsHtml;
                    closeAllModals();
                    document.getElementById('addSongsModal').style.display = 'flex';
                } else {
                    alert('Não há músicas disponíveis para adicionar a este álbum.');
                }
            } catch (error) {
                console.error('Erro ao carregar músicas disponíveis:', error);
                alert('Erro ao carregar músicas disponíveis.');
            }
        }
        
        function closeAddSongsModal() {
            document.getElementById('addSongsModal').style.display = 'none';
        }
        
        async function addSelectedSongsToAlbum(albumId) {
            const checkboxes = document.querySelectorAll('#addSongsModal input[type="checkbox"]:checked');
            const songIds = Array.from(checkboxes).map(cb => cb.value);
            
            if (songIds.length === 0) {
                alert('Selecione pelo menos uma música.');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('action', 'add_songs_to_album');
                formData.append('album_id', albumId);
                formData.append('song_ids', JSON.stringify(songIds));
                
                const response = await fetch('admin.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (response.ok) {
                    closeAddSongsModal();
                    openAlbumModal(albumId); // Recarregar o modal do álbum
                } else {
                    alert('Erro ao adicionar músicas ao álbum.');
                }
            } catch (error) {
                console.error('Erro ao adicionar músicas:', error);
                alert('Erro ao adicionar músicas ao álbum.');
            }
        }
        
        async function removeSongFromAlbum(songId, albumId) {
            if (confirm('Remover esta música do álbum?')) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'remove_song_from_album');
                    formData.append('song_id', songId);
                    
                    const response = await fetch('admin.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (response.ok) {
                        openAlbumModal(albumId); // Recarregar o modal do álbum
                    } else {
                        alert('Erro ao remover música do álbum.');
                    }
                } catch (error) {
                    console.error('Erro ao remover música:', error);
                    alert('Erro ao remover música do álbum.');
                }
            }
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        function closeAllModals() {
            const modals = ['editModal', 'editArtistModal', 'editAlbumModal', 'albumModal', 'addSongsModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) modal.style.display = 'none';
            });
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
        
        // Atualizar texto do label quando arquivo for selecionado
        document.addEventListener('change', function(e) {
            if (e.target.type === 'file' && e.target.files.length > 0) {
                const label = e.target.previousElementSibling;
                const fileName = e.target.files[0].name;
                const fileSize = (e.target.files[0].size / 1024 / 1024).toFixed(2);
                
                label.innerHTML = `
                    <i class="fas fa-check-circle" style="color: #1db954;"></i>
                    <span style="color: #1db954;">${fileName}</span>
                    <small>${fileSize} MB selecionado</small>
                `;
            }
        });
    </script>

    <!-- Modal de Edição -->
    <div id="editModal" class="edit-modal">
        <div class="edit-modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Editar Música</h2>
            <form method="POST" class="edit-form">
                <input type="hidden" name="action" value="edit_song">
                <input type="hidden" id="editSongId" name="song_id">
                
                <label>Título:</label>
                <input type="text" id="editTitle" name="title" required>
                
                <label>Artista:</label>
                <select id="editArtistId" name="artist_id" required>
                    <?php foreach ($artists as $artist): ?>
                        <option value="<?= $artist['id'] ?>"><?= htmlspecialchars($artist['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Álbum (opcional):</label>
                <select id="editAlbumId" name="album_id">
                    <option value="">Nenhum</option>
                    <?php foreach ($albums as $album): ?>
                        <option value="<?= $album['id'] ?>"><?= htmlspecialchars($album['title']) ?> - <?= htmlspecialchars($album['artist_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>Duração:</label>
                <input type="time" id="editDuration" name="duration" step="1">
                
                <button type="submit">Salvar Alterações</button>
                <button type="button" onclick="closeEditModal()" style="background: #666; color: #fff;">Cancelar</button>
            </form>
            
            <div class="audio-files-section">
                <h3>Arquivos de Áudio</h3>
                <div id="audioFilesContainer" class="audio-files-list"></div>
                
                <form method="POST" enctype="multipart/form-data" class="add-audio-form">
                    <input type="hidden" name="action" value="add_audio_file">
                    <input type="hidden" id="addAudioSongId" name="song_id">
                    
                    <div class="file-upload-container">
                        <label for="new_audio_file" class="file-upload-label">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Clique aqui para adicionar o arquivo da sua música</span>
                            <small>Formatos: MP3, WAV, OGG, FLAC, M4A, AAC</small>
                        </label>
                        <input type="file" id="new_audio_file" name="new_audio_file" accept=".mp3,.wav,.ogg,.flac,.m4a,.aac" required class="file-input">
                    </div>
                    
                    <button type="submit" class="add-file-btn">
                        <i class="fas fa-plus"></i>
                        Adicionar Arquivo
                    </button>
                </form>
            </div>
            </form>
        </div>
    </div>

    <!-- Modal de Edição de Artista -->
    <div id="editArtistModal" class="edit-modal">
        <div class="edit-modal-content">
            <span class="close" onclick="closeEditArtistModal()">&times;</span>
            <h2>Editar Artista</h2>
            <form method="POST" action="admin.php" enctype="multipart/form-data" class="edit-form">
                <input type="hidden" name="action" value="edit_artist">
                <input type="hidden" id="editArtistId" name="artist_id">
                
                <label>Nome:</label>
                <input type="text" id="editArtistName" name="name" required>
                
                <label>URL da Imagem:</label>
                <input type="url" id="editArtistImage" name="image" placeholder="https://exemplo.com/imagem.jpg">
                
                <button type="submit">Salvar Alterações</button>
                <button type="button" onclick="closeEditArtistModal()" style="background: #666; color: #fff;">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal de Edição de Álbum -->
    <div id="editAlbumModal" class="edit-modal">
        <div class="edit-modal-content">
            <span class="close" onclick="closeEditAlbumModal()">&times;</span>
            <h2>Editar Álbum</h2>
            <form method="POST" action="admin.php" enctype="multipart/form-data" class="edit-form">
                <input type="hidden" name="action" value="edit_album">
                <input type="hidden" id="editAlbumId" name="album_id">
                
                <label>Título:</label>
                <input type="text" id="editAlbumTitle" name="title" required>
                
                <label>Artista:</label>
                <select id="editAlbumArtistId" name="artist_id" required>
                    <?php foreach ($artists as $artist): ?>
                        <option value="<?= $artist['id'] ?>"><?= htmlspecialchars($artist['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                
                <label>URL da Imagem:</label>
                <input type="url" id="editAlbumImage" name="image" placeholder="https://exemplo.com/imagem.jpg">
                
                <button type="submit">Salvar Alterações</button>
                <button type="button" onclick="closeEditAlbumModal()" style="background: #666; color: #fff;">Cancelar</button>
            </form>
        </div>
    </div>

    <!-- Modal de Músicas do Álbum -->
    <div id="albumModal" class="album-modal">
        <div class="album-modal-content">
            <span class="close" onclick="closeAlbumModal()">&times;</span>
            <div class="album-header">
                <img id="albumCover" src="" alt="Capa do Álbum">
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
    
    <!-- Modal para Adicionar Músicas ao Álbum -->
    <div id="addSongsModal" class="add-songs-modal">
        <div class="add-songs-modal-content">
            <span class="close" onclick="closeAddSongsModal()">&times;</span>
            <div id="addSongsModalContent">
                <!-- Conteúdo será carregado dinamicamente -->
            </div>
        </div>
    </div>
</body>
</html>