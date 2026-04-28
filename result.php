<?php
// Устанавливаем часовой пояс, чтобы временные метки были московскими
date_default_timezone_set('Europe/Moscow');

// ---------------------------------------------------------------------
// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// ---------------------------------------------------------------------

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Разбивает строку на массив отдельных символов.
 * Используется регулярное выражение с флагом 'u' (UTF-8),
 * чтобы корректно обрабатывать кириллицу и многобайтовые символы.
 * PREG_SPLIT_NO_EMPTY — не включать пустые элементы.
 */
function splitChars($text)
{
    if ($text === '') {
        return array();
    }
    return preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: array();
}

/**
 * Переводит ОДИН символ в нижний регистр.
 * Для кириллицы используем собственную таблицу соответствий,
 * потому что стандартный mb_strtolower может работать нестабильно
 * на разных версиях PHP для буквы Ё и некоторых других.
 * Для латиницы и прочих символов — strtolower.
 */
function lowerChar($char)
{
    // Таблица соответствий заглавных русских букв строчным
    $map = array(
        'А' => 'а', 'Б' => 'б', 'В' => 'в', 'Г' => 'г', 'Д' => 'д', 'Е' => 'е', 'Ё' => 'ё',
        'Ж' => 'ж', 'З' => 'з', 'И' => 'и', 'Й' => 'й', 'К' => 'к', 'Л' => 'л', 'М' => 'м',
        'Н' => 'н', 'О' => 'о', 'П' => 'п', 'Р' => 'р', 'С' => 'с', 'Т' => 'т', 'У' => 'у',
        'Ф' => 'ф', 'Х' => 'х', 'Ц' => 'ц', 'Ч' => 'ч', 'Ш' => 'ш', 'Щ' => 'щ', 'Ъ' => 'ъ',
        'Ы' => 'ы', 'Ь' => 'ь', 'Э' => 'э', 'Ю' => 'ю', 'Я' => 'я'
    );
    // Если символ есть в таблице — возвращаем строчный аналог
    if (isset($map[$char])) {
        return $map[$char];
    }
    // Иначе (латиница, цифры, знаки) — стандартная функция
    return strtolower($char);
}

/**
 * Переводит ЦЕЛУЮ строку в нижний регистр посимвольно,
 * используя функцию lowerChar для каждого символа.
 */
function lowerText($text)
{
    $result = '';
    foreach (splitChars($text) as $char) {
        $result .= lowerChar($char);
    }
    return $result;
}

/**
 * Общее количество символов в тексте (включая пробелы и переносы строк).
 */
function charCount($text)
{
    return count(splitChars($text));
}

/**
 * Подсчёт совпадений регулярного выражения в тексте.
 * Возвращает количество найденных вхождений.
 */
function regexCount($pattern, $text)
{
    return preg_match_all($pattern, $text, $matches) ?: 0;
}

/**
 * Замена непечатных символов на их текстовое описание для отображения в таблице.
 */
function describeChar($char)
{
    if ($char === ' ') return '[пробел]';
    if ($char === "\n") return '[перенос строки]';
    if ($char === "\r") return '[возврат каретки]';
    if ($char === "\t") return '[табуляция]';
    return $char;
}

/**
 * Сравнение двух слов для сортировки.
 * Проблема: strcmp в UTF-8 сортирует русские буквы некорректно.
 * Решение: переводим строки в CP1251 (Windows-1251),
 * где русские буквы идут по алфавиту, и сравниваем уже в этой кодировке.
 * Если перекодировка не удалась — сравниваем в UTF-8 (для английских слов).
 */
function compareWords($left, $right)
{
    $leftLower  = lowerText($left);
    $rightLower = lowerText($right);

    // Перекодируем в CP1251 для корректной сортировки кириллицы
    // IGNORE — символы, которых нет в CP1251, пропускаются
    $leftCp  = iconv('UTF-8', 'CP1251//IGNORE', $leftLower);
    $rightCp = iconv('UTF-8', 'CP1251//IGNORE', $rightLower);

    // Если обе перекодировки успешны — сравниваем в CP1251
    if ($leftCp !== false && $rightCp !== false) {
        return strcmp($leftCp, $rightCp);
    }

    // Иначе (например, только английские буквы) — сравниваем как есть
    return strcmp($leftLower, $rightLower);
}

// ---------------------------------------------------------------------
// ОСНОВНАЯ ФУНКЦИЯ АНАЛИЗА ТЕКСТА
// ---------------------------------------------------------------------

/**
 * Выполняет полный статистический анализ текста.
 * Возвращает ассоциативный массив с результатами.
 */
function analyzeText($text)
{
    // --- Анализ символов ---

    // Разбиваем текст на отдельные символы
    $chars = splitChars($text);
    $symbolStats = array();

    // Считаем количество вхождений каждого символа (без учёта регистра)
    foreach ($chars as $char) {
        $key = lowerChar($char); // приводим к нижнему регистру для группировки
        if (isset($symbolStats[$key])) {
            $symbolStats[$key]++;
        } else {
            $symbolStats[$key] = 1;
        }
    }

    // Сортируем символы по алфавиту (русские + английские)
    uksort($symbolStats, 'compareWords');

    // --- Анализ слов ---

    /*
     * Регулярное выражение для поиска слов:
     * [\p{L}\p{N}]+       — одна или больше букв или цифр
     * (?:[-\'][\p{L}\p{N}]+)* — ноль или больше частей, начинающихся с дефиса или апострофа
     *                          и продолжающихся буквами/цифрами
     * u — модификатор UTF-8
     */
    preg_match_all('/[\p{L}\p{N}]+(?:[-\'][\p{L}\p{N}]+)*/u', $text, $wordMatches);
    $wordStats = array();

    // Считаем количество вхождений каждого слова (без учёта регистра)
    foreach ($wordMatches[0] as $word) {
        $normalized = lowerText($word); // приводим к нижнему регистру
        if (isset($wordStats[$normalized])) {
            $wordStats[$normalized]++;
        } else {
            $wordStats[$normalized] = 1;
        }
    }

    // Сортируем слова по алфавиту
    uksort($wordStats, 'compareWords');

    // --- Возвращаем все результаты одним массивом ---
    return array(
        'char_total'  => charCount($text),       // всего символов
        'letters'     => regexCount('/\p{L}/u', $text),    // буквы (любые алфавиты)
        'lowercase'   => regexCount('/\p{Ll}/u', $text),   // строчные буквы
        'uppercase'   => regexCount('/\p{Lu}/u', $text),   // заглавные буквы
        'punctuation' => regexCount('/\p{P}/u', $text),    // знаки препинания
        'digits'      => regexCount('/\p{N}/u', $text),    // цифры
        'words_total' => count($wordMatches[0]),            // общее количество слов
        'symbols'     => $symbolStats,                      // таблица частот символов
        'words'       => $wordStats                         // таблица частот слов
    );
}

// ---------------------------------------------------------------------
// ТОЧКА ВХОДА: получение данных и вызов анализа
// ---------------------------------------------------------------------

// Берём текст из POST-запроса. Если ключ 'data' отсутствует — пустая строка.
$text    = $_POST['data'] ?? '';
$trimmed = trim($text);  // убираем пробелы по краям
$stats   = null;

// Если после обрезки что-то осталось — анализируем
if ($trimmed !== '') {
    $stats = analyzeText($text);  // передаём оригинал, не обрезанный, чтобы сохранить пробелы в начале/конце
}
?>
<!-- Далее идёт HTML-шаблон для вывода результатов -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат анализа текста</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <header class="page-header">
            <div>
                <h1>Результат анализа текста</h1>
                <p>UTF-8, русский и английский текст обрабатываются в одном интерфейсе.</p>
            </div>
            <!-- Ссылка для возврата к форме ввода -->
            <a href="index.html" class="back-link">Другой анализ</a>
        </header>

        <main class="panel">
            <!-- Если stats === null — текст не был введён -->
            <?php if ($stats === null): ?>
                <div class="summary-card">
                    <h2>Статус</h2>
                    <p class="error">Нет текста для анализа</p>
                </div>

            <!-- Иначе выводим результаты -->
            <?php else: ?>
                <!-- Блок с исходным текстом -->
                <section class="summary-card">
                    <h2>Исходный текст</h2>
                    <!-- nl2br преобразует \n в <br>, h() экранирует спецсимволы -->
                    <p><?php echo nl2br(h($text)); ?></p>
                </section>

                <!-- Таблица с общими показателями -->
                <div class="summary-grid">
                    <div class="summary-card">
                        <h2>Основные показатели</h2>
                        <table class="result-table">
                            <tbody>
                                <tr><th>Количество символов (с пробелами)</th><td><?php echo h($stats['char_total']); ?></td></tr>
                                <tr><th>Количество букв</th><td><?php echo h($stats['letters']); ?></td></tr>
                                <tr><th>Количество строчных букв</th><td><?php echo h($stats['lowercase']); ?></td></tr>
                                <tr><th>Количество заглавных букв</th><td><?php echo h($stats['uppercase']); ?></td></tr>
                                <tr><th>Количество знаков препинания</th><td><?php echo h($stats['punctuation']); ?></td></tr>
                                <tr><th>Количество цифр</th><td><?php echo h($stats['digits']); ?></td></tr>
                                <tr><th>Количество слов</th><td><?php echo h($stats['words_total']); ?></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Две таблицы рядом: символы и слова -->
                <div class="summary-grid">
                    <!-- Таблица частот символов -->
                    <section class="summary-card">
                        <h2>Вхождения символов</h2>
                        <table class="result-table">
                            <thead>
                                <tr>
                                    <th>Символ</th>
                                    <th>Количество</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Перебираем массив $stats['symbols'], ключ — символ, значение — сколько раз встретился -->
                                <?php foreach ($stats['symbols'] as $symbol => $count): ?>
                                    <tr>
                                        <!-- describeChar заменяет непечатные символы на их описание -->
                                        <td><?php echo h(describeChar($symbol)); ?></td>
                                        <td><?php echo h($count); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>

                    <!-- Таблица частот слов -->
                    <section class="summary-card">
                        <h2>Список слов</h2>
                        <table class="result-table">
                            <thead>
                                <tr>
                                    <th>Слово</th>
                                    <th>Количество</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Перебираем массив $stats['words'], ключ — слово, значение — сколько раз встретилось -->
                                <?php foreach ($stats['words'] as $word => $count): ?>
                                    <tr>
                                        <td><?php echo h($word); ?></td>
                                        <td><?php echo h($count); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </section>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>