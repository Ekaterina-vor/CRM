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
                <td>
                    <form class=\"date-form\" 
                          action=\"api/clients/ClientHistory.php\" 
                          method=\"GET\">
                        <input type=\"hidden\" 
                               name=\"id\" 
                               value=\"{$client['id']}\">
                        <input type=\"date\" 
                               id=\"from-{$client['id']}\" 
                               name=\"from\" 
                               class=\"date-input\">
                        <input type=\"date\" 
                               id=\"to-{$client['id']}\" 
                               name=\"to\" 
                               class=\"date-input\">
                        <button type=\"submit\" class=\"btn\">
                            Сформировать
                        </button>
                    </form>
                </td>
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