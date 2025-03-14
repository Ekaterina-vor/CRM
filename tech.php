<?php session_start();

if (isset($_GET['do']) && $_GET['do'] === 'logout') {
    require_once 'api/auth/LogoutUser.php';
    require_once 'api/DB.php';

    LogoutUser('login.php', $DB, $_SESSION['token']);

    exit;
}

require_once 'api/auth/AuthCheck.php';
require_once 'api/helpers/InputDefaultValue.php';
require_once 'api/clients/ClientsSearch.php';
require_once 'api/DB.php';

AuthCheck('', 'login.php');

require_once 'api/helpers/getUserType.php';

$userType = getUserType($DB);

if ($userType !== 'tech') {
    header('Location: clients.php');
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/settings.css">
    <link rel="stylesheet" href="styles/pages/clients.css">
    <link rel="stylesheet" href="styles/pages/tech.css">
    <link rel="stylesheet" href="styles/modules/font-awesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="styles/modules/micromodal.css">
    <title>CRM | Техподдержка</title>
</head>
<body>
    <header class="filters-header">
        <div class="filters-container">
            <p>
                <?php 
                    require 'api/DB.php';
                    require_once 'api/clients/AdminName.php';
                    require_once 'api/helpers/getUserType.php';

                    echo AdminName($_SESSION['token'], $DB);
                    $userType = getUserType($DB);
                    echo " <span style='color: green'>($userType)</span>";
                ?>
            </p>
            <ul>
                <li><a href="clients.php">Клиенты</a></li>
                <li><a href="products.php">Товары</a></li>
                <li><a href="orders.php">Заказы</a></li>
                <?php
                    if ($userType === 'tech') {
                        echo '<li><a href="tech.php">Обращение пользователя</a></li>';
                    }
                ?>
            </ul>
            <a class="filters-header__logout" href="?do=logout">Выйти</a>
        </div>
    </header>
    <main class="main">
        <div class="container">
            <div class="tickets-header">
                <h1 class="tickets-title">Обращения пользователей</h1>
                <div class="tickets-filters">
                    <button class="ticket-filter <?php echo (!isset($_GET['status']) || $_GET['status'] === 'all') ? 'active' : ''; ?>" onclick="window.location.href='?status=all'">
                        Все
                    </button>
                    <button class="ticket-filter <?php echo (isset($_GET['status']) && $_GET['status'] === 'waiting') ? 'active' : ''; ?>" onclick="window.location.href='?status=waiting'">
                        Ожидают
                    </button>
                    <button class="ticket-filter <?php echo (isset($_GET['status']) && $_GET['status'] === 'work') ? 'active' : ''; ?>" onclick="window.location.href='?status=work'">
                        В работе
                    </button>
                    <button class="ticket-filter <?php echo (isset($_GET['status']) && $_GET['status'] === 'complete') ? 'active' : ''; ?>" onclick="window.location.href='?status=complete'">
                        Выполнены
                    </button>
                </div>
            </div>

            <div class="tickets-grid">
                <?php
                    // Получаем текущую страницу
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $perPage = 12; // Количество тикетов на странице
                    $offset = ($page - 1) * $perPage;

                    // Формируем условие для фильтрации по статусу
                    $statusFilter = '';
                    if (isset($_GET['status']) && $_GET['status'] !== 'all') {
                        $status = $_GET['status'];
                        $statusFilter = "WHERE status = '$status'";
                    }

                    // Получаем общее количество тикетов
                    $countQuery = "SELECT COUNT(*) as count FROM tickets $statusFilter";
                    $totalTickets = $DB->query($countQuery)->fetch()['count'];
                    $totalPages = ceil($totalTickets / $perPage);

                    // Получаем тикеты для текущей страницы
                    $query = "SELECT t.*, 
                             cl.name as client_name,
                             CONCAT(COALESCE(a.surname, ''), ' ', COALESCE(a.name, '')) as admin_name
                             FROM tickets t 
                             LEFT JOIN clients cl ON t.clients = cl.id 
                             LEFT JOIN users a ON t.admin = a.id 
                             $statusFilter 
                             ORDER BY t.created_at DESC 
                             LIMIT $perPage OFFSET $offset";
                    
                    $tickets = $DB->query($query)->fetchAll();

                    foreach ($tickets as $ticket) {
                        $statusClass = $ticket['status'];
                        $statusText = [
                            'waiting' => 'Ожидает',
                            'work' => 'В работе',
                            'complete' => 'Выполнено'
                        ][$ticket['status']];

                        $statusIcon = [
                            'waiting' => 'clock-o',
                            'work' => 'cog',
                            'complete' => 'check'
                        ][$ticket['status']];

                        echo "
                        <div class='ticket-card'>
                            <div class='ticket-header'>
                                <span class='ticket-id'>#" . $ticket['id'] . "</span>
                                <span class='ticket-type " . $ticket['type'] . "'>" . 
                                    ($ticket['type'] === 'tech' ? 'Техническая неполадка' : 'Проблема с CRM') . 
                                "</span>
                            </div>
                            <div class='ticket-message'>" . htmlspecialchars($ticket['message']) . "</div>
                            <div class='ticket-info'>
                                <span><i class='fa fa-user'></i>Клиент: " . htmlspecialchars($ticket['client_name'] ?? 'Неизвестно') . "</span>
                                <span><i class='fa fa-user-secret'></i>Админ: " . htmlspecialchars(trim($ticket['admin_name']) ?? 'Не назначен') . "</span>
                                <div class='ticket-status-container'>
                                    <select class='status-select' data-ticket-id='" . $ticket['id'] . "'>
                                        <option value='waiting' " . ($ticket['status'] === 'waiting' ? 'selected' : '') . ">Ожидает</option>
                                        <option value='work' " . ($ticket['status'] === 'work' ? 'selected' : '') . ">В работе</option>
                                        <option value='complete' " . ($ticket['status'] === 'complete' ? 'selected' : '') . ">Выполнено</option>
                                    </select>
                                    <span class='ticket-status " . $statusClass . "'>
                                        <i class='fa fa-" . $statusIcon . "'></i>
                                        " . $statusText . "
                                    </span>
                                </div>";
                                
                                // Добавляем отображение файлов
                                if (!empty($ticket['file_path'])) {
                                    echo "<div class='ticket-files'>";
                                    $files = explode('|', $ticket['file_path']);
                                    foreach ($files as $file) {
                                        // Убираем начальный слеш, если он есть
                                        $file = ltrim($file, '/');
                                        
                                        $fileName = basename($file);
                                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                        $fileIcon = match($fileExt) {
                                            'pdf' => 'file-pdf-o',
                                            'doc', 'docx' => 'file-word-o',
                                            'xls', 'xlsx' => 'file-excel-o',
                                            'jpg', 'jpeg', 'png', 'gif' => 'file-image-o',
                                            default => 'file-o'
                                        };
                                        
                                        if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            echo "<a href='javascript:void(0)' onclick='showFile(\"" . htmlspecialchars($file) . "\", \"image\")' class='ticket-file'>
                                                    <i class='fa fa-" . $fileIcon . "'></i>
                                                    " . htmlspecialchars($fileName) . "
                                                </a>";
                                        } 
                                        else if ($fileExt === 'pdf') {
                                            echo "<a href='javascript:void(0)' onclick='showFile(\"" . htmlspecialchars($file) . "\", \"pdf\")' class='ticket-file'>
                                                    <i class='fa fa-" . $fileIcon . "'></i>
                                                    " . htmlspecialchars($fileName) . "
                                                </a>";
                                        }
                                        else {
                                            echo "<a href='" . htmlspecialchars($file) . "' target='_blank' class='ticket-file'>
                                                    <i class='fa fa-" . $fileIcon . "'></i>
                                                    " . htmlspecialchars($fileName) . "
                                                </a>";
                                        }
                                    }
                                    echo "</div>";
                                }
                                
                            echo "</div>
                            <div class='ticket-date'>
                                <i class='fa fa-calendar'></i> 
                                " . date('d.m.Y H:i', strtotime($ticket['created_at'])) . "
                            </div>
                            <button class='chat-btn' onclick='openChat(" . $ticket['id'] . ")'><i class='fa fa-comments'></i> Открыть чат</button>
                        </div>";
                    }
                ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination-container">
                <?php
                    // Сохраняем параметры фильтрации для пагинации
                    $filterParams = isset($_GET['status']) ? '&status=' . $_GET['status'] : '';

                    // Кнопка "Предыдущая"
                    $prevDisabled = ($page <= 1) ? " disabled" : "";
                    echo "<a href='?page=" . ($page - 1) . $filterParams . "'$prevDisabled>
                            <i class='fa fa-arrow-left'></i>
                          </a>";

                    // Номера страниц
                    echo "<div class='pagination'>";
                    for ($i = 1; $i <= $totalPages; $i++) {
                        $activeClass = ($i === $page) ? " class='active'" : "";
                        echo "<a href='?page=$i$filterParams'$activeClass>$i</a>";
                    }
                    echo "</div>";

                    // Кнопка "Следующая"
                    $nextDisabled = ($page >= $totalPages) ? " disabled" : "";
                    echo "<a href='?page=" . ($page + 1) . $filterParams . "'$nextDisabled>
                            <i class='fa fa-arrow-right'></i>
                          </a>";
                ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Модальное окно для ответа -->
    <div class="modal micromodal-slide" id="reply-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Ответ клиенту</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content">
                    <form id="replyForm" onsubmit="sendReply(event)">
                        <input type="hidden" id="ticket_id" name="ticket_id">
                        <input type="hidden" id="user_id" name="user_id">
                        <div class="form-group">
                            <label for="message">Сообщение</label>
                            <textarea id="message" name="message" rows="4" required></textarea>
                        </div>
                        <div class="modal__footer">
                            <button type="submit" class="modal__btn modal__btn-primary">Отправить</button>
                            <button type="button" class="modal__btn" data-micromodal-close>Отмена</button>
                        </div>
                    </form>
                </main>
            </div>
        </div>
    </div>

    <!-- Добавляем модальное окно для просмотра файлов -->
    <div class="modal micromodal-slide" id="file-preview-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container modal__container--large" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Просмотр файла</h2>
                    <button class="modal__close" aria-label="Close modal" data-micromodal-close></button>
                </header>
                <main class="modal__content" id="file-preview-content">
                    <!-- Контент будет добавлен динамически -->
                </main>
            </div>
        </div>
    </div>

    <!-- Добавляем модальное окно для чата -->
    <div class="modal micromodal-slide" id="chat-modal" aria-hidden="true">
        <div class="modal__overlay" tabindex="-1" data-micromodal-close>
            <div class="modal__container" role="dialog" aria-modal="true">
                <header class="modal__header">
                    <h2 class="modal__title">Чат с клиентом</h2>
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
        background: #e9ecef;
    }

    .chat-message.admin .message-content {
        background: rgb(154, 197, 165);
        color: white;
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
        background: rgb(154, 197, 165);
        color: white;
        border: none;
        padding: 0 20px;
        border-radius: 4px;
        cursor: pointer;
    }

    .chat-form button:hover {
        background: rgb(134, 177, 145);
    }

    .chat-btn {
        background: rgb(154, 197, 165);
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: 10px;
    }

    .chat-btn:hover {
        background: rgb(134, 177, 145);
    }
    </style>

    <script defer src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Инициализация MicroModal
            MicroModal.init({
                openTrigger: 'data-micromodal-trigger',
                closeTrigger: 'data-micromodal-close',
                disableFocus: true,
                disableScroll: true,
                awaitOpenAnimation: true,
                awaitCloseAnimation: true
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
                    })
                    .catch(error => {
                        console.error('Ошибка загрузки сообщений:', error);
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

            // Обработчик изменения статуса
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    const ticketId = this.dataset.ticketId;
                    const newStatus = this.value;
                    const statusContainer = this.closest('.ticket-status-container');
                    const statusSpan = statusContainer.querySelector('.ticket-status');

                    fetch('api/tickets/UpdateStatus.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'ticket_id=' + ticketId + '&status=' + newStatus
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Обновляем отображение статуса
                            const statusText = {
                                'waiting': 'Ожидает',
                                'work': 'В работе',
                                'complete': 'Выполнено'
                            }[newStatus];
                            
                            const statusIcon = {
                                'waiting': 'clock-o',
                                'work': 'cog',
                                'complete': 'check'
                            }[newStatus];

                            // Обновляем класс и содержимое статуса
                            statusSpan.className = 'ticket-status ' + newStatus;
                            statusSpan.innerHTML = `<i class="fa fa-${statusIcon}"></i>${statusText}`;
                            
                            // Если включен фильтр по статусу, обновляем страницу
                            const urlParams = new URLSearchParams(window.location.search);
                            if (urlParams.has('status') && urlParams.get('status') !== 'all') {
                                location.reload();
                            }
                        } else {
                            alert('Ошибка при обновлении статуса');
                            // Возвращаем предыдущее значение
                            select.value = statusSpan.className.split(' ')[1];
                        }
                    })
                    .catch(error => {
                        console.error('Ошибка:', error);
                        alert('Ошибка при обновлении статуса');
                        // Возвращаем предыдущее значение
                        select.value = statusSpan.className.split(' ')[1];
                    });
                });
            });
        });

        function showFile(filePath, type) {
            console.log('Opening file:', filePath, 'Type:', type);
            const container = document.getElementById('file-preview-content');
            container.innerHTML = '';

            // Убираем начальный слеш, если он есть
            filePath = filePath.replace(/^\//, '');

            if (type === 'image') {
                const img = document.createElement('img');
                img.src = filePath;
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                img.style.objectFit = 'contain';
                
                img.onerror = function() {
                    console.error('Error loading image:', filePath);
                    container.innerHTML = '<p style="color: red;">Ошибка загрузки изображения</p>';
                };
                
                img.onload = function() {
                    console.log('Image loaded successfully');
                };
                
                container.appendChild(img);
            } else if (type === 'pdf') {
                const iframe = document.createElement('iframe');
                iframe.src = filePath;
                iframe.style.width = '100%';
                iframe.style.height = '80vh';
                iframe.style.border = 'none';
                container.appendChild(iframe);
            }

            MicroModal.show('file-preview-modal', {
                onShow: modal => console.log('Файл открыт для просмотра'),
                onClose: modal => console.log('Просмотр файла закрыт'),
                disableScroll: true,
                disableFocus: true
            });
        }

        function showReplyModal(ticketId, userId) {
            document.getElementById('ticket_id').value = ticketId;
            document.getElementById('user_id').value = userId;
            MicroModal.show('reply-modal');
        }

        function sendReply(event) {
            event.preventDefault();
            const formData = new FormData(event.target);

            fetch('api/tickets/AddMessage.php', {
                method: 'POST',
                body: new URLSearchParams(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    MicroModal.close('reply-modal');
                    event.target.reset();
                    alert('Ответ успешно отправлен');
                } else {
                    alert('Ошибка при отправке ответа');
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Ошибка при отправке ответа');
            });
        }
    </script>
</body>
</html>