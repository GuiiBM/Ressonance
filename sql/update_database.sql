-- Adicionar tabela de usuários
USE ressonance_music;
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    google_id VARCHAR(255) UNIQUE,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    age INT,
    city VARCHAR(255),
    profile_picture VARCHAR(500),
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Adicionar usuário admin padrão
INSERT INTO users (name, email, is_admin) VALUES 
('Admin', 'admin@ressonance.com', TRUE)
ON DUPLICATE KEY UPDATE is_admin = TRUE;