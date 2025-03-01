<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
        }
        textarea {
            min-height: 150px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <form action="" method="POST">
            <div class="form-group">
                <label for="header">Обращение:</label>
                <input type="text" id="header" name="header" required>
            </div>
            
            <div class="form-group">
                <label for="main">Основной контент:</label>
                <textarea id="main" name="main" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="footer">Футер:</label>
                <input type="text" id="footer" name="footer" required>
            </div>
            
            <button type="submit">Отправить</button>
        </form>
    </div>
</body>
</html> 