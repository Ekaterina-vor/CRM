<?php
session_start();
require_once '../DB.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';
    
    if (empty($id) || !isset($status)) {
        $_SESSION['orders-errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"><h4 style="margin: 0;">Все поля должны быть заполнены</h4></div>';
        header('Location: ../../orders.php');
        exit;
    }

    try {
        $stmt = $DB->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $result = $stmt->execute([$status, $id]);
        
        if ($result) {
            header('Location: ../../orders.php');
            exit;
        } else {
            $_SESSION['orders-errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"><h4 style="margin: 0;">Ошибка при обновлении статуса заказа</h4></div>';
            header('Location: ../../orders.php');
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['orders-errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"><h4 style="margin: 0;">Ошибка базы данных: ' . $e->getMessage() . '</h4></div>';
        header('Location: ../../orders.php');
        exit;
    }
}
?> 