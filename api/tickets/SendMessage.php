<?php
session_start();
require_once '../DB.php';

header('Content-Type: application/json');

// Получаем данные из POST-запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['ticket_id']) || !isset($data['message']) || empty(trim($data['message']))) {
    echo json_encode(['error' => 'Не все данные предоставлены']);
    exit;
}

try {
    // Проверяем существование таблицы ticket_messages
    $checkTable = $DB->query("SHOW TABLES LIKE 'ticket_messages'");
    if ($checkTable->rowCount() == 0) {
        $DB->exec("
            CREATE TABLE ticket_messages (
                id INT AUTO_INCREMENT PRIMARY KEY,
                ticket_id INT NOT NULL,
                user_id INT NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
    }

    // Получаем ID пользователя
    $stmt = $DB->prepare("SELECT id FROM users WHERE token = ?");
    $stmt->execute([$_SESSION['token']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode(['error' => 'Пользователь не найден']);
        exit;
    }

    // Проверяем, имеет ли пользователь доступ к этому тикету
    $stmt = $DB->prepare("SELECT id FROM tickets WHERE id = ? AND (clients = ? OR admin = ?)");
    $stmt->execute([$data['ticket_id'], $user['id'], $user['id']]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['error' => 'Доступ запрещен']);
        exit;
    }

    // Сохраняем сообщение
    $stmt = $DB->prepare("INSERT INTO ticket_messages (ticket_id, user_id, message) VALUES (?, ?, ?)");
    $result = $stmt->execute([
        $data['ticket_id'],
        $user['id'],
        trim($data['message'])
    ]);

    if ($result) {
        // Обновляем статус тикета на "в работе", если он был в статусе "ожидает"
        $stmt = $DB->prepare("UPDATE tickets SET status = 'in_progress' WHERE id = ? AND status = 'waiting'");
        $stmt->execute([$data['ticket_id']]);

        echo json_encode([
            'success' => true,
            'message' => 'Сообщение успешно отправлено'
        ]);
    } else {
        throw new Exception("Ошибка при сохранении сообщения");
    }

} catch (Exception $e) {
    error_log("Error in SendMessage.php: " . $e->getMessage());
    echo json_encode([
        'error' => 'Ошибка при отправке сообщения: ' . $e->getMessage()
    ]);
} 