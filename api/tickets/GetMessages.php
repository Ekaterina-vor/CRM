<?php
session_start();
require_once '../DB.php';

header('Content-Type: application/json');

if (!isset($_GET['ticket_id'])) {
    echo json_encode(['error' => 'Не указан ID тикета']);
    exit;
}

try {
    // Проверяем, имеет ли пользователь доступ к этому тикету
    $stmt = $DB->prepare("SELECT id FROM tickets WHERE id = ? AND (clients = (SELECT id FROM users WHERE token = ?) OR admin = (SELECT id FROM users WHERE token = ?))");
    $stmt->execute([$_GET['ticket_id'], $_SESSION['token'], $_SESSION['token']]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['error' => 'Доступ запрещен']);
        exit;
    }

    // Получаем сообщения
    $stmt = $DB->prepare("
        SELECT m.*, u.type as user_type 
        FROM ticket_messages m 
        JOIN users u ON m.user_id = u.id 
        WHERE m.ticket_id = ? 
        ORDER BY m.created_at ASC
    ");
    $stmt->execute([$_GET['ticket_id']]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Форматируем сообщения для фронтенда
    $formattedMessages = array_map(function($message) {
        return [
            'message' => $message['message'],
            'is_admin' => $message['user_type'] === 'tech',
            'created_at' => date('d.m.Y H:i', strtotime($message['created_at']))
        ];
    }, $messages);

    echo json_encode($formattedMessages);

} catch (Exception $e) {
    echo json_encode(['error' => 'Ошибка при получении сообщений']);
} 