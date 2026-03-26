<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сафонов А.О., группа 241-352, лаб. А-3</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="https://mospolytech.ru/upload/medialibrary/5fa/Logo_Polytech_rus_main.jpg" alt="Логотип Московского Политеха" class="logo">
        <div class="header-text">
            <h1>Сафонов Александр Олегович</h1>
            <p>Группа: 241-352 | Лабораторная работа № А-3: Виртуальная клавиатура</p>
        </div>
    </header>

    <main>
        <?php
        // Получаем текущее состояние из GET
        $displayValue = isset($_GET['display']) ? $_GET['display'] : '';
        $clickCounter = isset($_GET['counter']) ? (int)$_GET['counter'] : 0;

        // Обрабатываем нажатие кнопки
        if (isset($_GET['key'])) {
            $keyPressed = $_GET['key'];
            
            $clickCounter++;
            
            if ($keyPressed === 'reset') {
                $displayValue = '';
            } else {
                $displayValue .= $keyPressed;
            }
        }

        // Формируем ссылки для кнопок с цифрами
        $digitLinks = [];
        for ($i = 0; $i <= 9; $i++) {
            $digitLinks[$i] = "?key=$i&display=" . urlencode($displayValue) . "&counter=$clickCounter";
        }

        // Ссылка для кнопки сброса (с текущим значением счетчика)
        $resetLink = "?key=reset&display=" . urlencode($displayValue) . "&counter=$clickCounter";
        ?>

        <div class="calculator">
            <div class="result"><?php echo htmlspecialchars($displayValue); ?></div>

            <div class="buttons">
                <?php for ($i = 0; $i <= 9; $i++): ?>
                    <a href="<?php echo $digitLinks[$i]; ?>" class="button"><?php echo $i; ?></a>
                <?php endfor; ?>
                                
                <a href="<?php echo $resetLink; ?>" class="button reset">СБРОС</a>
            </div>
        </div>
    </main>

    <footer>
        <p>Общее число нажатий кнопок: <strong><?php echo $clickCounter; ?></strong></p>
        <p>Сафонов Александр Олегович, группа 241-352, 2026</p>
    </footer>
</body>
</html>