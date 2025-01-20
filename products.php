<?php

session_start();

require_once 'modules/AuthCheck.php';
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
            <p>Фамилия имя отчество</p>
            <ul>
                <li><a href="">Клиенты</a></li>
                <li><a href="">Товары</a></li>
                <li><a href="">Заказы</a></li>
            </ul>
            <a class="filters-header__logout" href="">Выйти</a>
        </div>
    </header>
    <main>
        <section class="filters-filters">
            <div class="filters-container">
                <form action="">
                    <label for="search">Поиск по названию</label>
                    <input type="text" id="search" name="search" placeholder="Футболка">
                    
                    <label for="filter">Фильтр</label>
                    <select name="filter" id="filter">
                        <option value="name">Название</option>
                        <option value="price">Цена</option>
                        <option value="quantity">Количество</option>
                    </select>

                    <label for="sort">Сортировка</label>
                    <select name="sort" id="sort">
                        <option value="asc">По возрастанию</option>
                        <option value="desc">По убыванию</option>
                    </select>
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
                        <tr>
                            <td>1</td>
                            <td>Футболка белая</td>
                            <td>Хлопковая футболка базовая</td>
                            <td>1500</td>
                            <td>50</td>
                            <td onclick="MicroModal.show('edit-modal')"><i class="fa fa-pencil" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('delete-modal')"><i class="fa fa-trash" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('qr-modal')"><i class="fa fa-qrcode" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Джинсы классические</td>
                            <td>Прямые джинсы из денима</td>
                            <td>3500</td>
                            <td>30</td>
                            <td onclick="MicroModal.show('edit-modal')"><i class="fa fa-pencil" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('delete-modal')"><i class="fa fa-trash" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('qr-modal')"><i class="fa fa-qrcode" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Куртка зимняя</td>
                            <td>Тёплая куртка с капюшоном</td>
                            <td>7000</td>
                            <td>15</td>
                            <td onclick="MicroModal.show('edit-modal')"><i class="fa fa-pencil" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('delete-modal')"><i class="fa fa-trash" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('qr-modal')"><i class="fa fa-qrcode" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Кроссовки спортивные</td>
                            <td>Легкие беговые кроссовки</td>
                            <td>4500</td>
                            <td>25</td>
                            <td onclick="MicroModal.show('edit-modal')"><i class="fa fa-pencil" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('delete-modal')"><i class="fa fa-trash" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('qr-modal')"><i class="fa fa-qrcode" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>Шапка вязаная</td>
                            <td>Тёплая шапка из шерсти</td>
                            <td>1200</td>
                            <td>40</td>
                            <td onclick="MicroModal.show('edit-modal')"><i class="fa fa-pencil" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('delete-modal')"><i class="fa fa-trash" aria-hidden="true"></i></td>
                            <td onclick="MicroModal.show('qr-modal')"><i class="fa fa-qrcode" aria-hidden="true"></i></td>
                        </tr>
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

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script> 
    <script defer src="scripts/initClientsModal.js"></script>
</body>
</html>