<?php
function OutputProduct($clients){
    // Перебираем каждого клиента из массива
    foreach($clients as $client){
        echo "<tr>
                <td>{$client['id']}</td>
                <td>{$client['name']}</td>
                <td>{$client['description']}</td>
                <td>{$client['price']}</td>
                <td>{$client['stock']}</td>
                  
                <td onclick=\"MicroModal.show('edit-modal')\"><i class=\"fa fa-pencil\" aria-hidden=\"true\"></i></td>
              
                <td>
                <a href='api/product/DeleteProduct.php?id={$client['id']}'>
                <i class='fa fa-trash' aria-hidden='true'></i>
                </a>
                </td>
                <td>
                    <a href='api/product/generateQR.php?id={$client['id']}'>
                        <i class='fa fa-qrcode' aria-hidden='true'></i>
                    </a>
                </td>

            </tr>";
    }
}

?>