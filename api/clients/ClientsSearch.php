<?php 
 
function ClientsSearch($params, $DB){ 
   $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';
   //Получить данные из базы данных 
   $search = isset($params['search']) ? $params['search'] : ''; 
   $sort = isset($params['sort']) ? $params['sort'] : '0'; 
 
   //Добавить сортировку (order by) 
   // 0 - ордер не добавляется 
   // 1 - ордер по возрастанию 
   // 2 - ордер по убыванию 
   $orderBy = ""; 
   if($sort == 1){ 
      $orderBy = " ORDER BY $search_name ASC";
   }elseif($sort == 2){ 
      $orderBy = " ORDER BY $search_name DESC";
   } 
 
   $search = strtolower($search); 
 
   // Выбираем поле для поиска в зависимости от search_name
   $search_field = $search_name === 'email' ? 'email' : 'name';
   
   $clients = $DB->query( 
      "SELECT * FROM clients WHERE LOWER($search_field) LIKE '%$search%'" . $orderBy 
   )->fetchAll(); 
    
   //Вывести данные в таблицу 
   return $clients; 
} 
 
?>