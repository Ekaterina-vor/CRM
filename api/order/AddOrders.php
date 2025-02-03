<?php session_start(); 
 
if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
    $formData = $_POST; 
    $fields = ['clients', 'products'];
    $errors =[];

    $_SESSION['orders-errors'] ='';
    // 1. Проверить пришли ли данные  
    foreach ($fields as $key => $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])){
            $errors[$field][] = 'field is required';
        }
    } 
    
    if (!empty($errors)){
        $errorHtml = '<ul>';
        foreach($errors as $field => $fieldErrors) {
            foreach($fieldErrors as $error) {
                $errorHtml .= "<li>* {$field} : {$error}</li>";
            }
        }
        $errorHtml .= '</ul>';
        
        $_SESSION['orders-errors'] = $errorHtml;
        header('Location: ../../orders.php');
        exit;
    }


     
    }


   
   
  
 
?>