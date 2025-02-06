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
                <form action="" method="GET">
                    <label for="search">Поиск</label>
                    <input type="text" id="search" name="search" placeholder="Поиск..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <select name="search_name" id="filter">
                        <option value="clients.name" <?php echo (isset($_GET['search_name']) && $_GET['search_name'] == 'clients.name') ? 'selected' : ''; ?>>Клиент</option>
                        <option value="orders.id" <?php echo (isset($_GET['search_name']) && $_GET['search_name'] == 'orders.id') ? 'selected' : ''; ?>>ИД</option>
                        <option value="orders.order_date" <?php echo (isset($_GET['search_name']) && $_GET['search_name'] == 'orders.order_date') ? 'selected' : ''; ?>>Дата</option>
                        <option value="orders.total" <?php echo (isset($_GET['search_name']) && $_GET['search_name'] == 'orders.total') ? 'selected' : ''; ?>>Сумма</option>
                        <option value="orders.status" <?php echo (isset($_GET['search_name']) && $_GET['search_name'] == 'orders.status') ? 'selected' : ''; ?>>Статус</option>
                    </select>
                    <select name="sort" id="sort">
                        <option value="0" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == '0') ? 'selected' : ''; ?>>По умолчанию</option>
                        <option value="1" <?php echo (isset($_GET['sort']) && $_GET['sort'] == '1') ? 'selected' : ''; ?>>По возрастанию</option>
                        <option value="2" <?php echo (isset($_GET['sort']) && $_GET['sort'] == '2') ? 'selected' : ''; ?>>По убыванию</option>
                    </select>
                    <div class="show-inactive">
                        <input type="checkbox" id="show_inactive" name="show_inactive" value="1" <?php echo isset($_GET['show_inactive']) && $_GET['show_inactive'] == '1' ? 'checked' : ''; ?>>
                        <label for="show_inactive">Показывать неактивные заказы</label>
                    </div>
                    <button type="submit" name="filter" value="1">Поиск</button>
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
                            <th>Сумма</th>
                            <th>Элементы заказа</th>
                            <th>Статус</th>
                            <th>Чек</th>
                            <th>Редактировать</th>
                            <th>Удалить</th>
                            
                            
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        require 'api/DB.php'; 
                        require_once 'api/order/OutputOrders.php';
                        require_once 'api/order/OrdersSearch.php';

                        $orders = OrdersSearch($_GET, $DB); 
                        OutputOrders($orders);
                        
                       

                        // foreach($orders as $order) {
                        //     echo "<tr>";
                        //     echo "<td>{$order['order_id']}</td>";
                        //     echo "<td>{$order['name']}</td>";
                        //     echo "<td>{$order['order_date']}</td>";
                        //     echo "<td>{$order['total']} ₽</td>";
                        //     echo "<td>{$order['product_names_with_prices_and_quantities']}</td>";
                        //     echo "<td>
                        //             <button onclick=\"MicroModal.show('edit-modal'); setEditData({$order['order_id']})\" class=\"table__button table__button--edit\">
                        //                 <i class=\"fa fa-pencil\" aria-hidden=\"true\"></i>
                        //             </button>
                        //         </td>";
                        //     echo "<td>
                        //             <button onclick=\"MicroModal.show('delete-modal'); setDeleteId({$order['order_id']})\" class=\"table__button table__button--delete\">
                        //                 <i class=\"fa fa-trash\" aria-hidden=\"true\"></i>
                        //             </button>
                        //         </td>";
                        //     echo "<td>
                        //             <button onclick=\"window.location.href='check.php?id={$order['order_id']}'\" class=\"table__button table__button--check\">
                        //                 <i class=\"fa fa-file-text-o\" aria-hidden=\"true\"></i>
                        //             </button>
                        //         </td>";
                        //     echo "<td>
                        //             <button onclick=\"MicroModal.show('details-modal'); showDetails({$order['order_id']})\" class=\"table__button table__button--details\">
                        //                 <i class=\"fa fa-info-circle\" aria-hidden=\"true\"></i>
                        //             </button>
                        //         </td>";
                        //     echo "</tr>";
                        // }
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
            <form method="post" action="api/order/AddOrders.php" class="modal__form"> 
                    <div class="modal__form-group"> 
                        <label for="client">Клиент</label> 
                        <select name="client" id="client" class="main__select">
                            <?php   
                                $users = $DB->query("SELECT id, name FROM clients")->fetchAll();
                                foreach($users as $key => $user) {
                                    $id = $user['id'];
                                    $name = $user['name'];
                                    echo "<option value='$id'>$name</option>";
                                }
                            ?>
                        </select>
                    </div> 
                    <div class="modal__form-group"> 
                        <label for="products">Товары</label> 
                        <select name="products[]" id="products" class="main__select" multiple>
                            <?php   
                                $products = $DB->query("SELECT id, name, price, stock FROM products WHERE stock > 0")->fetchAll();
                                foreach($products as $key => $product) {
                                    $id = $product['id'];
                                    $name = $product['name'];
                                    $price = $product['price'];
                                    $stock = $product['stock'];
                                    echo "<option value='$id'>$name - $price ₽ - $stock шт.</option>";
                                }
                            ?>
                            
                        </select> 
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

    <div class="modal micromodal-slide 
    <?php 
        if(isset($_SESSION['orders-errors']) && !empty($_SESSION['orders-errors'])) {
            echo 'open';
        }
    ?>
    " id="error-modal" aria-hidden="true"> 
        <div class="modal__overlay" tabindex="-1" data-micromodal-close> 
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title"> 
            <header class="modal__header"> 
              <h2 class="modal__title" id="modal-1-title"> 
                Ощибка! 
              </h2> 
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button> 
            </header> 
            <main class="modal__content" id="modal-1-content"> 
            <?php 
                if(isset($_SESSION['orders-errors'])) {
                    echo $_SESSION['orders-errors'];
                    unset($_SESSION['orders-errors']);
                }
            ?>
            </main> 
          </div> 
        </div> 
      </div>                           

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script defer src="scripts/initOrdersModal.js"></script>
</body>
</html>
