<?php 



function LogoutUser($redirect, $DB, $token = ''){
    


    unset($_SESSION['token']);

    if ($token){
        // Очищаем токен в БД для пользователя
        $DB->query("
            UPDATE users SET token = NULL
            WHERE token = '$token'  
            ")->fetchAll();
        

         }

        header("Location: $redirect");
   
}


?>