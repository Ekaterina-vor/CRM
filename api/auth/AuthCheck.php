<?php




function AuthCheck($successPath = '', $errorPath = '') {
    require_once 'api/DB.php';

    if (!isset($_SESSION['token']) && $errorPath) {  
        header("Location: " . $errorPath);  
        return;  
    }


    //токен текущего пользователя
    $token = $_SESSION['token'];
    $adminID = $DB->query(
        "SELECT id FROM users WHERE token = '$token'
        ")->fetchAll();



     
    //Если adminId пустой - редирект на $errorPath 
    if (empty($adminID) && $errorPath) { 
        header("Location: " . $errorPath); 
       
    } 
    //Если adminId не пустой - редирект на $successPath 
    if (!empty($adminID) && $successPath) { 
        header("Location: " . $successPath); 
       
    }  
 
}


?>