<?php 
 
function ProductSearch($params, $DB){ 
   //Получить данные из базы данных 
   $search = isset($params['search']) ? $params['search'] : ''; 

   $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';

   $sort = isset($params['sort']) ? $params['sort'] : '0'; 

 
   //Добавить сортировку (order by) 
   // 0 - ордер не добавляется 
   // 1 - ордер по возрастанию 
   // 2 - ордер по убыванию 
   $order = ""; 
   if($sort == 1){ 
      $order = " ORDER BY $search_name ASC"; 
   }elseif($sort == 2){ 
      $order = " ORDER BY $search_name DESC"; 
   } 
 
   $search = strtolower($search); 
 
   $products = $DB->query( 
      "SELECT * FROM products WHERE LOWER(name) LIKE '%$search%'" . $order 
   )->fetchAll(); 
    
   //Вывести данные в таблицу 
   return $products; 
} 
 
?>