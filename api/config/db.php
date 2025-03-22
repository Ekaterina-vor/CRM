<?php
try {
    $DB = new PDO('mysql:host=localhost;dbname=crm;charset=utf8mb4', 'root', '');
    $DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $DB->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}
?> 