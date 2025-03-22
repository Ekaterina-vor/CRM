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
    <link rel="stylesheet" href="styles/pages/orders.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            MicroModal.init({
                openTrigger: 'data-micromodal-trigger',
                closeTrigger: 'data-micromodal-close',
                disableFocus: true,
                disableScroll: true,
                awaitOpenAnimation: false,
                awaitCloseAnimation: false
            });
        });
    </script>
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
                <li><a href="promotions.php">Акции</a></li>
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
                
                <form action="api/clients/EditClients.php" method="POST" class="modal__form"> 
                    <input type="hidden" name="id" value="<?php echo $_GET['edit-user']; ?>">
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
<!-- Создание тикета -->
    <button class="support-btn">
        <i class="fa fa-question-circle fa-3x" aria-hidden="true"></i>
    </button>
    <div class="support-create-tickets">
        <div class="support-buttons">
            <button type="button" class="view-tickets-btn" data-micromodal-trigger="my-tickets-modal">
                <i class="fa fa-list" aria-hidden="true"></i> Мои обращения
            </button>
            <button type="button" class="close-btn" onclick="closeTicketForm()">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <form action="api/tickets/CreateTickets.php" method="POST" enctype="multipart/form-data">
            <label for="type">Тип обращения</label>
            <select name="support-type" id="type" class="support-select">
                <option value="tech">Техническая поддержка</option>
                <option value="crm">Проблема с crm</option>
            </select>
            <label for="message">Текст сообщения</label>
            <textarea name="support-message" id="message"></textarea>
            <input type="file" name="files" id="files">
            <div class="support-tickets-buttons">
                <button type="submit" class="support-submit">Создать тикет</button>
                <button type="button" class="cancel-button" onclick="closeTicketForm()">Отмена</button>
            </div>
        </form>
    </div>

    <!-- Модальное окно для списка обращений -->
    <div class="modal micromodal-slide" id="my-tickets-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Мои обращения</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <div class="tickets-list">
                        <?php
                        // Получаем тикеты текущего пользователя
                        $stmt = $DB->prepare("SELECT t.*, u.name as admin_name 
                                            FROM tickets t 
                                            LEFT JOIN users u ON t.admin = u.id 
                                            WHERE t.clients = (SELECT id FROM users WHERE token = ?)
                                            ORDER BY t.created_at DESC");
                        $stmt->execute([$_SESSION['token']]);
                        $tickets = $stmt->fetchAll();

                        foreach ($tickets as $ticket) {
                            $statusClass = $ticket['status'] === 'waiting' ? 'status-waiting' : 
                                         ($ticket['status'] === 'in_progress' ? 'status-progress' : 'status-done');
                            $statusText = $ticket['status'] === 'waiting' ? 'Ожидает' : 
                                        ($ticket['status'] === 'in_progress' ? 'В работе' : 'Завершено');
                            ?>
                            <div class="ticket-card">
                                <div class="ticket-header">
                                    <span class="ticket-type"><?php echo $ticket['type'] === 'tech' ? 'Техническая поддержка' : 'Проблема с CRM'; ?></span>
                                    <span class="ticket-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </div>
                                <div class="ticket-body">
                                    <p class="ticket-message"><?php echo htmlspecialchars($ticket['message']); ?></p>
                                    <div class="ticket-info">
                                        <span class="ticket-date"><?php echo date('d.m.Y H:i', strtotime($ticket['created_at'])); ?></span>
                                        <?php if ($ticket['admin_name']): ?>
                                            <span class="ticket-admin">Техподдержка: <?php echo htmlspecialchars($ticket['admin_name']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($ticket['file_path']): ?>
                                        <div class="ticket-attachment">
                                            <i class="fa fa-paperclip"></i>
                                            <a href="<?php echo htmlspecialchars($ticket['file_path']); ?>" target="_blank">Прикрепленный файл</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="ticket-actions">
                                    <button type="button" class="chat-btn" onclick="openChat(<?php echo $ticket['id']; ?>)">
                                        <i class="fa fa-comments"></i> Открыть чат
                                    </button>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Модальное окно для чата -->
    <div class="modal micromodal-slide" id="chat-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Чат с технической поддержкой</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <div class="chat-container">
                        <div class="chat-messages" id="chat-messages">
                            <!-- Сообщения будут добавляться здесь -->
                        </div>
                        <form id="chat-form" class="chat-form">
                            <input type="hidden" id="ticket-id" name="ticket_id" value="">
                            <textarea name="message" id="chat-message" placeholder="Введите сообщение..."></textarea>
                            <button type="submit">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <style>
    .support-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: #b2d581;
        border: none;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
        transition: all 0.3s ease;
    }

    .support-btn:hover {
        background-color: #7ab030;
        transform: scale(1.1);
    }

    .support-btn i {
        color: white;
    }

    .support-create-tickets {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 300px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        padding: 20px;
        z-index: 999;
        display: none;
    }

    .support-create-tickets.active {
        display: block;
    }

    .support-create-tickets form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .support-create-tickets label {
        font-weight: bold;
        margin-bottom: 5px;
    }

    .support-create-tickets select,
    .support-create-tickets textarea {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .support-create-tickets textarea {
        min-height: 100px;
        resize: vertical;
    }

    .support-create-tickets input[type="file"] {
        margin: 10px 0;
    }

    .support-tickets-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
    }

    .support-create-tickets button {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .support-create-tickets button[type="submit"] {
        background-color: #b2d581;
        color: white;
    }

    .support-create-tickets button[type="submit"]:hover {
        background-color: #7ab030;
    }

    .support-create-tickets .cancel-button {
        background-color: #f2f2f2;
        color: #333;
    }

    .support-create-tickets .cancel-button:hover {
        background-color: #e0e0e0;
    }

    @media (max-width: 576px) {
        .support-btn {
            width: 50px;
            height: 50px;
            bottom: 15px;
            right: 15px;
        }
        
        .support-create-tickets {
            width: 90%;
            right: 5%;
            left: 5%;
        }
    }

    .support-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .view-tickets-btn {
        background: #b2d581;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .view-tickets-btn:hover {
        background: #7ab030;
    }

    .close-btn {
        background: none;
        border: none;
        color: #666;
        cursor: pointer;
        font-size: 20px;
        padding: 5px;
        transition: color 0.3s ease;
    }

    .close-btn:hover {
        color: #333;
    }

    .support-select,
    .support-create-tickets textarea {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .support-create-tickets textarea {
        min-height: 100px;
        resize: vertical;
    }

    .support-submit {
        background: #b2d581;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s ease;
    }

    .support-submit:hover {
        background: #7ab030;
    }

    .tickets-list {
        max-height: 70vh;
        overflow-y: auto;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .ticket-card {
        background: white;
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: box-shadow 0.3s ease;
    }

    .ticket-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .ticket-type {
        font-weight: 500;
        color: #333;
        font-size: 1.1em;
    }

    .ticket-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: 500;
    }

    .status-waiting { 
        background: #fff3cd; 
        color: #856404; 
    }
    
    .status-progress { 
        background: #cce5ff; 
        color: #004085; 
    }
    
    .status-done { 
        background: #d4edda; 
        color: #155724; 
    }

    .ticket-body {
        padding: 10px 0;
    }

    .ticket-message {
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .ticket-info {
        display: flex;
        justify-content: space-between;
        font-size: 0.9em;
        color: #666;
        margin-top: 10px;
    }

    .ticket-attachment {
        margin-top: 10px;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .ticket-attachment a {
        color: #007bff;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .ticket-actions {
        margin-top: 15px;
        display: flex;
        justify-content: flex-end;
    }

    .chat-btn {
        background: #b2d581;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .chat-btn:hover {
        background: #7ab030;
        transform: translateY(-1px);
    }

    .chat-btn i {
        font-size: 16px;
    }

    .chat-container {
        display: flex;
        flex-direction: column;
        height: 60vh;
    }

    .chat-messages {
        flex-grow: 1;
        overflow-y: auto;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 15px;
    }

    .chat-message {
        margin-bottom: 10px;
        display: flex;
    }

    .chat-message.user {
        justify-content: flex-end;
    }

    .message-content {
        max-width: 70%;
        padding: 10px;
        border-radius: 8px;
        background: white;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .chat-message.user .message-content {
        background: #b2d581;
        color: white;
    }

    .chat-message.admin .message-content {
        background: #e9ecef;
    }

    .message-time {
        font-size: 0.8em;
        color: #666;
        margin-top: 5px;
        display: block;
    }

    .chat-form {
        display: flex;
        gap: 10px;
        padding: 15px;
        background: white;
        border-top: 1px solid #eee;
    }

    .chat-form textarea {
        flex-grow: 1;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        resize: none;
        height: 60px;
    }

    .chat-form button {
        background: #b2d581;
        color: white;
        border: none;
        padding: 0 20px;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .chat-form button:hover {
        background: #7ab030;
    }

    @media (max-width: 768px) {
        .modal__container {
            width: 95%;
            margin: 10px;
            padding: 15px;
        }

        .modal__title {
            font-size: 1.2em;
        }

        .ticket-header {
            flex-direction: column;
            gap: 10px;
        }

        .ticket-info {
            flex-direction: column;
            gap: 5px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const supportBtn = document.querySelector('.support-btn');
        const supportForm = document.querySelector('.support-create-tickets');
        
        supportBtn.addEventListener('click', function() {
            supportForm.classList.toggle('active');
        });

        // Инициализация MicroModal
        MicroModal.init({
            openTrigger: 'data-micromodal-trigger',
            closeTrigger: 'data-micromodal-close',
            disableScroll: true,
            awaitOpenAnimation: false,
            awaitCloseAnimation: false
        });

        // Функция для закрытия формы тикета
        window.closeTicketForm = function() {
            supportForm.classList.remove('active');
        };

        document.querySelector('.cancel-button').addEventListener('click', function() {
            supportForm.classList.remove('active');
        });

        // Функция для открытия чата
        window.openChat = function(ticketId) {
            document.getElementById('ticket-id').value = ticketId;
            loadChatMessages(ticketId);
            MicroModal.show('chat-modal');
        };

        // Загрузка сообщений чата
        function loadChatMessages(ticketId) {
            fetch(`api/tickets/GetMessages.php?ticket_id=${ticketId}`)
                .then(response => response.json())
                .then(messages => {
                    const chatMessages = document.getElementById('chat-messages');
                    chatMessages.innerHTML = '';
                    messages.forEach(message => {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = `chat-message ${message.is_admin ? 'admin' : 'user'}`;
                        messageDiv.innerHTML = `
                            <div class="message-content">
                                <p>${message.message}</p>
                                <span class="message-time">${message.created_at}</span>
                            </div>
                        `;
                        chatMessages.appendChild(messageDiv);
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                });
        }

        // Отправка сообщения
        document.getElementById('chat-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const ticketId = document.getElementById('ticket-id').value;
            const message = document.getElementById('chat-message').value;
            
            if (!message.trim()) return;

            fetch('api/tickets/SendMessage.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    ticket_id: ticketId,
                    message: message
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    document.getElementById('chat-message').value = '';
                    loadChatMessages(ticketId);
                } else {
                    alert(result.error || 'Ошибка при отправке сообщения');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при отправке сообщения');
            });
        });
    });
    </script>
    
</body>
</html>