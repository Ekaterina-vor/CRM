<?php

session_start();

require_once 'modules/AuthCheck.php';
AuthCheck('clients.php', 'login.php');

?>