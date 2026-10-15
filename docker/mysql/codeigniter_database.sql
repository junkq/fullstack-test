CREATE DATABASE IF NOT EXISTS codeigniter_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE codeigniter_db;

CREATE TABLE IF NOT EXISTS comments (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(254) NOT NULL,
    text TEXT NOT NULL,
    date DATE NOT NULL,
    created_at DATETIME NOT NULL,
    PRIMARY KEY (id),
    INDEX idx_comments_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
