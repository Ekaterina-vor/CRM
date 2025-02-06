<?php
session_start();
require_once '../DB.php';

if(isset($_GET['id'])) {
    $orderId = $_GET['id'];
    
    // Обновляем статус заказа на 0 (архив)
    $sql = "UPDATE orders SET status = '0' WHERE id = :id";
    $stmt = $DB->prepare($sql);
    $stmt->execute(['id' => $orderId]);
    
    header('Location: ../../orders.php');
    exit;
}
?>