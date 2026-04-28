<?php

date_default_timezone_set('Europe/Moscow');
session_start();

// ============================================
// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// ============================================

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Форматирование числа: убирает лишние нули и десятичную точку, если они не нужны
 */
function formatNumber($value)
{
    // Если число целое (например, 5.0), возвращаем как целое
    if ((float) ((int) $value) === (float) $value) {
        return (string) ((int) $value);
    }
    
    // Иначе форматируем с плавающей точкой, удаляя лишние нули в конце
    return rtrim(rtrim(number_format((float) $value, 10, '.', ''), '0'), '.');
}

// ============================================
// ПАРСЕР АРИФМЕТИЧЕСКИХ ВЫРАЖЕНИЙ
// ============================================

/**
 * Пропуск пробельных символов в выражении
 */
function skipSpaces($expression, &$position)
{
    $length = strlen($expression);
    while ($position < $length && ctype_space($expression[$position])) {
        $position++;
    }
}

/**
 * Парсинг числа из выражения
 */
function parseNumber($expression, &$position)
{
    skipSpaces($expression, $position);
    $length = strlen($expression);
    $start = $position;
    $hasDigits = false;
    $hasPoint = false;

    // Считываем цифры и десятичную точку
    while ($position < $length) {
        $char = $expression[$position];
        
        if ($char >= '0' && $char <= '9') {
            $hasDigits = true;
            $position++;
            continue;
        }
        
        if ($char === '.') {
            if ($hasPoint) {
                throw new RuntimeException('Неверная запись числа: обнаружено несколько десятичных точек.');
            }
            $hasPoint = true;
            $position++;
            continue;
        }
        
        break;
    }

    $number = substr($expression, $start, $position - $start);
    
    // Проверки корректности числа
    if (!$hasDigits || $number === '' || $number === '.') {
        throw new RuntimeException('Ожидалось число.');
    }
    
    if ($number[0] === '.' || substr($number, -1) === '.') {
        throw new RuntimeException('Число не может начинаться или заканчиваться точкой.');
    }
    
    return (float) $number;
}

/**
 * Парсинг множителя (Factor)
 * Обрабатывает числа, выражения в скобках и унарные +/-
 */
function parseFactor($expression, &$position)
{
    skipSpaces($expression, $position);
    $length = strlen($expression);

    if ($position >= $length) {
        throw new RuntimeException('Выражение неожиданно закончилось.');
    }

    $char = $expression[$position];

    // Обработка унарного плюса
    if ($char === '+') {
        $position++;
        return parseFactor($expression, $position);
    }

    // Обработка унарного минуса
    if ($char === '-') {
        $position++;
        return -parseFactor($expression, $position);
    }

    // Обработка выражения в скобках
    if ($char === '(') {
        $position++;
        $value = parseExpression($expression, $position);
        skipSpaces($expression, $position);
        
        if ($position >= $length || $expression[$position] !== ')') {
            throw new RuntimeException('Не найдена закрывающая скобка.');
        }
        
        $position++;
        return $value;
    }

    // Иначе это должно быть число
    return parseNumber($expression, $position);
}

/**
 * Парсинг терма (Term)
 * Обрабатывает операции умножения и деления (приоритет 2)
 */
function parseTerm($expression, &$position)
{
    $value = parseFactor($expression, $position);
    $length = strlen($expression);

    while (true) {
        skipSpaces($expression, $position);
        
        if ($position >= $length) {
            return $value;
        }

        $char = $expression[$position];
        
        // Проверяем, является ли символ оператором умножения/деления
        if ($char !== '*' && $char !== '/' && $char !== ':') {
            return $value;
        }

        $position++;
        $right = parseFactor($expression, $position);

        // Проверка деления на ноль
        if (($char === '/' || $char === ':') && abs($right) < 1e-12) {
            throw new RuntimeException('Деление на ноль невозможно.');
        }

        // Выполняем операцию
        if ($char === '*') {
            $value *= $right;
        } else {
            $value /= $right;
        }
    }
}

/**
 * Парсинг выражения (Expression)
 * Обрабатывает операции сложения и вычитания (приоритет 1)
 */
function parseExpression($expression, &$position)
{
    $value = parseTerm($expression, $position);
    $length = strlen($expression);

    while (true) {
        skipSpaces($expression, $position);
        
        if ($position >= $length) {
            return $value;
        }

        $char = $expression[$position];
        
        // Проверяем, является ли символ оператором сложения/вычитания
        if ($char !== '+' && $char !== '-') {
            return $value;
        }

        $position++;
        $right = parseTerm($expression, $position);

        // Выполняем операцию
        if ($char === '+') {
            $value += $right;
        } else {
            $value -= $right;
        }
    }
}

/**
 * Основная функция вычисления арифметического выражения
 */
function calculateExpression($expression)
{
    // Заменяем запятую на точку (поддержка десятичных дробей в русской нотации)
    $expression = trim(str_replace(',', '.', $expression));

    if ($expression === '') {
        throw new RuntimeException('Выражение не задано.');
    }

    // Валидация допустимых символов
    if (!preg_match('/^[0-9+\-*\/:().\s]+$/', $expression)) {
        throw new RuntimeException('В выражении присутствуют недопустимые символы.');
    }

    // Парсинг и вычисление
    $position = 0;
    $result = parseExpression($expression, $position);
    
    // Проверяем, что всё выражение полностью распарсено
    skipSpaces($expression, $position);
    if ($position !== strlen($expression)) {
        throw new RuntimeException('Нарушена структура арифметического выражения.');
    }

    return $result;
}

// ============================================
// УПРАВЛЕНИЕ СЕССИЕЙ И ИСТОРИЕЙ
// ============================================

// Инициализация истории в сессии (если ещё не создана)
if (!isset($_SESSION['history']) || !is_array($_SESSION['history'])) {
    $_SESSION['history'] = array();
}

// Если есть отложенный результат (переносим в историю)
if (isset($_SESSION['pending'])) {
    array_unshift($_SESSION['history'], $_SESSION['pending']);
    $_SESSION['history'] = array_slice($_SESSION['history'], 0, 15); // Храним не более 15 записей
    unset($_SESSION['pending']);
}

// ============================================
// ОБРАБОТКА POST-ЗАПРОСА
// ============================================

$expression = '';
$currentResult = null;
$currentClass = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expression = trim((string) ($_POST['val'] ?? ''));

    try {
        $value = calculateExpression($expression);
        $currentResult = 'Значение выражения: ' . formatNumber($value);
        $currentClass = 'success';
    } catch (Throwable $e) {
        $currentResult = 'Ошибка вычисления выражения: ' . $e->getMessage();
        $currentClass = 'error';
    }

    // Сохраняем результат для следующего запроса (чтобы показать в истории)
    $_SESSION['pending'] = array(
        'expression' => $expression,
        'result' => $currentResult
    );
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР10. Калькулятор</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <header class="page-header">
            <h1>ЛР10. Калькулятор</h1>
            <p>Поддерживаются целые числа, десятичные дроби, операции <code>+</code>, <code>-</code>, <code>*</code>, <code>/</code>, <code>:</code> и скобки.</p>
        </header>

        <main class="content-card">
            <!-- Блок с результатом вычисления -->
            <?php if ($currentResult !== null): ?>
                <div class="status <?= h($currentClass); ?>">
                    <?= h($currentResult); ?>
                </div>
            <?php endif; ?>

            <!-- Форма ввода выражения -->
            <form method="post" action="" class="contact-form">
                <label>
                <div class="form-row">
                    <span>Введите выражение</span>
                </div>
                    <input type="text" name="val" value="<?= h($expression); ?>" placeholder="Например: (12.5-2)*3+7/2">
                </label>
                <button type="submit" class="submit-button">Вычислить</button>
            </form>
        </main>

        <!-- Блок истории вычислений -->
        <aside class="content-card history-panel">
            <h2>История вычислений</h2>
            
            <?php if (empty($_SESSION['history'])): ?>
                <p>История пока пуста.</p>
            <?php else: ?>
                <div class="history-list">
                    <?php foreach ($_SESSION['history'] as $item): ?>
                        <div class="history-item">
                            <strong><?= h($item['expression']); ?></strong>
                            <span><?= h($item['result']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </aside>
    </div>
</body>
</html>