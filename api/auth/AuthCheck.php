<?php




function AuthCheck($redirect_success = '', $redirect_error = 'login.php', $DB = null) {
    require_once 'api/DB.php';
    require_once 'LogoutUser.php';

    if (!isset($_SESSION['token'])) {  
        if ($redirect_error){
        header("Location: " . $redirect_error);
     } 
        return;  
    }


    //токен текущего пользователя
    $token = $_SESSION['token'];
    $adminID = $DB->query(
        "SELECT id FROM users WHERE token = '$token'
        ")->fetchAll();



     
    //Если adminId пустой - редирект на $redirect_error 
    if (empty($adminID) && $redirect_error) { 
        LogoutUser($redirect_error, $DB);
        header("Location: " . $redirect_error); 
       
    } 
    //Если adminId не пустой - редирект на $redirect_success 
    if (!empty($adminID) && $redirect_success) { 
        header("Location: " . $redirect_success); 
       
    }  
 
}


?>