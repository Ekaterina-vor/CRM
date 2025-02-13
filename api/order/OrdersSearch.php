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
 
    // Базовое условие WHERE
    $whereConditions = [];
    
    // По умолчанию показываем только активные заказы
    if (!$show_inactive) {
        $whereConditions[] = "orders.status = '1'";
    }
 
    // Добавляем условия поиска
    if (!empty($search)) { 
        switch ($search_name) { 
            case 'clients.name': 
                $whereConditions[] = "LOWER(clients.name) LIKE LOWER('%" . $search . "%')"; 
                break; 
            case 'orders.id': 
                $whereConditions[] = "orders.id = '" . $search . "'"; 
                break; 
            case 'orders.order_date': 
                $whereConditions[] = "DATE(orders.order_date) = '" . $search . "'"; 
                break; 
            case 'orders.status': 
                $whereConditions[] = "orders.status = '" . $search . "'"; 
                break; 
        } 
    } 
 
    // Объединяем все условия WHERE
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
                $query .= " ORDER BY clients.name " . $orderDirection; 
                break; 
            case 'orders.id': 
                $query .= " ORDER BY orders.id " . $orderDirection; 
                break; 
            case 'orders.order_date': 
                $query .= " ORDER BY orders.order_date " . $orderDirection; 
                break; 
            case 'orders.total': 
                $query .= " ORDER BY total " . $orderDirection; 
                break; 
            case 'orders.status': 
                $query .= " ORDER BY orders.status " . $orderDirection; 
                break; 
            default: 
                $query .= " ORDER BY orders.id " . $orderDirection; 
        } 
    } 
 
    return $DB->query($query)->fetchAll(); 
} 
 
?>