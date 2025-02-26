<?php
function OutputClients($clients) {
    // Получаем текущую страницу
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $clientsPerPage = 5;
    
    // Вычисляем начальный индекс для текущей страницы
    $start = ($currentPage - 1) * $clientsPerPage;
    
    // Получаем только нужный срез массива клиентов
    $pagedClients = array_slice($clients, $start, $clientsPerPage);
    
    foreach ($pagedClients as $client) {
        echo "<tr>
                <td>{$client['id']}</td>
                <td>{$client['name']}</td>
                <td>{$client['email']}</td>
                <td>{$client['phone']}</td>
                <td>{$client['birthday']}</td>
                <td>{$client['created_at']}</td>
                <td>
                    <form class='date-form' action='api/clients/ClientHistory.php' method='GET'>
                        <input type='hidden' name='id' value='{$client['id']}'>
                        <input type='date' name='from' class='date-input' required>
                        <input type='date' name='to' class='date-input' required>
                        <button type='submit' class='btn'>Сформировать</button>
                    </form>
                </td>
                <td>
                    <button onclick=\"editClient({$client['id']})\" class='btn-edit'>
                        <i class='fa fa-pencil'></i>
                    </button>
                </td>
                <td>
                    <button onclick=\"deleteClient({$client['id']})\" class='btn-delete'>
                        <i class='fa fa-trash'></i>
                    </button>
                </td>
            </tr>";
    }
}

?>