<?php




function AuthCheck($successPath = '', $errorPath = '') {
    require_once 'api/DB.php';
    require_once 'LogoutUser.php';

    if (!isset($_SESSION['token'])) {  
        if ($errorPath){
        header("Location: " . $errorPath);
     } 
        return;  
    }


    //токен текущего пользователя
    $token = $_SESSION['token'];
    $adminID = $DB->query(
        "SELECT id FROM users WHERE token = '$token'
        ")->fetchAll();



     
    //Если adminId пустой - редирект на $errorPath 
    if (empty($adminID) && $errorPath) { 
        LogoutUser($errorPath, $DB);
        header("Location: " . $errorPath); 
       
    } 
    //Если adminId не пустой - редирект на $successPath 
    if (!empty($adminID) && $successPath) { 
        header("Location: " . $successPath); 
       
    }  
 
}


?>