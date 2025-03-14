<?php
session_start();

// Добавить запись обращения в БД
// client = id текущего пользователя
// admin = пустое значение

// Включаем вывод ошибок для отладки
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Подключаемся к базе данных
require_once '../DB.php';

// Логируем входящие данные
error_log("POST data: " . print_r($_POST, true));
error_log("Session token: " . $_SESSION['token']);

// Получаем данные из формы
$type = $_POST['support-type'] ?? '';
$message = $_POST['support-message'] ?? '';

// Проверяем обязательные поля
if (empty($message) || empty($type)) {
    $_SESSION['tickets-error'] = "Заполните все обязательные поля";
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Получаем ID пользователя из сессии
try {
    $stmt = $DB->prepare("SELECT id, type FROM users WHERE token = ?");
    $stmt->execute([$_SESSION['token']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $admin_id = $user['id'] ?? 0;
    error_log("Found admin ID: " . $admin_id);
    
    if ($admin_id === 0) {
        throw new Exception("Не удалось определить ID пользователя");
    }
} catch (Exception $e) {
    error_log("Error getting user ID: " . $e->getMessage());
    $_SESSION['tickets-error'] = "Ошибка: " . $e->getMessage();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

// Обработка загруженного файла
$file_path = null;
if (isset($_FILES['files']) && $_FILES['files']['error'] != UPLOAD_ERR_NO_FILE) {
    $upload_dir = '../../uploads/tickets/';
    
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file = $_FILES['files'];
    if ($file['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $file['tmp_name'];
        $name = $file['name'];
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $name);
        $server_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($tmp_name, $server_path)) {
            $file_path = 'uploads/tickets/' . $file_name;
            error_log("File uploaded successfully: " . $file_path);
        } else {
            error_log("Failed to move uploaded file");
        }
    }
}

try {
    // Проверяем существование таблицы tickets
    $checkTable = $DB->query("SHOW TABLES LIKE 'tickets'");
    if ($checkTable->rowCount() == 0) {
        $DB->exec("
            CREATE TABLE tickets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                type VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                clients INT NOT NULL,
                admin INT,
                status VARCHAR(50) DEFAULT 'waiting',
                file_path TEXT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
        error_log("Created tickets table");
    }
    
    // Сохраняем тикет
    $query = "INSERT INTO tickets (type, message, clients, admin, status, file_path) 
              VALUES (:type, :message, :clients, :admin, :status, :file_path)";
    
    $params = [
        ':type' => $type,
        ':message' => $message,
        ':clients' => $admin_id, // ID авторизованного пользователя
        ':admin' => $admin_id,   // Тот же ID в поле admin
        ':status' => 'waiting',
        ':file_path' => $file_path
    ];
    
    error_log("Executing query with params: " . print_r($params, true));
    
    $stmt = $DB->prepare($query);
    $result = $stmt->execute($params);
    
    if ($result) {
        error_log("Ticket saved successfully");
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?success=ticket_created');
        exit;
    } else {
        $error = $stmt->errorInfo();
        error_log("Database error: " . print_r($error, true));
        throw new Exception("Ошибка при сохранении тикета: " . $error[2]);
    }
} catch (Exception $e) {
    error_log("Error saving ticket: " . $e->getMessage());
    $_SESSION['tickets-error'] = "Ошибка: " . $e->getMessage();
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
?>