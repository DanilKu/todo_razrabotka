-- Создание базы данных todo_oskarev
CREATE DATABASE IF NOT EXISTS todo_oskarev CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Использование базы данных
USE todo_oskarev;

-- Создание таблицы Tasks
CREATE TABLE IF NOT EXISTS Tasks (
    Id INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(255) NOT NULL,
    Description TEXT,
    IsCompleted BOOLEAN DEFAULT FALSE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    DueDate DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставка тестовых данных
INSERT INTO Tasks (Title, Description, IsCompleted, DueDate) VALUES
('Изучить PHP', 'Изучить основы программирования на PHP', FALSE, '2024-01-15 18:00:00'),
('Создать проект', 'Разработать веб-приложение для управления задачами', FALSE, '2024-01-20 20:00:00'),
('Протестировать приложение', 'Проверить все функции приложения', TRUE, '2024-01-10 16:00:00');
