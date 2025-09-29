<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_logged']) || !$_SESSION['user_logged']) {
    header('Location: auto_oauth.php');
    exit;
}

require_once 'config.php';

$songs = $pdo->query("SELECT s.*, ar.name as artist_name FROM songs s JOIN artists ar ON s.artist_id = ar.id ORDER BY s.title")->fetchAll(PDO::FETCH_ASSOC);

if ($_POST && isset($_POST['upload_audio'])) {
    $song_id = $_POST['song_id'];
    $user_id = $_SESSION['user_id'];
    $is_same_song = isset($_POST['confirm_same_song']);
    
    if (!$is_same_song) {
        $error = "Você deve confirmar que é a mesma música!";
    } else if (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] === 0) {
        $uploadDir = 'audio/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileInfo = pathinfo($_FILES['audio_file']['name']);
        $format = strtolower($fileInfo['extension']);
        $allowedFormats = ['mp3', 'wav', 'ogg', 'flac', 'm4a', 'aac'];
        
        if (in_array($format, $allowedFormats)) {
            $fileName = $song_id . '_' . time() . '.' . $format;
            $uploadPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['audio_file']['tmp_name'], $uploadPath)) {
                $stmt = $pdo->prepare("INSERT INTO song_files (song_id, file_path, file_format, file_size, uploaded_by) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$song_id, $fileName, $format, $_FILES['audio_file']['size'], $user_id]);
                
                $success = "Arquivo enviado com sucesso! Aguarde verificação.";
            } else {
                $error = "Erro ao fazer upload do arquivo.";
            }
        } else {
            $error = "Formato não suportado. Use: " . implode(', ', $allowedFormats);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Música - Ressonance</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <div class="logo-container">
                <div class="logo-text">RESSONANCE</div>
            </div>
        </div>
        <div class="profile">
            <a href="index.php">HOME</a>
            <a href="auto_oauth.php">PERFIL</a>
        </div>
    </header>

    <main style="margin-top: 70px; padding: 2rem; max-width: 800px; margin-left: auto; margin-right: auto;">
        <h1 style="margin-bottom: 2rem;">📤 Upload de Música</h1>
        
        <?php if (isset($success)): ?>
            <div style="background: #1db954; color: #000; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ✅ <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div style="background: #ff4b5a; color: #fff; padding: 1rem; border-radius: 8px; margin-bottom: 2rem;">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div style="background: #1d1d1d; padding: 2rem; border-radius: 12px;">
            <form method="POST" enctype="multipart/form-data">
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Selecione a Música:</label>
                    <select name="song_id" required style="width: 100%; padding: 0.75rem; background: #282828; border: 1px solid #404040; border-radius: 4px; color: #fff;">
                        <option value="">Escolha uma música...</option>
                        <?php foreach ($songs as $song): ?>
                            <option value="<?= $song['id'] ?>"><?= htmlspecialchars($song['title']) ?> - <?= htmlspecialchars($song['artist_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Arquivo de Áudio:</label>
                    <input type="file" name="audio_file" accept=".mp3,.wav,.ogg,.flac,.m4a,.aac" required 
                           style="width: 100%; padding: 0.75rem; background: #282828; border: 1px solid #404040; border-radius: 4px; color: #fff;">
                    <small style="color: #b3b3b3; margin-top: 0.5rem; display: block;">
                        Formatos suportados: MP3, WAV, OGG, FLAC, M4A, AAC
                    </small>
                </div>

                <div style="background: #ff4b5a; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <h3 style="color: #fff; margin-bottom: 1rem;">⚠️ AVISO IMPORTANTE</h3>
                    <p style="color: #fff; margin-bottom: 1rem;">
                        Você está enviando uma versão alternativa de uma música existente. 
                        <strong>CERTIFIQUE-SE</strong> de que é exatamente a mesma música, apenas em formato diferente.
                    </p>
                    <p style="color: #fff; margin-bottom: 1rem;">
                        <strong>Enviar músicas diferentes resultará em punição e banimento da plataforma.</strong>
                    </p>
                    <label style="display: flex; align-items: center; gap: 0.5rem; color: #fff; cursor: pointer;">
                        <input type="checkbox" name="confirm_same_song" required style="transform: scale(1.2);">
                        Confirmo que é a mesma música, apenas em formato diferente
                    </label>
                </div>

                <button type="submit" name="upload_audio" 
                        style="background: #1db954; color: #000; border: none; padding: 1rem 2rem; border-radius: 25px; font-weight: 600; cursor: pointer; font-size: 1rem;">
                    📤 Enviar Arquivo
                </button>
            </form>
        </div>
    </main>
</body>
</html>