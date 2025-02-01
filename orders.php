<?php session_start(); 
 
if (isset($_GET['do']) && $_GET['do'] === 'logout'){ 
    require_once 'api/auth/LogoutUser.php'; 
    require_once 'api/DB.php'; 
 
    LogoutUser('login.php',$DB, $_SESSION['token']);
}  
 
require_once 'api/auth/AuthCheck.php'; 
AuthCheck('', 'login.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM | Заказы</title>
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/orders.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
</head>
<body>
    <header class="filters-header">
        <div class="filters-container">
            <p> 
                <?php 
                 require 'api/DB.php'; 
                 require_once 'api/clients/AdminName.php';  
                  
                 echo AdminName($_SESSION['token'], $DB); 
                ?> 
            </p>
            <ul>
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
            </ul>
            <a class="filters-header__logout" href="?do=logout">Выйти</a>
        </div>
    </header>
    <main>
        <section class="filters-filters">
            <div class="filters-container">
                <form action="">
                    <label for="search">Поиск</label>
                    <input type="text" id="search" name="search" placeholder="Поиск...">
                    <select name="filter" id="filter">
                        <option value="client">Клиент</option>
                        <option value="id">ИД</option>
                        <option value="date">Дата</option>
                        <option value="sum">Сумма</option>
                    </select>
                    <select name="sort" id="sort">
                        <option value="0">По возрастанию</option>
                        <option value="1">По убыванию</option>
                    </select>
                    <button type="submit" >Поиск</button>
                    <a href="?" class="main__button main__button--reset">Сбросить</a>
                </form>
            </div>
        </section>
        <section class="filters-clients">
            <div class="clients__header">
                <button onclick="MicroModal.show('add-modal')" class="clients__add-button">
                    <i class="fa fa-plus-square" aria-hidden="true"></i>
                </button>
                <h2 class="clients__title">Список заказов</h2>
            </div>
            <div class="filters-container">
                <table>
                    <thead>
                        <tr>
                            <th>ИД</th>
                            <th>ФИО клиента</th>
                            <th>Дата заказа</th>
                            <th>Цена</th>
                            <th>Элементы заказа</th>
                            <th>Редактировать</th>
                            <th>Удалить</th>
                            <th>Чек</th>
                            <th>Подробнее</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        require 'api/DB.php'; 
                        
                        $orders = $DB->query(
                            "SELECT 
                                orders.id AS order_id, 
                                clients.name AS name, 
                                orders.order_date, 
                                orders.total,
                                GROUP_CONCAT(CONCAT(products.name, ' (', products.price, ') x ', order_items.quantity) SEPARATOR ', ') AS product_names_with_prices_and_quantities
                            FROM 
                                orders
                            JOIN 
                                clients ON orders.client_id = clients.id
                            JOIN 
                                order_items ON orders.id = order_items.order_id
                            JOIN 
                                products ON order_items.product_id = products.id
                            GROUP BY 
                                orders.id, 
                                clients.name, 
                                orders.order_date, 
                                orders.total;"                                                  
                        )->fetchAll();

                        foreach($orders as $order) {
                            echo "<tr>";
                            echo "<td>{$order['order_id']}</td>";
                            echo "<td>{$order['name']}</td>";
                            echo "<td>{$order['order_date']}</td>";
                            echo "<td>{$order['total']} ₽</td>";
                            echo "<td>{$order['product_names_with_prices_and_quantities']}</td>";
                            echo "<td>
                                    <button onclick=\"MicroModal.show('edit-modal'); setEditData({$order['order_id']})\" class=\"table__button table__button--edit\">
                                        <i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>
                                    </button>
                                </td>";
                            echo "<td>
                                    <button onclick=\"MicroModal.show('delete-modal'); setDeleteId({$order['order_id']})\" class=\"table__button table__button--delete\">
                                        <i class=\"fa fa-trash\" aria-hidden=\"true\"></i>
                                    </button>
                                </td>";
                            echo "<td>
                                    <button onclick=\"window.location.href='check.php?id={$order['order_id']}'\" class=\"table__button table__button--check\">
                                        <i class=\"fa fa-file-text-o\" aria-hidden=\"true\"></i>
                                    </button>
                                </td>";
                            echo "<td>
                                    <button onclick=\"MicroModal.show('details-modal'); showDetails({$order['order_id']})\" class=\"table__button table__button--details\">
                                        <i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i>
                                    </button>
                                </td>";
                            echo "</tr>";
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Модальные окна -->
    <div class="modal micromodal-slide" id="add-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">Добавить заказ</h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
            <form class="modal__form"> 
                    <div class="modal__form-group"> 
                        <label for="fullname">ФИО</label> 
                        <input type="text" id="fullname" name="fullname" required> 
                    </div> 
                    <div class="modal__form-group"> 
                        <label for="email">Почта</label> 
                        <input type="email" id="email" name="email" required> 
                    </div> 
                    
                    <div class="modal__form-actions"> 
                        <button type="submit" class="modal__btn modal__btn-primary">Создать</button> 
                        <button type="button" class="modal__btn modal__btn-secondary" data-micromodal-close>Отменить</button> 
                    </div> 
                </form> 
            </main>
          </div>
        </div>
    </div>

    <div class="modal micromodal-slide" id="delete-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
            <header class="modal__header">
              <h2 class="modal__title" id="modal-1-title">Вы уверены, что хотите удалить заказ?</h2>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
            </header>
            <main class="modal__content" id="modal-1-content">
                <button>Удалить</button>
                <button onclick="MicroModal.close('delete-modal')">Отменить</button>
            </main>
          </div>
        </div>
    </div>

    <div class="modal micromodal-slide" id="details-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-1-title">Подробная информация</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="modal-1-content">
                    <!-- Подробная информация о заказе -->
                </main>
            </div>
        </div>
    </div>

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script defer src="scripts/initOrdersModal.js"></script>
</body>
</html>
