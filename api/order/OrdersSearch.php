<?php 
 
function ClientsSearch($params, $DB){ 
   //Получить данные из базы данных 
   $search = isset($params['search']) ? $params['search'] : ''; 
   $sort = isset($params['sort']) ? $params['sort'] : '0'; 
 
   //Добавить сортировку (order by) 
   // 0 - ордер не добавляется 
   // 1 - ордер по возрастанию 
   // 2 - ордер по убыванию 
   $order = ""; 
   if($sort == 1){ 
      $order = " ORDER BY name ASC"; 
   }elseif($sort == 2){ 
      $order = " ORDER BY name DESC"; 
   } 
 
   $search = trim(strtolower($search)); 
 
   $clients = $DB->query( 
      "SELECT * FROM clients WHERE LOWER(name) LIKE '%$search%'" . $order 
   )->fetchAll(); 
    
   //Вывести данные в таблицу 
   return $clients; 
} 
 
function OrdersSearch($params, $DB) { 
    $search = isset($params['search']) ? trim($params['search']) : ''; 
    $sort = isset($params['sort']) ? $params['sort'] : '0'; 
    $search_name = isset($params['search_name']) ? $params['search_name'] : 'clients.name'; 
    $show_inactive = isset($params['show_inactive']) && $params['show_inactive'] == '1';
    $order = ''; 
    
    // Пагинация
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    $per_page = 5;
    $offset = ($page - 1) * $per_page;

    $query = " 
        SELECT 
            orders.id as order_id, 
            clients.name, 
            orders.order_date, 
            orders.status, 
            SUM(products.price * order_items.quantity) as total, 
            GROUP_CONCAT(products.name SEPARATOR ', ') AS product_names, 
            GROUP_CONCAT(order_items.quantity SEPARATOR ', ') AS product_quantities, 
            GROUP_CONCAT(products.price SEPARATOR ', ') AS product_prices,
            users.name AS admin_name 
        FROM 
            orders 
        JOIN 
            clients ON orders.client_id = clients.id 
        JOIN
            users ON orders.admin = users.id
        JOIN 
            order_items ON orders.id = order_items.order_id 
        JOIN 
            products ON order_items.product_id = products.id"; 
 
    // Формируем условия WHERE
    $whereConditions = array();
    
    if (!empty($search)) {
        $whereConditions[] = "(LOWER($search_name) LIKE :search)";
        $params_array[':search'] = "%$search%";
    }

    // Изменяем обработку статуса заказов
    if (isset($params['show_inactive'])) {
        switch ($params['show_inactive']) {
            case '1': // Активные
                $whereConditions[] = "orders.status = '1'";
                break;
            case '2': // Неактивные
                $whereConditions[] = "orders.status = '0'";
                break;
            // case '0' или отсутствие параметра - показываем все заказы
        }
    }

    if (!empty($whereConditions)) {
        $query .= " WHERE " . implode(" AND ", $whereConditions);
    }
 
    $query .= " GROUP BY orders.id, clients.name, orders.order_date, orders.status"; 
 
    // Добавляем HAVING для поиска по цене после GROUP BY 
    if (!empty($search) && $search_name === 'orders.total') { 
        $query .= " HAVING total = '" . $search . "'"; 
    } 
 
    // Добавляем сортировку
    if ($sort != '0') {
        $orderDirection = ($sort == '1') ? 'ASC' : 'DESC';
        switch ($search_name) {
            case 'clients.name':
            case 'orders.id':
            case 'orders.order_date':
            case 'orders.total':
            case 'orders.status':
                $query .= " ORDER BY $search_name $orderDirection";
                break;
            default:
                $query .= " ORDER BY orders.id $orderDirection";
        }
    }

    // Добавляем пагинацию
    $query .= " LIMIT :limit OFFSET :offset";
    $params_array[':limit'] = $per_page;
    $params_array[':offset'] = $offset;

    // Подготавливаем и выполняем запрос
    $query = $DB->prepare($query);

    foreach($params_array as $key => &$val) {
        $query->bindValue($key, $val, 
            ($key === ':limit' || $key === ':offset') ? PDO::PARAM_INT : PDO::PARAM_STR
        );
    }

    $query->execute();
    return $query->fetchAll();
} 
 
?>