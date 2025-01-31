<?php
function OutputOrders($clients){
    // Перебираем каждого клиента из массива
    foreach($clients as $client){
        echo "<tr>
                <td>{$client['id']}</td>
                <td>{$client['client_id']}</td>
                <td>{$client['order_date']}</td>
                <td>{$client['total']}</td>
                <td>{$client['status']}</td>
                <td>{$client['created_at']}</td>
                <td onclick=\"MicroModal.show('history-modal')\"><i class=\"fa fa-history\" aria-hidden=\"true\"></i></td>
                <td onclick=\"MicroModal.show('edit-modal')\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></td>
                <td>
                <a href='api/clients/DeleteClient.php?id={$client['id']}'>
                <i class='fa fa-trash' aria-hidden='true'></i>
                </a>
                </td>
            </tr>";
    }
}

?>