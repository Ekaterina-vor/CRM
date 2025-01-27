<?php session_start(); 
 
if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
    $formData = $_POST; 
    $fields = ['fullname', 'email', 'phone', 'birthday'];
    $errors =[];

    $_SESSION['clients-errors'] ='';
    // 1. Проверить пришли ли данные  
    foreach ($fields as $key => $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])){
            $errors[$field][] = 'field is required';
        }
    } 
    
    if (!empty($errors)){
        $_SESSION['clients-errors'] = json_encode($errors);
        header('Location: ../../clients.php');
        exit;
    }

    // 2. Фильтрация данных 
    // 3. Проверить есть ли такой клиент 
    // 4. Записать клиента }  
   
} 
 
?>