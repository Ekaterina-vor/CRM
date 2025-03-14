<?php session_start(); 
 
if (isset($_GET['do']) && $_GET['do'] === 'logout'){ 
    require_once 'api/auth/LogoutUser.php'; 
    require_once 'api/DB.php'; 
 
    LogoutUser('login.php',$DB, $_SESSION['token']);
}  
 
require_once 'api/auth/AuthCheck.php'; 
AuthCheck('', 'login.php'); 
require_once 'api/helpers/InputDefaultValue.php';

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
    <style>
    .pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        margin-bottom: 10px;
    }

    .page-numbers {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin: 10px 0;
    }

    .page-link {
        padding: 5px 10px;
        border: 2px solid #ddd;
        border-radius: 4px;
        color: #666;
        text-decoration: none;
        position: relative;
        top: 30px;
        left: -190px;

    }

    .page-link:hover {
        background-color:rgb(245, 245, 245);
        color: #333;
    }

    .page-link[href='?page=<?php echo $currentPage; ?>'] {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
        
    }

    .page-link.active {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
    }
    </style>
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
                <?php
                    require_once 'api/helpers/getUserType.php';
                    $userType = getUserType($DB);
                    if ($userType === 'tech') {
                        echo '<li><a href="tech.php">Обращение пользователя</a></li>';
                    }
                ?>
            </ul>
            <a class="filters-header__logout" href="?do=logout">Выйти</a>
        </div>
    </header>
    <main>
        <section class="filters-filters">
            <div class="filters-container">
                <form action="" method="GET">
                    <label for="search">Поиск</label>
                    <input <?php InputDefaultValue('search', ''); ?> class="main__input" type="text" id="search" name="search" placeholder="Поиск...">
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
                        <select name="show_inactive" id="show_inactive">
                            <option value="0" <?php echo (!isset($_GET['show_inactive']) || $_GET['show_inactive'] == '0') ? 'selected' : ''; ?>>Все заказы</option>
                            <option value="1" <?php echo (isset($_GET['show_inactive']) && $_GET['show_inactive'] == '1') ? 'selected' : ''; ?>>Активные</option>
                            <option value="2" <?php echo (isset($_GET['show_inactive']) && $_GET['show_inactive'] == '2') ? 'selected' : ''; ?>>Неактивные</option>
                        </select>
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

                <?php 
                $maxClients = 5; // Количество записей на странице

                // Получаем общее количество заказов с учетом фильтров
                $countQuery = "SELECT COUNT(DISTINCT orders.id) as total 
                               FROM orders 
                               JOIN clients ON orders.client_id = clients.id
                               JOIN order_items ON orders.id = order_items.order_id
                               JOIN products ON order_items.product_id = products.id";

                // Добавляем условия WHERE в зависимости от фильтров
                $whereConditions = [];
                if (isset($_GET['search']) && !empty($_GET['search'])) {
                    $search = strtolower($_GET['search']);
                    $whereConditions[] = "(LOWER(clients.name) LIKE '%$search%' OR LOWER(products.name) LIKE '%$search%')";
                }

                if (isset($_GET['show_inactive'])) {
                    switch ($_GET['show_inactive']) {
                        case '1': // Активные
                            $whereConditions[] = "orders.status = '1'";
                            break;
                        case '2': // Неактивные
                            $whereConditions[] = "orders.status = '0'";
                            break;
                    }
                }

                if (!empty($whereConditions)) {
                    $countQuery .= " WHERE " . implode(" AND ", $whereConditions);
                }

                $stmt = $DB->query($countQuery);
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                // Вычисляем максимальное количество страниц
                $maxPage = ceil($total / $maxClients);

                // Получаем текущую страницу
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                // Если текущая страница больше максимальной, перенаправляем на последнюю
                if ($currentPage > $maxPage && $maxPage > 0) {
                    $urlParams = $_GET;
                    $urlParams['page'] = $maxPage;
                    $redirectUrl = '?' . http_build_query($urlParams);
                    header("Location: $redirectUrl");
                    exit;
                }

                // Если страница меньше 1, устанавливаем первую страницу
                if ($currentPage < 1) {
                    $currentPage = 1;
                }

                // Сохраняем параметры URL
                $urlParams = [];
                if (isset($_GET['search'])) $urlParams['search'] = $_GET['search'];
                if (isset($_GET['sort'])) $urlParams['sort'] = $_GET['sort'];
                if (isset($_GET['show_inactive'])) $urlParams['show_inactive'] = $_GET['show_inactive'];

                // Функция для генерации URL с параметрами
                function buildUrl($page, $params) {
                    $url = "?page=" . $page;
                    foreach ($params as $key => $value) {
                        if ($key !== 'page') {
                            $url .= "&" . $key . "=" . urlencode($value);
                        }
                    }
                    return $url;
                }

                ?>
                <div class="pagination">
                    <a href="<?php echo buildUrl(max(1, $currentPage - 1), $urlParams); ?>" 
                       class="nav-btn <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                    </a>
                    <div class="page-info">
                        Страница <?php echo $currentPage; ?> из <?php echo $maxPage; ?>
                    </div>
                    <a href="<?php echo buildUrl($currentPage + 1, $urlParams); ?>" 
                       class="nav-btn <?php echo $currentPage >= $maxPage ? 'disabled' : ''; ?>">
                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <?php 
                // Отображение номеров страниц только если есть записи
                if ($maxPage > 0) {
                    for ($i = 1; $i <= $maxPage; $i++) {
                        $isCurrentPage = ($i == $currentPage) ? 'style="background-color:rgb(154, 197, 165); color: white; border-color:rgb(184, 186, 188);"' : '';
                        echo "<a href='" . buildUrl($i, $urlParams) . "' class='page-link' $isCurrentPage>$i</a>";
                    }
                }
                ?>


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
                            <th>Админ</th>
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
                        <option value="new">Новый пользователь</option>
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
                    <div class="modal__form-group group-email" id="email-field">
                        <label for="email">Почта</label>
                        <input type="email" id="email" name="email" placeholder="Введите почту....">
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
      
      <div class="modal micromodal-slide" id="edit-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Редактировать заказ</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <form action="api/order/EditOrder.php" method="POST" class="modal__form">
                        <input type="hidden" name="id" id="edit-id">
                        <div class="modal__form-group">
                            <label for="status">Статус</label>
                            <select name="status" id="edit-status">
                                <option value="1">Активный</option>
                                <option value="0">Неактивный</option>
                            </select>
                        </div>
                        <div class="modal__form-actions">
                            <button type="submit" class="modal__btn modal__btn-primary">Сохранить</button>
                            <button type="button" class="modal__btn" data-micromodal-close>Отменить</button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script defer src="scripts/initOrdersModal.js"></script>
    <script defer src="scripts/orders.js"></script>
    <script>
    function editOrder(id, status) {
        document.getElementById('edit-id').value = id;
        document.getElementById('edit-status').value = status;
        MicroModal.show('edit-modal');
    }
    </script>
     <script>
    function clearUrlAndClose() {
        let url = new URL(window.location.href);
        url.searchParams.delete('edit-user');
        window.history.pushState({}, '', url);
        MicroModal.close('edit-modal');
    }
    </script>

    <!-- Создание тикета -->
    <button class="support-btn">
        <i class="fa fa-question-circle fa-3x" aria-hidden="true"></i>
    </button>
    <div class="support-create-ticket">
        <form action="api/tickets/CreateTickets.php" method="POST" enctype="multipart/form-data">
            <label for="type">Тип обращения</label>
            <select name="support-type" id="type" class="support-select">
                <option value="tech">Техническая поддержка</option>
                <option value="crm">Проблема с crm</option>
            </select>
            <label for="message">Текст сообщения</label>
            <textarea name="support-message" id="message"></textarea>
            <input type="file" name="files" id="files">
            <button type="submit" class="support-submit">Создать тикет</button>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const supportBtn = document.querySelector('.support-btn');
        const supportForm = document.querySelector('.support-create-ticket');
        
        supportBtn.addEventListener('click', function() {
            supportForm.classList.toggle('active');
        });
    });
    </script>
</body>
</html>
