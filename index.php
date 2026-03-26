<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Сафонов А.О., группа 241-352, лаб. А-2, вариант 2</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <img src="https://mospolytech.ru/upload/medialibrary/5fa/Logo_Polytech_rus_main.jpg" alt="Логотип Московского Политеха" class="logo">
        <div class="header-text">
            <h1>Сафонов Александр Олегович</h1>
            <p>Группа: 241-352 | Лабораторная работа № А-2 (вариант 2)</p>
        </div>
    </header>

    <main>
        <?php
        $start_value = 0;  
        $encounting = 15;
        $step = 0.8;
        $min_value = -20;
        $max_value = 50;
        $type = 'E' ;
        $x = $start_value;
        $all_values = [];
        $sum = 0;
        $min_func = null;
        $max_func = null;
        $count_numeric = 0;
        
        echo "<h2>Табулирование функции (тип верстки: $type)</h2>";
        
        if ($type == 'B') echo "<ul>";
        if ($type == 'C') echo "<ol>";
        if ($type == 'D') {
            echo '<table class="result-table">';
            echo '<tr><th>№</th><th>Аргумент (x)</th><th>Значение f(x)</th></tr>';
        }
        
        for ($i = 0; $i < $encounting; $i++, $x += $step) {
            
            if ($x <= 10) {
                if ($x == 0) {
                    $f = "error";
                } else {
                    $f = (10 + $x) / $x;
                }
            } 
            elseif ($x < 20) {
                $f = ($x / 7) * ($x - 2);
            } 
            else {
                $f = $x * 8 + 2;
            }
            
            if (is_numeric($f)) {
                $f_rounded = round($f, 3);
                $all_values[] = $f_rounded;
                $sum += $f_rounded;
                $count_numeric++;
                
                if ($min_func === null || $f_rounded < $min_func) $min_func = $f_rounded;
                if ($max_func === null || $f_rounded > $max_func) $max_func = $f_rounded;
            } else {
                $f_rounded = $f;
            }
            
            switch ($type) {
                case 'A':
                    echo "f($x) = $f_rounded<br>";
                    break;
                case 'B':
                    echo "<li>f(" . round($x, 3) . ") = $f_rounded</li>";
                    break;
                case 'C':
                    echo "<li>f(" . round($x, 3) . ") = $f_rounded</li>";
                    break;
                case 'D':
                    echo "<tr>";
                    echo "<td>" . ($i + 1) . "</td>";
                    echo "<td>" . round($x, 3) . "</td>";
                    echo "<td>$f_rounded</td>";
                    echo "</tr>";
                    break;
                case 'E':
                    echo '<div class="function-block">';
                    echo "f(" . round($x, 3) . ") = $f_rounded";
                    echo '</div>';
                    break;
            }
            
            if (is_numeric($f_rounded) && ($f_rounded <= $min_value || $f_rounded >= $max_value)) {
                echo "<p class='stop-message'>Достигнуто предельное значение функции ($f_rounded). Вычисления остановлены.</p>";
                break;
            }
        }
        
        if ($type == 'B') echo "</ul>";
        if ($type == 'C') echo "</ol>";
        if ($type == 'D') echo "</table>";
        
        echo "<div class='statistics'>";
        echo "<h3>Статистика по числовым значениям функции:</h3>";
        
        if ($count_numeric > 0) {
            $average = $sum / $count_numeric;
            echo "<p>Сумма значений: <strong>" . round($sum, 3) . "</strong></p>";
            echo "<p>Минимальное значение: <strong>" . round($min_func, 3) . "</strong></p>";
            echo "<p>Максимальное значение: <strong>" . round($max_func, 3) . "</strong></p>";
            echo "<p>Среднее арифметическое: <strong>" . round($average, 3) . "</strong></p>";
            echo "<p>Количество числовых значений: <strong>$count_numeric</strong></p>";
        } else {
            echo "<p>Нет числовых значений для анализа.</p>";
        }
        echo "</div>";
        
        $footer_type = $type;
        ?>
    </main>

    <footer>
        <p>Тип верстки в данной сессии: <strong><?php echo $footer_type; ?></strong></p>
        <p>Сафонов Александр Олегович, группа 241-352, 2026</p>
    </footer>
</body>
</html>