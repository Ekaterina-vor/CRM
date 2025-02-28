<?php 
 
function ProductSearch($params, $DB){ 
   // Получаем параметры поиска 
   $search = isset($params['search']) ? strtolower($params['search']) : ''; 

   $search_name = isset($params['search_name']) ? $params['search_name'] : 'name';

   $sort = isset($params['sort']) ? $params['sort'] : '0'; 

   // Пагинация
   $currentPage = isset($params['page']) ? (int)$params['page'] : 1;
   $itemsPerPage = 5;
   $offset = ($currentPage - 1) * $itemsPerPage;

   // Формируем SQL запрос
   $sql = "SELECT * FROM products";
   $params_array = array();

   // Добавляем условие WHERE если есть поисковый запрос
   if (!empty($search)) {
       $sql .= " WHERE ";
       if (in_array($search_name, ['name', 'price', 'stock'])) {
           $sql .= "$search_name LIKE :search";
           $params_array[':search'] = "%$search%";
       }
   }

   // Добавляем сортировку
   if (in_array($sort, ['1', '2']) && in_array($search_name, ['name', 'price', 'stock'])) {
       $sql .= " ORDER BY $search_name " . ($sort === '1' ? 'ASC' : 'DESC');
   }

   // Добавляем пагинацию
   $sql .= " LIMIT :limit OFFSET :offset";
   $params_array[':limit'] = $itemsPerPage;
   $params_array[':offset'] = $offset;

   // Подготавливаем и выполняем запрос
   $stmt = $DB->prepare($sql);

   foreach($params_array as $key => &$val) {
       $stmt->bindValue($key, $val, 
           ($key === ':limit' || $key === ':offset') ? PDO::PARAM_INT : PDO::PARAM_STR
       );
   }

   $stmt->execute();
   return $stmt->fetchAll(); 
} 
 
?>