<?php

session_start();

if (isset($_GET['do']) && $_GET['do'] === 'logout'){ 
    require_once 'api/auth/LogoutUser.php'; 
    require_once 'api/DB.php'; 
 
    LogoutUser('login.php',$DB, $_SESSION['token']);
    exit;
} 

require_once 'api/auth/AuthCheck.php';
AuthCheck('', 'login.php');
require_once 'api/helpers/InputDefaultValue.php';

// require 'vendor/autoload.php';
// use Endroid\QrCode\QrCode;
// use Endroid\QrCode\Writer\PngWriter;
// $qrCode = new QrCode('Hello, World!');
// $writer = new PngWriter();
// $result = $writer->write($qrCode);
// header('Content-Type: '.$result->getMimeType());
// echo $result->getString();




?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>CRM | Товары</title>
        <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="styles/settings.css"> 
        <link rel="stylesheet" href="styles/pages/products.css">
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
            </ul>
            <a class="filters-header__logout" href="">Выйти</a>
        </div>
    </header>
    <main>
        <section class="filters-filters">
            <div class="filters-container">
                <form action="">
                    <label for="search">Поиск по названию</label>
                    <input <?php InputDefaultValue('search', ''); ?> class="main__input" type="text" id="search" name="search" placeholder="Что-то...">
                    
                    
                    <select name="search_name" id="sort1">
                        <option value="name" <?php echo ($_GET['search_name'] ?? '') === 'name' ? 'selected' : ''; ?>>Название</option>
                        <option value="price" <?php echo ($_GET['search_name'] ?? '') === 'price' ? 'selected' : ''; ?>>Цена</option>
                        <option value="stock" <?php echo ($_GET['search_name'] ?? '') === 'stock' ? 'selected' : ''; ?>>Количество</option>
                    </select>

                    <select name="sort" id="sort">
                        <option value="0" <?php echo ($_GET['sort'] ?? '') === '0' ? 'selected' : ''; ?>>По умолчанию</option>
                        <option value="1" <?php echo ($_GET['sort'] ?? '') === '1' ? 'selected' : ''; ?>>По возрастанию</option>
                        <option value="2" <?php echo ($_GET['sort'] ?? '') === '2' ? 'selected' : ''; ?>>По убыванию</option>
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
                <?php 
                $maxClients = 5;

                // Получаем общее количество клиентов
                $countQuery = "SELECT COUNT(*) as total FROM clients";
                $stmt = $DB->query($countQuery);
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

                // Вычисляем максимальное количество страниц
                $maxPage = ceil($total / $maxClients);

                // Получаем текущую страницу
                $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

                // Если страница выходит за пределы, делаем редирект
                if ($currentPage < 1 || $currentPage > $maxPage) {
                    $correctPage = max(1, min($currentPage, $maxPage));
                    $queryParams = $_GET;
                    $queryParams['page'] = $correctPage;
                    $queryString = http_build_query($queryParams);
                    header("Location: ?" . $queryString);
                    exit();
                }

                        
                    ?>
                    <div class="pagination">
                    <a href="?page=<?php echo max(1, $currentPage - 1); ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" 
                       class="nav-btn <?php echo $currentPage <= 1 ? 'disabled' : ''; ?>">
                        <i class="fa fa-arrow-left" aria-hidden="true"></i>
                    </a>
                    <div class="page-info">
                        Страница <?php echo $currentPage; ?> из <?php echo $maxPage; ?>
                    </div>
                    <a href="?page=<?php echo min($maxPage, $currentPage + 1); ?><?php echo isset($_GET['search']) ? '&search=' . $_GET['search'] : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" 
                       class="nav-btn <?php echo $currentPage >= $maxPage ? 'disabled' : ''; ?>">
                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
                <?php 
                    for ($i = 1; $i <= $maxPage; $i++) {
                        $isCurrentPage = ($i == $currentPage) ? 'style="background-color:rgb(154, 197, 165); color: white; border-color:rgb(184, 186, 188);"' : '';
                        echo "<a href='?page=$i' class='page-link' $isCurrentPage>$i</a>";
                    }
                    ?>
                
                <h2 class="clients__title">Список товаров</h2>
            </div>
            <div class="filters-container">
                <table>
                    <thead>
                        <tr>
                            <th>ИД</th>
                            <th>Название</th>
                            <th>Описание</th>
                            <th>Цена</th>
                            <th>Количество</th>
                            <th>Редактировать</th>
                            <th>Удалить</th>
                            <th>QR-код</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require 'api/DB.php'; 
                        require_once('api/product/ProductSearch.php'); 
                        require_once('api/product/OutputProduct.php');

                        $products = ProductSearch($_GET, $DB);

                        OutputProduct($products);
                        



                        ?>



                        <!-- <tr>
                            <td>1</td>
                            <td>Футболка белая</td>
                            <td>Хлопковая футболка базовая</td>
                            <td>1500</td>
                            <td>50</td>
                            <td onclick="MicroModal.show('edit-modal')"><i class="fa fa-pencil" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('delete-modal')"><i class="fa fa-trash" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('qr-modal')"><i class="fa fa-qrcode" aria-hidden="true"></i></td>
                        </tr> -->
                        
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <div class="modal micromodal-slide" id="add-modal" aria-hidden="true"> 
        <div class="modal__overlay" tabindex="-1" data-micromodal-close> 
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title"> 
                <header class="modal__header"> 
                    <h2 class="modal__title" id="modal-1-title"> 
                        Добавить товар 
                    </h2> 
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button> 
                </header> 
                <main class="modal__content" id="modal-1-content"> 
                    <form method='POST' action="api/product/AddProduct.php" class="modal__form"> 
                        <div class="modal__form-group"> 
                            <label for="name">Название</label> 
                            <input type="text" id="name" name="name" > 
                        </div> 
                        <div class="modal__form-group"> 
                            <label for="description">Описание</label> 
                            <textarea id="description" name="description" ></textarea> 
                        </div> 
                        <div class="modal__form-group"> 
                            <label for="price">Цена</label> 
                            <input type="number" id="price" name="price" > 
                        </div> 
                        <div class="modal__form-group"> 
                            <label for="quantity">Количество</label> 
                            <input type="number" id="quantity" name="stock" > 
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


    <div class="modal micromodal-slide" id="edit-modal" aria-hidden="true"> 
        <div class="modal__overlay" tabindex="-1" data-micromodal-close> 
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title"> 
                <header class="modal__header"> 
                    <h2 class="modal__title" id="modal-1-title"> 
                        Редактировать товар 
                    </h2> 
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button> 
                </header> 
                <main class="modal__content" id="modal-1-content"> 
                    <form class="modal__form"> 
                        <div class="modal__form-group"> 
                            <label for="name">Название</label> 
                            <input type="text" id="name" name="name" required> 
                        </div> 
                        <div class="modal__form-group"> 
                            <label for="description">Описание</label> 
                            <textarea id="description" name="description" required></textarea> 
                        </div> 
                        <div class="modal__form-group"> 
                            <label for="price">Цена</label> 
                            <input type="number" id="price" name="price" required> 
                        </div> 
                        <div class="modal__form-group"> 
                            <label for="quantity">Количество</label> 
                            <input type="number" id="quantity" name="quantity" required> 
                        </div> 
                        <div class="modal__form-actions"> 
                            <button type="submit" class="modal__btn modal__btn-primary">Редактировать</button> 
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
                    <h2 class="modal__title" id="modal-1-title"> 
                        Удалить товар?
                    </h2> 
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button> 
                </header> 
                <main class="modal__content" id="modal-1-content">
                    <button class="styled-button">Удалить</button>
                    <button class="styled-button" onclick="MicroModal.close('delete-modal')">Отменить</button>
                </main> 
            </div> 
        </div> 
    </div> 

    <div class="modal micromodal-slide 
    <?php 
        if(isset($_SESSION['product-errors']) && !empty($_SESSION['product-errors'])) {
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
                if(isset($_SESSION['product-errors'])) {
                    echo $_SESSION['product-errors'];
                    unset($_SESSION['product-errors']);
                }
            ?>
            </main> 
          </div> 
        </div> 
      </div>
      


    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script> 
    <script defer src="scripts/initClientsModal.js"></script>
</body>
</html>