<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сафонов А.О., группа 241-352, лаб. А-4</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="https://mospolytech.ru/upload/medialibrary/5fa/Logo_Polytech_rus_main.jpg" alt="Логотип Московского Политеха" class="logo">
        <div class="header-text">
            <h1>Сафонов Александр Олегович</h1>
            <p>Группа: 241-352 | Лабораторная работа № А-4: Пользовательские функции и вывод таблиц</p>
        </div>
    </header>

    <main>
        <?php
        
        $columnCount = 4;
        
        $structures = array(
            // 1. Обычная таблица с фруктами
            "Яблоко*Банан*Апельсин#Груша*Виноград*Киви#Ананас*Манго*Персик",
            
            // 2. Таблица с данными студентов
            "Студент*Группа*Оценка#Сафонов*241-352*5#Иванов*241-352*4#Сидоров*241-352*5",
            
            // 3. Таблица товаров
            "Название*Цена*Количество#Хлеб*45*100#Молоко*80*50#Сыр*350*20",
            
            // 4. Строки с разным количеством ячеек
            "Один*Два*Три*Четыре#Пять*Шесть*Семь#Восемь*Девять",
            
            // 5. Строки с пустыми ячейками
            "Имя*Возраст*Город#Иван**Москва#Мария*25*#Петр**",
            
            // 6. Есть пустая строка в середине
            "Заголовок1*Заголовок2*Заголовок3##Строка после пустой*с*данными",
            
            // 7. Пустая строка в конце
            "Данные*есть*везде#",
            
            // 8. Таблица с одной строкой
            "Только*одна*строка*таблицы",
            
            // 9. Таблица вообще без строк (пустая)
            "",
            
            // 10. Таблица со строкой без ячеек (пустая строка)
            "#",
            
            // 11. Строка с символами-разделителями внутри текста
            "Текст с * внутри*обрабатывается*корректно#Второй*ряд*данных",
            
            // 12. Сложная структура
            "A1*A2*A3*A4*A5#B1*B2*B3#C1*C2#D1#"
        );
                
        
        // Формирует HTML-код одной строки таблицы
        function getTR($rowData, $cols) {
            // Разбиваем строку на ячейки
            $cells = explode('*', $rowData);
            
            // Если строка пустая или нет ячеек
            if (empty($rowData) || (count($cells) == 1 && $cells[0] === '')) {
                return '';
            }
            
            $html = '<tr>';
            
            // Выводим существующие ячейки
            for ($i = 0; $i < count($cells); $i++) {
                $content = ($cells[$i] !== '') ? htmlspecialchars($cells[$i]) : '&nbsp;';
                $html .= '<td>' . $content . '</td>';
            }
            
            // Добавляем пустые ячейки до требуемого количества колонок
            for ($i = count($cells); $i < $cols; $i++) {
                $html .= '<td>&nbsp;</td>';
            }
            
            $html .= '</tr>';
            return $html;
        }
        
        // Выводит HTML-код таблицы
        function outTable($structure, $number, $cols) {
            // Заголовок таблицы
            echo "<h2>Таблица №{$number}</h2>";
            
            // Разбиваем на строки
            $rows = explode('#', $structure);
            
            // Проверка: нет строк
            if (count($rows) == 0 || (count($rows) == 1 && $rows[0] === '')) {
                echo "<p class='error'>В таблице нет строк</p>";
                return;
            }
            
            $rowsHTML = '';
            $hasCells = false;
            
            // Обрабатываем каждую строку
            for ($i = 0; $i < count($rows); $i++) {
                $rowHTML = getTR($rows[$i], $cols);
                if (!empty($rowHTML)) {
                    $rowsHTML .= $rowHTML;
                    $hasCells = true;
                }
            }
            
            // Проверка: есть ли строки с ячейками
            if (!$hasCells) {
                echo "<p class='error'>В таблице нет строк с ячейками</p>";
                return;
            }
            
            // Вывод таблицы
            echo "<table>" . $rowsHTML . "</table>";
        }
        
        
        // Проверка количества колонок
        if ($columnCount <= 0) {
            echo "<p class='error'>Неправильное число колонок</p>";
        } else {
            // Вывод всех таблиц из массива
            for ($i = 0; $i < count($structures); $i++) {
                outTable($structures[$i], $i + 1, $columnCount);
            }
        }
        ?>
    </main>

    <footer>
        <p>Сафонов Александр Олегович, группа 241-352, 2026</p>
    </footer>
</body>
</html>