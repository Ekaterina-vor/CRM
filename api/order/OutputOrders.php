<?php
function OutputOrders($clients){

    

    // Перебираем каждого клиента из массива
    foreach($clients as $client){
        // Создаем строку с информацией о продуктах
        $products_info = '';
        $names = explode(', ', $client['product_names']);
        $quantities = explode(', ', $client['product_quantities']);
        $prices = explode(', ', $client['product_prices']);
        
        for($i = 0; $i < count($names); $i++) {
            $products_info .= $names[$i] . ' (' . $quantities[$i] . ' шт. x ' . $prices[$i] . ' руб.), ';
        }
        $products_info = rtrim($products_info, ', ');
        
        $statusClass = $client['status'] == '1' ? 'active' : 'inactive';
        $statusText = $client['status'] == '1' ? 'Активный' : 'Неактивный';

        echo "<tr class='order-{$statusClass}'>
                <td>{$client['order_id']}</td>
                <td>{$client['name']}</td>
                <td>{$client['order_date']}</td>
                <td>{$client['total']}</td>
                <td>{$products_info}</td>
                <td>{$client['admin_name']}</td>
                <td><span class='status-badge status-{$statusClass}'>{$statusText}</span></td>
                <td>
                    <a href='api/order/generateCheack.php?id={$client['order_id']}'>
                        <i class='fa fa-file-text-o' aria-hidden='true'></i>
                    </a>
                </td>
                <td onclick=\"MicroModal.show('edit-modal')\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></td>
                <td>
                <a href='api/order/DeleteOrder.php?id={$client['order_id']}'>
                <i class='fa fa-trash' aria-hidden='true'></i>
                </a>
                </td>
            </tr>";
    }
}

?>