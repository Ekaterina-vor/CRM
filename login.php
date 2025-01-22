<?php

session_start();

require_once 'api/auth/AuthCheck.php';
AuthCheck('clients.php');
//сделать: вывод ошибки для пароля и покрасить ошибку в красный цвет и сделать поменьше

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Авторизация</title>
    <link rel="stylesheet" href="styles/settings.css"> 
    <link rel="stylesheet" href="styles/pages/login.css">
</head>
<body>
    <div class="login-container">
        <!-- Форма с полями логин, пароль и кнопкой войти -->
        <form action="api/auth/AuthUser.php" method="POST" class="login-form" >
            <label for="username">Логин:</label>
            <input type="text" id="username" name="username" >
            <p class="error" style = "Color:red;">
                <?php
                if (isset($_SESSION['login-errors'])){
                    $errors = $_SESSION['login-errors'];
              
                    echo isset($errors['login']) ? $errors['login'] : '';
                }
                ?>
            </p>
            
            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" >
            <p class="error" style = "Color:red;">
                <?php
                if (isset($_SESSION['password-errors'])){
                    $errors = $_SESSION['password-errors'];
              
                    echo isset($errors['password']) ? $errors['password'] : '';
                }
                ?>
            </p>
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>