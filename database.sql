-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS ressonance_music;
USE ressonance_music;

-- Tabela de artistas
CREATE TABLE artists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    image VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabela de álbuns
CREATE TABLE albums (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist_id INT,
    image VARCHAR(500),
    release_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artists(id)
);

-- Tabela de músicas
CREATE TABLE songs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    artist_id INT,
    album_id INT,
    duration TIME,
    file_path VARCHAR(500),
    plays INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (artist_id) REFERENCES artists(id),
    FOREIGN KEY (album_id) REFERENCES albums(id)
);

-- Tabela de playlists
CREATE TABLE playlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(500),
    song_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir dados de exemplo
INSERT INTO artists (name, image) VALUES
('The Weeknd', 'https://via.placeholder.com/160x160/ff9f43/ffffff?text=TW'),
('Billie Eilish', 'https://via.placeholder.com/160x160/6c5ce7/ffffff?text=BE'),
('Dua Lipa', 'https://via.placeholder.com/160x160/fd79a8/ffffff?text=DL'),
('Ed Sheeran', 'https://via.placeholder.com/160x160/00b894/ffffff?text=ES'),
('Ariana Grande', 'https://via.placeholder.com/160x160/e17055/ffffff?text=AG');

INSERT INTO albums (title, artist_id, image, release_date) VALUES
('After Hours', 1, 'https://via.placeholder.com/160x160/ff9f43/ffffff?text=AH', '2020-03-20'),
('Happier Than Ever', 2, 'https://via.placeholder.com/160x160/6c5ce7/ffffff?text=HTE', '2021-07-30'),
('Future Nostalgia', 3, 'https://via.placeholder.com/160x160/fd79a8/ffffff?text=FN', '2020-03-27'),
('Divide', 4, 'https://via.placeholder.com/160x160/00b894/ffffff?text=DIV', '2017-03-03'),
('Positions', 5, 'https://via.placeholder.com/160x160/e17055/ffffff?text=POS', '2020-10-30');

INSERT INTO songs (title, artist_id, album_id, duration, plays) VALUES
('Blinding Lights', 1, 1, '00:03:20', 1500000),
('Save Your Tears', 1, 1, '00:03:35', 980000),
('Happier Than Ever', 2, 2, '00:04:58', 750000),
('Bad Guy', 2, 2, '00:03:14', 2100000),
('Levitating', 3, 3, '00:03:23', 1800000),
('Don\t Start Now', 3, 3, '00:03:03', 1650000),
('Shape of You', 4, 4, '00:03:53', 3200000),
('Perfect', 4, 4, '00:04:23', 2800000),
('positions', 5, 5, '00:02:52', 920000),
('34+35', 5, 5, '00:02:54', 1100000);

INSERT INTO playlists (name, description, image, song_count) VALUES
('Daily Mix 1', 'Made for you', 'https://via.placeholder.com/160x160/ff9f43/ffffff?text=Mix1', 25),
('Indie Rock', 'Best indie rock hits', 'https://via.placeholder.com/160x160/6c5ce7/ffffff?text=Rock', 50),
('Pop Hits', 'Top pop songs right now', 'https://via.placeholder.com/160x160/fd79a8/ffffff?text=Pop', 75),
('Jazz Classics', 'Timeless jazz music', 'https://via.placeholder.com/160x160/00b894/ffffff?text=Jazz', 30),
('Discover Weekly', 'Your weekly mixtape of fresh music', 'https://via.placeholder.com/160x160/e17055/ffffff?text=DW', 30),
('Release Radar', 'Catch all the latest music', 'https://via.placeholder.com/160x160/74b9ff/ffffff?text=RR', 20);