<?php session_start();

require_once 'api/DB.php';

if (isset($_GET['do']) && $_GET['do'] === 'logout'){
    require_once 'api/auth/LogoutUser.php';
    LogoutUser('login.php',$DB, $_SESSION['token']);
} 

require_once 'api/auth/AuthCheck.php';
require_once 'api/helpers/InputDefaultValue.php';
AuthCheck('', 'login.php', $DB);
require_once 'api/helpers/getUserType.php';
$userType = getUserType($DB);
if ($userType !== 'tech') {
    header('Location: clients.php');
}



?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Главная</title>
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/settings.css"> 
    <link rel="stylesheet" href="styles/pages/clients.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
    
    
</head>
<body>
    <header class="filters-header">
        <div class="filters-container">
            <p>
                <?php
                 require 'api/DB.php';
                 require_once 'api/clients/AdminName.php'; 

                 echo AdminName($_SESSION['token'], $DB);
                ?>
            </p>
            <ul>
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
                <?php
                    
                    if ($userType === 'tech') {
                        echo '<li><a href="tech.php">Обращение пользователя</a></li>';
                    }
                ?>
            </ul>
            <a class="filters-header__logout" href="?do=logout">Выйти</a>
        </div>
    </header>
    

    



      
      
     
</body>
</html>