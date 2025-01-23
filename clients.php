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
    <title>CRM | Главная</title>
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/settings.css"> 
    <link rel="stylesheet" href="styles/pages/clients.css">
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
                <li><a href="">Клиенты</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="">Заказы</a></li>
            </ul>
            <a class="filters-header__logout" href="?do=logout">Выйти</a>
        </div>
    </header>
    <main>
        <section class="filters-filters">
            <div class="filters-container">
                <form action="">
                    <label for="search">Поиск по имени</label>
                    <input type="text" id="search" name="search" placeholder="Александр">
                    <label for="sort">Сортировка</label>
                    <select name="sort" id="sort">
                        <option value="0">По возрастанию</option>
                        <option value="1">По убыванию</option>
                    </select>
                </form>
            </div>
        </section>
        <section class="filters-clients">
            <div class="clients__header">
                    <button onclick="MicroModal.show('add-modal')" class="clients__add-button">
                        <i class="fa fa-plus-square" aria-hidden="true"></i>
                    </button>
                    <h2 class="clients__title">Список клиентов</h2>
                </div>
            <div class="filters-container">
            
                <table>
                    <thead>
                        <tr>
                            <th>ИД</th>
                            <th>ФИО</th>
                            <th>Почта</th>
                            <th>Телефон</th>
                            <th>День рождения</th>
                            <th>Дата создания</th>
                            <th>История заказов</th>
                            <th>Редактировать</th>
                            <th>Удалить</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        require 'api/DB.php';
                        require_once ('api/clients/OutputClients.php');

                        $clients = $DB->query(
                            "SELECT * FROM clients
                            ")->fetchAll();

                        OutputClients($clients);
                        
                    ?>
                        <!-- <tr>
                            <td>0</td>
                            <td>Воронова Екатерина Сегреевна</td>
                            <td>example@mail.ru</td>
                            <td>89765432164</td>
                            <td>19.05.2004</td>
                            <td>15.01.2025</td>
                            <td onclick="MicroModal.show('history-modal')"><i class="fa fa-history" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('edit-modal')"><i class="fa fa-pencil" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('delete-modal')" class="styled-cell"><i class="fa fa-trash" aria-hidden="true"></i></td>
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
                Добавить клиента 
              </h2> 
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
                    <div class="modal__form-group"> 
                        <label for="phone">Телефон</label> 
                        <input type="tel" id="phone" name="phone" required> 
                    </div> 
                    <div class="modal__form-group"> 
                        <label for="birthday">День рождения</label> 
                        <input type="date" id="birthday" name="birthday" required> 
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
                Редактировать клиента 
              </h2> 
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
                    <div class="modal__form-group"> 
                        <label for="phone">Телефон</label> 
                        <input type="tel" id="phone" name="phone" required> 
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
                Удалить клиента?
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

      <div class="modal micromodal-slide" id="history-modal" aria-hidden="true"> 
        <div class="modal__overlay" tabindex="-1" data-micromodal-close> 
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title"> 
            <header class="modal__header"> 
              <h2 class="modal__title" id="modal-1-title"> 
                История заказов
              </h2> 
              <small>Фамилия Имя Отчество</small>
              <button class="modal__close" aria-label="Close modal" data-micromodal-close></button> 
            </header> 
            <main class="modal__content" id="modal-1-content">
                <div class="order">
                    <div class="order__info">
                        <h3 class="order__number">Заказ №1</h3>
                        <time class="order__date">Дата оформления : 2025-01-13 09:25:30</time>
                        <p class="order__total">Общая сумма : 300.00</p>
                    </div>
                    <table class="order__items">
                        <tr>
                            <th>ИД</th>
                            <th>Название товара</th>
                            <th>Количество</th>
                            <th>Цена</th>
                        </tr>
                        <tr>
                            <td>9s13</td>
                            <td>Футболка</td>
                            <td>10</td>
                            <td>10000</td>
                        </tr>
                        <tr>
                            <td>9s13</td>
                            <td>Футболка</td>
                            <td>10</td>
                            <td>10000</td>
                        </tr>
                    </table>
                </div>
                <div class="order">
                    <div class="order__info">
                        <h3 class="order__number">Заказ №2</h3>
                        <time class="order__date">Дата оформления : 2025-01-13 09:25:30</time>
                        <p class="order__total">Общая сумма : 300.00</p>
                    </div>
                    <table class="order__items">
                        <tr>
                            <th>ИД</th>
                            <th>Название товара</th>
                            <th>Количество</th>
                            <th>Цена</th>
                        </tr>
                        <tr>
                            <td>9s44</td>
                            <td>Джинсы</td>
                            <td>5</td>
                            <td>23000</td>
                        </tr>
                        <tr>
                            <td>9s13</td>
                            <td>Легинсы</td>
                            <td>10</td>
                            <td>10000</td>
                        </tr>
                    </table>
                </div>
            </main> 
          </div> 
        </div> 
      </div> 

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script> 
    <script defer src="scripts/initClientsModal.js"></script>
</body>
</html>