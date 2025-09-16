<?php
/**
 * API для работы с задачами
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Обработка preflight запросов
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';

// Получение метода запроса
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetRequest();
            break;
        case 'POST':
            handlePostRequest();
            break;
        case 'PUT':
            handlePutRequest();
            break;
        case 'DELETE':
            handleDeleteRequest();
            break;
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Метод не поддерживается']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Обработка GET запросов
 */
function handleGetRequest() {
    global $pdo;
    
    if (isset($_GET['id'])) {
        // Получение задачи по ID
        $task = getTaskById($pdo, $_GET['id']);
        if ($task) {
            echo json_encode(['success' => true, 'data' => $task]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Задача не найдена']);
        }
    } elseif (isset($_GET['status'])) {
        // Получение задач по статусу
        $isCompleted = $_GET['status'] === 'completed' ? 1 : 0;
        $tasks = getTasksByStatus($pdo, $isCompleted);
        echo json_encode(['success' => true, 'data' => $tasks]);
    } else {
        // Получение всех задач
        $tasks = getAllTasks($pdo);
        echo json_encode(['success' => true, 'data' => $tasks]);
    }
}

/**
 * Обработка POST запросов (создание новой задачи)
 */
function handlePostRequest() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['title']) || empty(trim($input['title']))) {
        http_response_code(400);
        echo json_encode(['error' => 'Название задачи обязательно']);
        return;
    }
    
    $title = trim($input['title']);
    $description = isset($input['description']) ? trim($input['description']) : '';
    $dueDate = isset($input['dueDate']) ? $input['dueDate'] : null;
    
    $taskId = addTask($pdo, $title, $description, $dueDate);
    $newTask = getTaskById($pdo, $taskId);
    
    echo json_encode(['success' => true, 'data' => $newTask, 'message' => 'Задача успешно создана']);
}

/**
 * Обработка PUT запросов (обновление задачи)
 */
function handlePutRequest() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID задачи обязателен']);
        return;
    }
    
    $id = $input['id'];
    $title = isset($input['title']) ? trim($input['title']) : '';
    $description = isset($input['description']) ? trim($input['description']) : '';
    $isCompleted = isset($input['isCompleted']) ? (bool)$input['isCompleted'] : false;
    $dueDate = isset($input['dueDate']) ? $input['dueDate'] : null;
    
    $rowsAffected = updateTask($pdo, $id, $title, $description, $isCompleted, $dueDate);
    
    if ($rowsAffected > 0) {
        $updatedTask = getTaskById($pdo, $id);
        echo json_encode(['success' => true, 'data' => $updatedTask, 'message' => 'Задача успешно обновлена']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Задача не найдена']);
    }
}

/**
 * Обработка DELETE запросов (удаление задачи)
 */
function handleDeleteRequest() {
    global $pdo;
    
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID задачи обязателен']);
        return;
    }
    
    $id = $input['id'];
    $rowsAffected = deleteTask($pdo, $id);
    
    if ($rowsAffected > 0) {
        echo json_encode(['success' => true, 'message' => 'Задача успешно удалена']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Задача не найдена']);
    }
}
?>
