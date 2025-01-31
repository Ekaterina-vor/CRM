<?php session_start(); 
 
if($_SERVER['REQUEST_METHOD'] == 'POST'){ 
    $formData = $_POST; 
    $fields = ['name', 'description', 'price', 'stock'];
    $errors =[];

    $_SESSION['product-errors'] ='';
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
        
        $_SESSION['product-errors'] = $errorHtml;
        header('Location: ../../products.php');
        exit;
    }

    // 2. Функция для очистки данных 
    function cleanData($fields) { 
        $fields = trim($fields); 
        $fields = stripslashes($fields); 
        $fields = strip_tags($fields); 
        $fields = htmlspecialchars($fields); 
        return $fields; 
    } 
 
    // Очистка всех полей формы 
    foreach ($formData as $key => $value) { 
        $formData[$key] = cleanData($value); 
        echo json_encode($formData);
    }

// 3. Проверить есть ли такой товар 
$productName = $formData['name']; 
require_once '../DB.php'; 
 
$existingProduct = $DB->query( 
    "SELECT id FROM products WHERE name = '$productName'" 
)->fetchAll(); 

if (!empty($existingProduct)) { 
    $_SESSION['product-errors'] = '<div style="color: #842029; background-color: #f8d7da; border: 1px solid #f5c2c7; border-radius: 5px; padding: 15px; margin: 10px 0;"> 
        <h4 style="margin: 0;">Товар с таким названием уже существует</h4> 
    </div>'; 
    header('Location: ../../products.php'); 
    exit(); 
}

// 4. Записать товар в базу данных
$sql = "INSERT INTO products (name, description, price, stock)  
        VALUES (:name, :description, :price, :stock)"; 
 
$stmt = $DB->prepare($sql); 
$stmt->execute([ 
    ':name' => $formData['name'], 
    ':description' => $formData['description'], 
    ':price' => $formData['price'], 
    ':stock' => $formData['stock'] 
]); 

header('Location: ../../products.php'); 
exit();

} 

?>