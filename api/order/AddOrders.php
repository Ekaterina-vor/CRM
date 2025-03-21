<?php session_start(); 
 require_once '../DB.php';
if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
    $formData = $_POST; 
    $fields = ['client', 'products'];
    $errors =[];

     if ($formData['client'] === 'new') {
        $fields[] = 'email';
    }
    $_SESSION['orders-errors'] ='';
    // 1. Проверить пришли ли данные  
    foreach ($fields as $key => $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])){
            $errors[$field][] = 'field is required';
        }
    } 

    $clientID = $formData['client'] === 'new' ? time() : $formData['client'];

    if ($formData['client'] === 'new') {
        $stmt = $DB->prepare("INSERT INTO clients (id, name, email, phone, birthday) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $clientID,
            $formData['name'] ?? 'не указано',
            $formData['email'],
            'не указано', // значение по умолчанию для phone
            'не указано'  // значение по умолчанию для birthday
        ]);
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

    //код акции (code_promo)
    $promo = $_POST['promo'];
    $promoInfo = []; // информация о акции

    // 1. получить информацию о акции и записать в promoInfo (по code_promo)
    // 2. проверить активна ли акция (uses < max_uses, cancel_at < текущей даты)

    $promoInfo = $DB->query(   
        "SELECT * FROM promotions WHERE code_promo = '$promo'" 
        )->fetchALL();

    if (empty($promoInfo)) {
        $_SESSION['orders_error'] = 'Промокод не существует';
        header('Location: ../../orders.php');
        exit;
    }

    if ($promoInfo[0]['uses'] >= $promoInfo[0]['max_uses']) {
        $_SESSION['orders_error'] = 'Акция закончена';
        header('Location: ../../orders.php');
        exit;
    }
    
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

    // Применяем скидку, если промокод действителен
    if (!empty($promoInfo)) {
        $discount = $promoInfo[0]['discount'];
        $total = $total - ($total * ($discount / 100));
        
        // Увеличиваем количество использований промокода
        $promoId = $promoInfo[0]['id'];
        $DB->query("UPDATE promotions SET uses = uses + 1 WHERE id = $promoId");
    }

    $token = $_SESSION['token'];
    $adminID = $DB->query(
        "SELECT id FROM users WHERE token = '$token'"
        )->fetchAll(PDO::FETCH_ASSOC)[0]['id'];


    //создание заказа с полями
    $orders = [
        'id' => time(),
        'client_id' => $clientID,
        'total' => $total,
        'admin' => $adminID,
    ];


    // Добавляем заказ в таблицу orders
    $sql = "INSERT INTO orders (id, client_id, total, admin) VALUES (:id, :client_id, :total, :admin)";
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