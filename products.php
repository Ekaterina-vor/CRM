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
                    <input type="text" id="search" name="search" placeholder="Товар">
                    
                    
                    <select name="search_name" id="sort1">
                        <option value="name">Название</option>
                        <option value="price">Цена</option>
                        <option value="stock">Количество</option>
                    </select>

                    <select name="sort" id="sort">
                    <option value="0">По умолчанию</option>
                        <option value="1">По возрастанию</option>
                        <option value="2">По убыванию</option>
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