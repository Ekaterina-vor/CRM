<?php
function OutputClients($clients){
    // Перебираем каждого клиента из массива
    foreach($clients as $client){
        echo "<tr>
                <td>{$client['id']}</td>
                <td>{$client['name']}</td>
                <td>{$client['email']}</td>
                <td>{$client['phone']}</td>
                <td>{$client['birthday']}</td>
                <td>{$client['created_at']}</td>
                <td onclick=\"MicroModal.show('history-modal')\"><i class=\"fa fa-history\" aria-hidden=\"true\"></i></td>
                <td onclick=\"MicroModal.show('edit-modal')\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></td>
                <td onclick=\"MicroModal.show('delete-modal')\"><i class=\"fa fa-trash\" aria-hidden=\"true\"></i></td>
            </tr>";
    }
}

?>