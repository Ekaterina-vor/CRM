<?php
require_once '../../config/database.php';

$clientData = $_POST;
$id = $clientData['id'];

try {
    // Подготавливаем данные для обновления
    $name = $clientData['fullname'];
    $email = $clientData['email'];
    $phone = $clientData['phone'];
    
    // Формируем SQL запрос
    $sql = "UPDATE clients SET 
            name = :name,
            email = :email,
            phone = :phone
            WHERE id = :id";
            
    $stmt = $pdo->prepare($sql);
    
    // Выполняем запрос
    $result = $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone
    ]);
    
    if ($result) {
        header('Location: ../../clients.php');
        exit;
    } else {
        $_SESSION['clients-errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"> 
            <h4 style="margin: 0;">Ошибка при обновлении данных клиента</h4> 
        </div>';
        header('Location: ../../clients.php');
        exit;
    }
    
} catch (PDOException $e) {
    $_SESSION['clients-errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"> 
        <h4 style="margin: 0;">Ошибка базы данных: ' . $e->getMessage() . '</h4> 
    </div>';
    header('Location: ../../clients.php');
    exit;
}

