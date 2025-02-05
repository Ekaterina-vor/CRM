<?php session_start(); 
 
if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
    $formData = $_POST; 
    $fields = ['client', 'products'];
    $errors =[];

    $_SESSION['orders-errors'] ='';
    // 1. Проверить пришли ли данные  
    foreach ($fields as $key => $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])){
            $errors[$field][] = 'field is required';
        }
    } 
    
    if (!empty($errors)){
        $errorHtml = '<ul>';
        foreach($errors as $field => $fieldErrors) {
            foreach($fieldErrors as $error) {
                $errorHtml .= "<li>* {$field} : {$error}</li>";
            }
        }
        $errorHtml .= '</ul>';
        
        $_SESSION['orders-errors'] = $errorHtml;
        header('Location: ../../orders.php');
        exit;
    }

    require_once '../DB.php';
    //ид товаров
    $productsIds = $formData['products'];

    //получаем все товары из базы данных
    $allProducts = $DB->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

    //сумма выбранных товаров
    $total = 0;
    foreach($allProducts as $product) {
        if(in_array($product['id'], $productsIds)) {
            $total += $product['price'];
        }
    }


    //создание заказа с полями
    $orders = [
        'id' => time(),
        'client_id' => $formData['client'],
        'total' => $total
    ];


    // Добавляем заказ в таблицу orders
    $sql = "INSERT INTO orders (id, client_id, total) VALUES (:id, :client_id, :total)";
    $stmt = $DB->prepare($sql);
    $stmt->execute($orders);

    // Получаем ID созданного заказа
    $orderId = $DB->lastInsertId();

    // Подготавливаем запрос для добавления товаров в order_items
    $stmt = $DB->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

    // Добавляем каждый товар в order_items
    foreach($productsIds as $productId) {
        foreach($allProducts as $product) {
            if($product['id'] == $productId) {
                $stmt->execute([
                    $orderId,       // order_id
                    $productId,     // product_id
                    1,             // quantity (пока ставим 1)
                    $product['price'] // price
                ]);
                break;
            }
        }
    }

    // Редирект на страницу заказов после успешного создания
    header('Location: ../../orders.php');
    exit; 
     
    //удаление заказа(меняем статус на 0)
    // 0 - неактивный заказ(архив)
    // 1 - активный заказ
    

    
    
    
    
    
    


}


   
   
  
 
?>