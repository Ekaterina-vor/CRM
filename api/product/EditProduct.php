<?php
require_once '../../config/database.php';

$productData = $_POST;
$id = $productData['id'];

try {
    // Подготавливаем данные для обновления
    $name = $productData['name'];
    $description = $productData['description'];
    $price = $productData['price'];
    $stock = $productData['stock'];
    
    // Формируем SQL запрос
    $sql = "UPDATE products SET 
            name = :name,
            description = :description,
            price = :price,
            stock = :stock
            WHERE id = :id";
            
    $stmt = $pdo->prepare($sql);
    
    // Выполняем запрос
    $result = $stmt->execute([
        ':id' => $id,
        ':name' => $name,
        ':description' => $description,
        ':price' => $price,
        ':stock' => $stock
    ]);
    
    if ($result) {
        header('Location: ../../products.php');
        exit;
    } else {
        $_SESSION['products-errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"> 
            <h4 style="margin: 0;">Ошибка при обновлении данных продукта</h4> 
        </div>';
        header('Location: ../../products.php');
        exit;
    }
    
} catch (PDOException $e) {
    $_SESSION['products-errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"> 
        <h4 style="margin: 0;">Ошибка базы данных: ' . $e->getMessage() . '</h4> 
    </div>';
    header('Location: ../../products.php');
    exit;
}
