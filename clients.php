<?php session_start();


if (isset($_GET['do']) && $_GET['do'] === 'logout'){
    require_once 'api/auth/LogoutUser.php';
    require_once 'api/DB.php';

    LogoutUser('login.php',$DB, $_SESSION['token']);
} 

require_once 'api/auth/AuthCheck.php';
require_once 'api/helpers/InputDefaultValue.php';
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
                <li><a href="orders.php">Заказы</a></li>
            </ul>
            <a class="filters-header__logout" href="?do=logout">Выйти</a>
        </div>
    </header>
    <main>
        <section class="filters-filters">
            <div class="filters-container">
                <form action="" method="GET" class="main_form" >
                    
                    <label for="sort">Сортировка</label>
                    <select class="main__select" name="search_name" id="search_name">
                        <option value="name" <?php echo ($_GET['search_name'] ?? '') === 'name' ? 'selected' : ''; ?>>Поиск по имени</option>
                        <option value="email" <?php echo ($_GET['search_name'] ?? '') === 'email' ? 'selected' : ''; ?>>Поиск по почте</option>
                    </select>
                    <label for="search">Поиск по имени</label>
                    <input <?php InputDefaultValue('search', ''); ?> class="main__input" type="text" id="search" name="search" placeholder="Александр">
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
                
                
                
                <h2 class="clients__title">Список клиентов</h2>
            </div>
                    <?php 
                    for ($i = 1; $i <= $maxPage; $i++) {
                        $isCurrentPage = ($i == $currentPage) ? 'style="background-color:rgb(154, 197, 165); color: white; border-color:rgb(184, 186, 188);"' : '';
                        echo "<a href='?page=$i' class='page-link' $isCurrentPage>$i</a>";
                    }
                    ?>
    
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
                        require_once 'api/clients/OutputClients.php'; 
                        require_once 'api/clients/ClientsSearch.php'; 
                        $Clients = ClientsSearch($_GET, $DB); 
                        OutputClients($Clients); 
                        ?>
                       
                        
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
                <form action="api/clients/AddClients.php" method="POST" class="modal__form"> 
                    <div class="modal__form-group"> 
                        <label for="fullname">ФИО</label> 
                        <input type="text" id="fullname" name="fullname" > 
                    </div> 
                    <div class="modal__form-group"> 
                        <label for="email">Почта</label> 
                        <input type="email" id="email" name="email" > 
                    </div> 
                    <div class="modal__form-group"> 
                        <label for="phone">Телефон</label> 
                        <input type="tel" id="phone" name="phone" > 
                    </div> 
                    <div class="modal__form-group"> 
                        <label for="birthday">День рождения</label> 
                        <input type="date" id="birthday" name="birthday" > 
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




      <?php
        // В начале файла, где остальные session_start
        if(isset($_GET['edit-user'])) {
            if(!isset($_SESSION['show_modal'])) {
                $_SESSION['show_modal'] = true;
            }
            // Получаем данные пользователя
            $userId = $_GET['edit-user'];
            $stmt = $DB->prepare("SELECT * FROM clients WHERE id = ?");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);
            // Отладочный вывод
            error_log('User Data: ' . print_r($userData, true));
            // Временный вывод данных на страницу
            echo '<!-- Debug: ';
            print_r($userData);
            echo ' -->';
        }
      ?>
      
      

      <div class="modal micromodal-slide 
    <?php 
        if(isset($_GET['edit-user']) && !empty($_GET['edit-user']) && isset($_SESSION['show_modal']) && $_SESSION['show_modal']) {
            echo 'open';
            unset($_SESSION['show_modal']); // Сбрасываем флаг после открытия
        }
    ?>
    " id="edit-modal" aria-hidden="true"> 
        <div class="modal__overlay" tabindex="-1" data-micromodal-close> 
          <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title"> 
            <header class="modal__header"> 
              <h2 class="modal__title" id="modal-1-title"> 
                Редактировать клиента 
              </h2> 
              <button class="modal__close" aria-label="Close modal" onclick="clearUrlAndClose()" data-micromodal-close></button> 
            </header> 
            <main class="modal__content" id="modal-1-content"> 
                
                <form action="api/clients/EditClient.php?id=<?php echo $_GET['edit-user']; ?>" method="POST" class="modal__form"> 
                    <div class="modal__form-group"> 
                        <label for="fullname">ФИО</label> 
                        <input type="text" id="fullname" name="fullname" required value="<?php echo htmlspecialchars($userData['name'] ?? ''); ?>"> 
                    </div> 
                    <div class="modal__form-group"> 
                        <label for="email">Почта</label> 
                        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($userData['email'] ?? ''); ?>"> 
                    </div> 
                    <div class="modal__form-group"> 
                        <label for="phone">Телефон</label> 
                        <input type="tel" id="phone" name="phone" required value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>"> 
                    </div> 
                    <div class="modal__form-actions"> 
                        <button type="submit" class="modal__btn modal__btn-primary">Редактировать</button> 
                        <button type="button" class="modal__btn modal__btn-secondary" onclick="clearUrlAndClose()" data-micromodal-close>Отменить</button> 
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

   

      <div class="modal micromodal-slide 
    <?php 
        if(isset($_SESSION['clients-errors']) && !empty($_SESSION['clients-errors'])) {
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
                if(isset($_SESSION['clients-errors'])) {
                    echo $_SESSION['clients-errors'];
                    unset($_SESSION['clients-errors']);
                }
            ?>
            </main> 
          </div> 
        </div> 
      </div>
<!-- почта -->
      <div class="modal micromodal-slide 
    <?php 
        if(isset($_GET['send-email']) && !empty($_GET['send-email'])) {
            echo 'open';
        }
    ?>
    " id="send-email-modal" aria-hidden="true"> 
        <div class="modal__overlay" tabindex="-1" data-micromodal-close> 
            <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title"> 
                <header class="modal__header"> 
                    <h2 class="modal__title" id="modal-1-title"> 
                        Отправка письма
                    </h2> 
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button> 
                </header> 
                <main class="modal__content" id="modal-1-content"> 
                    <?php 
                        if(isset($_GET['send-email']) && !empty($_GET['send-email'])) {
                            echo $_GET['send-email'];
                        }
                    ?>
                    
                    <form action="api/clients/SendEmail.php?email=<?php echo $_GET['send-email']; ?>" method="POST">
                        <div class="form-group">
                            <label for="header">Обращение:</label>
                            <input type="text" id="header" name="header" >
                        </div>
                        
                        <div class="form-group">
                            <label for="main">Основной контент:</label>
                            <textarea id="main" name="main" ></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="footer">Футер:</label>
                            <input type="text" id="footer" name="footer" >
                        </div>
                        
                        <button type="submit">Отправить</button>
                    </form>
                </main> 
            </div> 
        </div> 
    </div>


    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script> 
    <script defer src="scripts/initClientsModal.js"></script>
    <script>
    function clearUrlAndClose() {
        // Получаем текущий URL
        let url = new URL(window.location.href);
        // Удаляем параметр edit-user
        url.searchParams.delete('edit-user');
        // Обновляем URL без перезагрузки страницы
        window.history.pushState({}, '', url);
        // Закрываем модальное окно
        MicroModal.close('edit-modal');
    }
    </script>
</body>
</html>