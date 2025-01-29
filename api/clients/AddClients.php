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
        $errorHtml = '<ul>';
        foreach($errors as $field => $fieldErrors) {
            foreach($fieldErrors as $error) {
                $errorHtml .= "<li>* {$field} : {$error}</li>";
            }
        }
        $errorHtml .= '</ul>';
        
        $_SESSION['clients-errors'] = $errorHtml;
        header('Location: ../../clients.php');
        exit;
    }

    // 2. Функция для очистки данных 
    function cleanData($fields) { 
        $fields = trim($fields); 
        $fields = stripslashes($fields); 
        $fields = strip_tags($fields); 
        $fields = htmlspecialchars($fields); 
        return $fields; 
    } 
 
    // Очистка всех полей формы 
    foreach ($formData as $key => $value) { 
        $formData[$key] = cleanData($value); 
        echo json_encode($formData);
    }

     // 3. Проверить есть ли такой клиент 
    $phone = $formData['phone']; 
    require_once '../DB.php'; 
     
    $existingClient = $DB->query( 
        "SELECT id FROM clients WHERE phone = '$phone'" 
    )->fetchAll(); 
 
    if (!empty($existingClient)) { 
        $_SESSION['clients-errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"> 
            <h4 style="margin: 0;">Клиент с таким номером телефона уже существует</h4> 
        </div>'; 
        header('Location: ../../clients.php'); 
        exit(); 
    }


   
   
    // 4. Записать клиента }  
    $userId = $existingClient[0]['id'] ?? null; 
     
    // 1. Если userID не пустой, записываем ошибку 
    if ($userId) { 
        $_SESSION['clients_errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"> 
            <h4 style="margin: 0;">Клиент уже существует в базе данных</h4> 
        </div>'; 
        header('Location: ../../clients.php'); 
        exit(); 
    } 
 
    // 2. Записать $formData в бд (без id и created_at) 
    $sql = "INSERT INTO clients (name, email, phone, birthday)  
            VALUES (:name, :email, :phone, :birthday)"; 
     
    $stmt = $DB->prepare($sql); 
    $stmt->execute([ 
        ':name' => $formData['fullname'], 
        ':email' => $formData['email'], 
        ':phone' => $formData['phone'], 
        ':birthday' => $formData['birthday'] 
    ]); 
 
    header('Location: ../../clients.php'); 
    exit();
   
} 
 
?>