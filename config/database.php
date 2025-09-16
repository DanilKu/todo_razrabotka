<?php
/**
 * Конфигурация базы данных
 */

// Параметры подключения к базе данных
$host = 'localhost';
$dbname = 'todo_oskarev';
$username = 'root';
$password = '';

try {
    // Создание PDO подключения
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Установка режима ошибок PDO на исключения
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Установка режима выборки по умолчанию
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Функция для безопасного выполнения запросов
function executeQuery($pdo, $sql, $params = []) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch(PDOException $e) {
        throw new Exception("Ошибка выполнения запроса: " . $e->getMessage());
    }
}

// Функция для получения всех задач
function getAllTasks($pdo) {
    $sql = "SELECT * FROM Tasks ORDER BY CreatedAt DESC";
    return executeQuery($pdo, $sql)->fetchAll();
}

// Функция для получения задачи по ID
function getTaskById($pdo, $id) {
    $sql = "SELECT * FROM Tasks WHERE Id = ?";
    return executeQuery($pdo, $sql, [$id])->fetch();
}

// Функция для добавления новой задачи
function addTask($pdo, $title, $description, $dueDate) {
    $sql = "INSERT INTO Tasks (Title, Description, DueDate) VALUES (?, ?, ?)";
    executeQuery($pdo, $sql, [$title, $description, $dueDate]);
    return $pdo->lastInsertId();
}

// Функция для обновления задачи
function updateTask($pdo, $id, $title, $description, $isCompleted, $dueDate) {
    $sql = "UPDATE Tasks SET Title = ?, Description = ?, IsCompleted = ?, DueDate = ? WHERE Id = ?";
    return executeQuery($pdo, $sql, [$title, $description, $isCompleted, $dueDate, $id])->rowCount();
}

// Функция для удаления задачи
function deleteTask($pdo, $id) {
    $sql = "DELETE FROM Tasks WHERE Id = ?";
    return executeQuery($pdo, $sql, [$id])->rowCount();
}

// Функция для получения задач по статусу
function getTasksByStatus($pdo, $isCompleted) {
    $sql = "SELECT * FROM Tasks WHERE IsCompleted = ? ORDER BY CreatedAt DESC";
    return executeQuery($pdo, $sql, [$isCompleted])->fetchAll();
}
?>
