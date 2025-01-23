<?php 



function AdminName($token, $DB){
    //сделать запрос по токену, вернуть имя и фамилию через return
    $admin = $DB->query("
        SELECT name, surname FROM users WHERE token = '$token'
    
    ")->fetchAll()[0];
    $name = $admin['name'];
    $surname = $admin['surname'];

    return "$name $surname";

}


?>