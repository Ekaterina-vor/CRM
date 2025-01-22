<?php session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    require_once '../DB.php';
    
    $login = isset($_POST['username']) ? $_POST['username'] : ''; 
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    //переменная для ошибок
 
    $_SESSION['login-errors'] = [];

    //проверка на логин
    if(!$login){
        $_SESSION['login-errors']['login'] =
        'Field is required';
    
    header('Location: ../../login.php');
    exit;
    }


    //проверка на пароль
    if(!$password){
        $_SESSION['password-errors']['password'] =
        'Field is required';
    
    header('Location: ../../login.php');
    exit;
    }


    // функция для фильтрации данных
    function clearData($input){
        $cleaned = strip_tags($input);
        $cleaned = trim($cleaned);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        return $cleaned;
    }
    
    $login = clearData($login);
   
    $password = clearData($password);

    //проверка логина
//сделать запрос в бд по логину($login)
//если пустой - записываем ошибку + редирект на логин
// Запрос в БД для проверки существования пользователя

    $userID = $DB->query(
        "SELECT id  FROM users WHERE login='$login'
        ")->fetchAll();
    
    if(empty($userID)){
        $_SESSION['login-errors']['login'] = 'User not found';
        header('Location: ../../login.php');
        exit;
    }
    //проверка пороля
    $userID = $DB->query(
        "SELECT id  FROM users WHERE login='$login' AND password='$password'
        ")->fetchAll();
    
    if(empty($userID)){
        $_SESSION['password-errors']['password'] = 'Wrong password';
        header('Location: ../../login.php');
        exit;
    }

    //генерация токена
    $uniqueString = time(); 
    $token = base64_encode("login = $login & password = $password & unique=$uniqueString"); 
     
    //Записать в сессию в поле token 
    $_SESSION['token'] = $token; 
     
    //Записать в БД в поле token 
    try { 
        $updateToken = $DB->prepare("UPDATE users SET token = ? WHERE login = ? AND password = ?"); 
        $updateToken->execute([$token, $login, $password]); 
         
        // Если успешно, делаем редирект на страницу клиентов 
        header('Location: ../../clients.php'); 
        exit; 
    } catch(PDOException $e) { 
        $_SESSION['login-errors']['token'] = 'Ошибка сохранения сессии'; 
        header('Location: ../../login.php'); 
        exit; 
    }
  


}else {
    echo json_encode([
        "error" => 'Неверный запрос',
    ]);
}

//проверка логина
//сделать запрос в бд по логину($login)
//если пустой - записываем ошибку + редирект на логин
// Запрос в БД для проверки существования пользователя



?>