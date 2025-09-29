USE ressonance_music;

-- Criar tabela para múltiplas versões de áudio
CREATE TABLE IF NOT EXISTS song_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    song_id INT NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_format VARCHAR(10) NOT NULL,
    file_size INT,
    uploaded_by INT,
    is_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (song_id) REFERENCES songs(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id)
);

-- Remover coluna file_path da tabela songs (será migrada)
-- ALTER TABLE songs DROP COLUMN file_path;