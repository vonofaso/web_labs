<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сафонов А.О., группа 241-352, лаб. А-5</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="https://mospolytech.ru/upload/medialibrary/5fa/Logo_Polytech_rus_main.jpg" alt="Логотип Московского Политеха" class="logo">
        <div class="header-text">
            <h1>Сафонов Александр Олегович</h1>
            <p>Группа: 241-352 | Лабораторная работа № А-5: Динамическое формирование контента и меню. Таблица умножения.</p>
        </div>
    </header>

    <main>
        <?php
        date_default_timezone_set('Europe/Moscow');
        
        // Получение параметров из URL
        $html_type = isset($_GET['html_type']) ? $_GET['html_type'] : null;
        $content = isset($_GET['content']) ? $_GET['content'] : null;
        ?>
        
        <!-- ГЛАВНОЕ МЕНЮ-->
        <div id="main_menu">
            <?php
            echo '<a href="?' . ($content !== null ? 'content=' . $content . '&' : '') . 'html_type=TABLE"';
            if ($html_type === null || $html_type === 'TABLE') echo ' class="selected"';
            echo '>Табличная верстка</a>';
            
            echo '<a href="?' . ($content !== null ? 'content=' . $content . '&' : '') . 'html_type=DIV"';
            if ($html_type === 'DIV') echo ' class="selected"';
            echo '>Блочная верстка</a>';
            ?>
        </div>
        
        <div class="content-wrapper">
            <!-- ОСНОВНОЕ МЕНЮ -->
            <div id="side_menu">
                <?php
                echo '<a href="?' . ($html_type !== null ? 'html_type=' . $html_type : '') . '"';
                if ($content === null) echo ' class="selected"';
                echo '>Всё</a>';
                
                for ($i = 2; $i <= 9; $i++) {
                    echo '<a href="?content=' . $i . ($html_type !== null ? '&html_type=' . $html_type : '') . '"';
                    if ($content !== null && $content == $i) echo ' class="selected"';
                    echo '>Таблица умножения на ' . $i . '</a>';
                }
                ?>
            </div>
            
            <!-- ТАБЛИЦА УМНОЖЕНИЯ -->
            <div class="table-container">
                <?php
                // Преобразует число в ссылку
                function outNumAsLink($num) {
                    if ($num >= 2 && $num <= 9) {
                        return '<a href="?content=' . $num . '" class="num-link">' . $num . '</a>';
                    }
                    return $num;
                }
                
                // Выводит столбец умножения на число n
                function outRow($n) {
                    for ($i = 2; $i <= 9; $i++) {
                        echo '<div class="multiplication-row">' . outNumAsLink($n) . ' × ' . outNumAsLink($i) . ' = ' . outNumAsLink($i * $n) . '</div>';
                    }
                }
                
                // Табличная верстка (HTML-таблица)
                function outTableForm() {
                    $content = isset($_GET['content']) ? $_GET['content'] : null;
                    echo '<table class="multiplication-table">';
                    
                    if ($content !== null && $content >= 2 && $content <= 9) {
                        // Один столбец
                        echo '  <tr><th class="table-header" colspan="2">Таблица умножения на ' . $content . '</th></tr>';
                        echo '  <tr><td class="table-cell">'; outRow($content); echo '</td></tr>';
                    } else {
                        // Полная таблица 8x8
                        echo '  <tr><th colspan="8" class="table-header">Таблица умножения (полная)</th></tr>';
                        echo '  <tr>';
                        for ($col = 2; $col <= 9; $col++) echo '<th class="table-subheader">× ' . $col . '</th>';
                        echo '  </tr>';
                        
                        for ($row = 2; $row <= 9; $row++) {
                            echo '  <tr>';
                            for ($col = 2; $col <= 9; $col++) {
                                echo '<td class="table-cell">' . outNumAsLink($row) . ' × ' . outNumAsLink($col) . ' = ' . outNumAsLink($row * $col) . '</td>';
                            }
                            echo '  </tr>';
                        }
                    }
                    echo '</table>';
                }
                
                // Блочная верстка (CSS-блоки)
                function outDivForm() {
                    $content = isset($_GET['content']) ? $_GET['content'] : null;
                    echo '<div class="multiplication-blocks">';
                    
                    if ($content !== null && $content >= 2 && $content <= 9) {
                        // Один крупный блок
                        echo '<div class="block-single"><div class="block-title">Таблица умножения на ' . $content . '</div><div class="block-content">';
                        outRow($content);
                        echo '</div></div>';
                    } else {
                        // Восемь блоков (2-9)
                        for ($i = 2; $i <= 9; $i++) {
                            echo '<div class="block-column"><div class="block-title">× ' . $i . '</div><div class="block-content">';
                            outRow($i);
                            echo '</div></div>';
                        }
                    }
                    echo '</div>';
                }
                
                // Выбор типа верстки
                if ($html_type === null || $html_type === 'TABLE') outTableForm();
                else outDivForm();
                ?>
            </div>
        </div>
        
        <!-- ИНФОРМАЦИОННЫЙ БЛОК -->
        <div class="info-footer">
            <?php
            $info = ($html_type === null || $html_type === 'TABLE') ? 'Тип верстки: Табличная | ' : 'Тип верстки: Блочная | ';
            $info .= ($content === null) ? 'Название: Полная таблица умножения | ' : 'Название: Таблица умножения на ' . $content . ' | ';
            $info .= 'Дата и время: ' . date('d.m.Y H:i:s');  // Московское время
            echo '<p>' . $info . '</p>';
            ?>
        </div>
    </main>

    <footer>
        <p>Сафонов Александр Олегович, группа 241-352, 2026</p>
    </footer>
</body>
</html>