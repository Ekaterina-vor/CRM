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

        echo "<tr>
                <td>{$client['order_id']}</td>
                <td>{$client['name']}</td>
                <td>{$client['order_date']}</td>
                <td>{$client['total']}</td>
                <td>{$products_info}</td>
                <td onclick=\"MicroModal.show('history-modal')\"><i class=\"fa fa-history\" aria-hidden=\"true\"></i></td>
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