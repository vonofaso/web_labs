<?php
// Генерация случайного числа от 0 до 100 (с двумя знаками после запятой)
function getRandomValue() {
    return mt_rand(0, 10000) / 100;
}

// Обработка данных формы
if( isset( $_POST['A'] ) ) // если из формы были переданы данные
{
    // Замена запятых на точки в числах
    $_POST['A'] = str_replace(',', '.', $_POST['A']);
    $_POST['B'] = str_replace(',', '.', $_POST['B']);
    $_POST['C'] = str_replace(',', '.', $_POST['C']);
    
    // Автоматическое решение задачи
    if( $_POST['TASK'] == 'mean' ) // среднее арифметическое
    {
        $result = round( ($_POST['A'] + $_POST['B'] + $_POST['C']) / 3, 2 );
    }
    elseif( $_POST['TASK'] == 'perimetr' ) // периметр треугольника
    {
        $result = $_POST['A'] + $_POST['B'] + $_POST['C'];
    }
    elseif( $_POST['TASK'] == 'area_triangle' ) // площадь треугольника (формула Герона)
    {
        $p = ($_POST['A'] + $_POST['B'] + $_POST['C']) / 2;
        $result = round( sqrt( $p * ($p - $_POST['A']) * ($p - $_POST['B']) * ($p - $_POST['C']) ), 2 );
    }
    elseif( $_POST['TASK'] == 'volume' ) // объем параллелепипеда
    {
        $result = $_POST['A'] * $_POST['B'] * $_POST['C'];
    }
    elseif( $_POST['TASK'] == 'hypotenuse' ) // гипотенуза
    {
        $result = round( sqrt( pow($_POST['A'], 2) + pow($_POST['B'], 2) ), 2 );
    }
    elseif( $_POST['TASK'] == 'quadratic' ) // квадратный трехчлен при x=1
    {
        $result = $_POST['A'] + $_POST['B'] + $_POST['C'];
    }
    
    // Подготовка отчета
    $out_text = '<div class="test-report">';
    $out_text .= '<h2>Результаты тестирования</h2>';
    $out_text .= '<p><strong>ФИО:</strong> ' . htmlspecialchars($_POST['FIO']) . '</p>';
    $out_text .= '<p><strong>Группа:</strong> ' . htmlspecialchars($_POST['GROUP']) . '</p>';
    
    if( !empty($_POST['ABOUT']) )
    {
        $out_text .= '<p><strong>О себе:</strong> ' . nl2br(htmlspecialchars($_POST['ABOUT'])) . '</p>';
    }
    
    $out_text .= '<p><strong>Тип задачи:</strong> ';
    if( $_POST['TASK'] == 'mean' ) $out_text .= 'Среднее арифметическое';
    elseif( $_POST['TASK'] == 'perimetr' ) $out_text .= 'Периметр треугольника';
    elseif( $_POST['TASK'] == 'area_triangle' ) $out_text .= 'Площадь треугольника (формула Герона)';
    elseif( $_POST['TASK'] == 'volume' ) $out_text .= 'Объем параллелепипеда';
    elseif( $_POST['TASK'] == 'hypotenuse' ) $out_text .= 'Гипотенуза прямоугольного треугольника';
    elseif( $_POST['TASK'] == 'quadratic' ) $out_text .= 'Значение квадратного трехчлена (при x=1)';
    $out_text .= '</p>';
    
    $out_text .= '<p><strong>Входные данные:</strong> A = ' . $_POST['A'] . ', B = ' . $_POST['B'] . ', C = ' . $_POST['C'] . '</p>';
    
    if( empty($_POST['answer']) )
    {
        $out_text .= '<p><strong>Предполагаемый результат:</strong> Задача самостоятельно решена не была</p>';
    }
    else
    {
        $user_answer = str_replace(',', '.', $_POST['answer']);
        $out_text .= '<p><strong>Предполагаемый результат:</strong> ' . htmlspecialchars($_POST['answer']) . '</p>';
    }
    
    $out_text .= '<p><strong>Вычисленный программой результат:</strong> ' . $result . '</p>';
    
    if( $result == str_replace(',', '.', $_POST['answer']) )
    {
        $out_text .= '<p class="success"><strong>Тест пройден</strong></p>';
    }
    else
    {
        $out_text .= '<p class="error"><strong>Ошибка: тест не пройден</strong></p>';
    }
    $out_text .= '</div>';
    
    // Сохраняем отчет в переменную для вывода
    $report_html = $out_text;
    
    // Отправка по email
    if( array_key_exists('send_mail', $_POST) )
    {
        $mail_text = strip_tags(str_replace('<br>', "\r\n", $out_text));
        $mail_text = preg_replace('/<[^>]*>/', '', $mail_text);
        
        mail( $_POST['MAIL'], 'Результат тестирования',
            $mail_text,
            "From: auto@test.ru\r\nContent-Type: text/plain; charset=utf-8\r\n" );
        
        $mail_message = '<p class="mail-info">Результаты теста были автоматически отправлены на e-mail ' . htmlspecialchars($_POST['MAIL']) . '</p>';
    }
    
    // Ссылка "Повторить тест" только для версии в браузере
    if( $_POST['version'] == 'browser' )
    {
        $repeat_link = '<a href="?FIO=' . urlencode($_POST['FIO']) . '&GROUP=' . urlencode($_POST['GROUP']) . '&reset=1" class="repeat-btn">Повторить тест</a>';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сафонов А.О., группа 241-352, лаб. А-6</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="https://mospolytech.ru/upload/medialibrary/5fa/Logo_Polytech_rus_main.jpg" alt="Логотип Московского Политеха" class="logo">
        <div class="header-text">
            <h1>Сафонов Александр Олегович</h1>
            <p>Группа: 241-352 | Лабораторная работа № А-6: Использование форм для передачи данных в программу РНР. Тест математических знаний.</p>
        </div>
    </header>

    <main>
        <?php if( isset( $report_html ) ): ?>
            <!-- Вывод результатов -->
            <?php echo $report_html; ?>
            <?php if( isset($mail_message) ) echo $mail_message; ?>
            <?php if( isset($repeat_link) ) echo $repeat_link; ?>
        <?php else: ?>
            <!-- Вывод формы -->
            <?php
            // Получение значений из GET для повторного теста
            $fio_val = isset($_GET['FIO']) ? $_GET['FIO'] : '';
            $group_val = isset($_GET['GROUP']) ? $_GET['GROUP'] : '';
            
            // Генерация случайных чисел для A, B, C
            if( isset($_GET['reset']) )
            {
                $a_val = getRandomValue();
                $b_val = getRandomValue();
                $c_val = getRandomValue();
            }
            else
            {
                $a_val = getRandomValue();
                $b_val = getRandomValue();
                $c_val = getRandomValue();
            }
            ?>
            
            <div class="test-form-container">
                <h2>Тест математических знаний</h2>
                
                <form method="post" action="" class="test-form">
                    <div class="form-row">
                        <label for="FIO">ФИО:</label>
                        <input type="text" id="FIO" name="FIO" value="<?php echo htmlspecialchars($fio_val); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <label for="GROUP">Номер группы:</label>
                        <input type="text" id="GROUP" name="GROUP" value="<?php echo htmlspecialchars($group_val); ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <label for="A">Значение А:</label>
                        <input type="text" id="A" name="A" value="<?php echo $a_val; ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <label for="B">Значение В:</label>
                        <input type="text" id="B" name="B" value="<?php echo $b_val; ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <label for="C">Значение С:</label>
                        <input type="text" id="C" name="C" value="<?php echo $c_val; ?>" required>
                    </div>
                    
                    <div class="form-row">
                        <label for="answer">Ваш ответ:</label>
                        <input type="text" id="answer" name="answer">
                    </div>
                    
                    <div class="form-row email-group" id="emailGroup">
                        <label for="MAIL">Ваш e-mail:</label>
                        <input type="email" id="MAIL" name="MAIL">
                    </div>
                    
                    <div class="form-row">
                        <label for="ABOUT">Немного о себе:</label>
                        <textarea id="ABOUT" name="ABOUT" rows="3"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <label for="TASK">Выберите задачу:</label>
                        <select id="TASK" name="TASK">
                            <option value="area_triangle">Площадь треугольника</option>
                            <option value="perimetr">Периметр треугольника</option>
                            <option value="volume">Объем параллелепипеда</option>
                            <option value="mean">Среднее арифметическое</option>
                            <option value="hypotenuse">Гипотенуза прямоугольного треугольника</option>
                            <option value="quadratic">Квадратный трехчлен (ax²+bx+c, x=1)</option>
                        </select>
                    </div>
                    
                    <div class="form-row checkbox-row">
                        <input type="checkbox" id="send_mail" name="send_mail">
                        <label for="send_mail">Отправить результат теста по e-mail</label>
                    </div>
                    
                    <div class="form-row">
                        <label for="version">Версия отображения:</label>
                        <select id="version" name="version">
                            <option value="browser">Версия для просмотра в браузере</option>
                            <option value="print">Версия для печати</option>
                        </select>
                    </div>
                    
                    <div class="form-row button-row">
                        <button type="submit">Проверить</button>
                    </div>
                </form>
            </div>
            
            <script>
                // JavaScript для показа/скрытия поля email
                const checkbox = document.getElementById('send_mail');
                const emailGroup = document.getElementById('emailGroup');
                
                // Изначально скрываем поле email
                emailGroup.style.display = 'none';
                
                checkbox.addEventListener('change', function() {
                    if(this.checked) {
                        emailGroup.style.display = 'flex';
                    } else {
                        emailGroup.style.display = 'none';
                        document.getElementById('MAIL').value = '';
                    }
                });
            </script>
        <?php endif; ?>
    </main>

    <footer>
        <p>Сафонов Александр Олегович, группа 241-352, 2026</p>
    </footer>
</body>
</html>