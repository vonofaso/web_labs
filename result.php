<?php
date_default_timezone_set('Europe/Moscow');

// Простая защита от XSS
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

// Форматирование числа: убираем лишние нули
function fmt($v) {
    $f = (float)$v;
    return ($f == (int)$f) ? (string)(int)$f : rtrim(rtrim(number_format($f, 6, '.', ''), '0'), '.');
}

// Простая проверка: число ли строка (без регулярных выражений)
function isNum($s) {
    $s = trim(str_replace(',', '.', $s));
    if ($s === '' || $s === '-' || $s === '.') return false;
    $p = strpos($s, '.');
    $m = ($s[0] === '-') ? 1 : 0;
    if ($p !== false) {
        $int = substr($s, $m, $p - $m);
        $dec = substr($s, $p + 1);
        return ctype_digit($int) && ctype_digit($dec);
    }
    return ctype_digit(substr($s, $m));
}

// Получаем элементы из формы (без регулярных выражений)
function getElements() {
    $res = [];
    foreach ($_POST as $k => $v) {
        if (strpos($k, 'element') === 0) {
            $idx = (int)substr($k, 7);
            $res[$idx] = trim((string)$v);
        }
    }
    ksort($res);
    return array_values($res);
}

// Название алгоритма
function algoName($a) {
    $n = ['selection'=>'Сортировка выбором','bubble'=>'Пузырьковая','shell'=>'Шелла','gnome'=>'Гнома','quick'=>'Быстрая','native'=>'Встроенная PHP'];
    return $n[$a] ?? 'Неизвестный';
}

// Добавление записи в лог
function logAdd(&$logs, &$cnt, $arr, $note) {
    $cnt++;
    $state = [];
    foreach ($arr as $i => $val) $state[] = "$i: ".fmt($val);
    $logs[] = ['step'=>$cnt, 'state'=>implode(' | ', $state), 'note'=>$note];
}

// === СОРТИРОВКИ (упрощённые) ===

function selectionSort($arr) {
    $logs=[]; $cnt=0; $n=count($arr);
    if ($n<=1) { logAdd($logs,$cnt,$arr,'Уже отсортирован'); return [$arr,$logs,$cnt]; }
    for ($i=0;$i<$n-1;$i++) {
        $min=$i;
        for ($j=$i+1;$j<$n;$j++) if ($arr[$j]<$arr[$min]) $min=$j;
        if ($min!=$i) {
            $t=$arr[$i]; $arr[$i]=$arr[$min]; $arr[$min]=$t;
            logAdd($logs,$cnt,$arr,"Обмен $i и $min");
        }
    }
    return [$arr,$logs,$cnt];
}

function bubbleSort($arr) {
    $logs=[]; $cnt=0; $n=count($arr);
    if ($n<=1) { logAdd($logs,$cnt,$arr,'Уже отсортирован'); return [$arr,$logs,$cnt]; }
    for ($i=0;$i<$n-1;$i++) {
        $swapped=false;
        for ($j=0;$j<$n-$i-1;$j++) {
            if ($arr[$j]>$arr[$j+1]) {
                $t=$arr[$j]; $arr[$j]=$arr[$j+1]; $arr[$j+1]=$t;
                $swapped=true;
                logAdd($logs,$cnt,$arr,"Обмен $j и ".($j+1));
            }
        }
        if (!$swapped) break;
    }
    return [$arr,$logs,$cnt];
}

function shellSort($arr) {
    $logs=[]; $cnt=0; $n=count($arr);
    if ($n<=1) { logAdd($logs,$cnt,$arr,'Уже отсортирован'); return [$arr,$logs,$cnt]; }
    for ($gap=intval($n/2);$gap>0;$gap=intval($gap/2)) {
        for ($i=$gap;$i<$n;$i++) {
            $tmp=$arr[$i]; $j=$i;
            while ($j>=$gap && $arr[$j-$gap]>$tmp) {
                $arr[$j]=$arr[$j-$gap]; $j-=$gap;
            }
            $arr[$j]=$tmp;
            logAdd($logs,$cnt,$arr,"Вставка при шаге $gap");
        }
    }
    return [$arr,$logs,$cnt];
}

function gnomeSort($arr) {
    $logs=[]; $cnt=0; $n=count($arr);
    if ($n<=1) { logAdd($logs,$cnt,$arr,'Уже отсортирован'); return [$arr,$logs,$cnt]; }
    $i=0;
    while ($i<$n) {
        if ($i==0 || $arr[$i]>=$arr[$i-1]) { $i++; }
        else { $t=$arr[$i]; $arr[$i]=$arr[$i-1]; $arr[$i-1]=$t; logAdd($logs,$cnt,$arr,"Обмен ".($i-1)." и $i"); $i--; }
    }
    return [$arr,$logs,$cnt];
}

function quickSort($arr) {
    $logs=[]; $cnt=0;
    function qs(&$a,$l,$r,&$logs,&$cnt) {
        if ($l>=$r) return;
        $i=$l; $j=$r; $p=$a[intval(($l+$r)/2)];
        while ($i<=$j) {
            while ($a[$i]<$p) $i++;
            while ($a[$j]>$p) $j--;
            if ($i<=$j) { $t=$a[$i]; $a[$i]=$a[$j]; $a[$j]=$t; logAdd($logs,$cnt,$a,"Разделение по $p"); $i++; $j--; }
        }
        if ($l<$j) qs($a,$l,$j,$logs,$cnt);
        if ($i<$r) qs($a,$i,$r,$logs,$cnt);
    }
    if (count($arr)<=1) { logAdd($logs,$cnt,$arr,'Уже отсортирован'); return [$arr,$logs,$cnt]; }
    qs($arr,0,count($arr)-1,$logs,$cnt);
    return [$arr,$logs,$cnt];
}

function nativeSort($arr) {
    $logs=[]; $cnt=0;
    logAdd($logs,$cnt,$arr,'До сортировки');
    sort($arr,SORT_NUMERIC);
    logAdd($logs,$cnt,$arr,'После сортировки');
    return [$arr,$logs,$cnt];
}

// === ОБРАБОТКА ===
$algo = $_POST['algorithm'] ?? 'selection';
$raw = getElements();
$ok = count($raw)>0; $errs=[]; $norm=[];

if ($ok) {
    foreach ($raw as $idx=>$val) {
        if ($val==='') { $errs[]="Пустой элемент $idx"; continue; }
        if (!isNum($val)) { $errs[]="Не число: $val"; continue; }
        $norm[] = (float)str_replace(',','.',$val);
    }
    if ($errs) $ok=false;
}

$logs=[]; $sorted=[]; $iters=0; $time=0;

if ($ok) {
    $start=microtime(true);
    switch($algo) {
        case 'bubble': list($sorted,$logs,$iters)=bubbleSort($norm); break;
        case 'shell': list($sorted,$logs,$iters)=shellSort($norm); break;
        case 'gnome': list($sorted,$logs,$iters)=gnomeSort($norm); break;
        case 'quick': list($sorted,$logs,$iters)=quickSort($norm); break;
        case 'native': list($sorted,$logs,$iters)=nativeSort($norm); break;
        default: list($sorted,$logs,$iters)=selectionSort($norm);
    }
    $time=microtime(true)-$start;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Результат сортировки</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="page">
    <header class="page-header">
        <h1>Протокол: <?php echo h(algoName($algo)); ?></h1>
        <a class="back-link" href="index.php">Назад</a>
    </header>
    <main class="panel">
        <section class="summary-card">
            <h2>Входные данные</h2>
            <p><?php echo $ok ? h(implode(', ',$raw)) : 'Нет данных'; ?></p>
        </section>
        <section class="summary-card">
            <h2>Проверка</h2>
            <p class="<?php echo $ok?'success':'error'; ?>">
                <?php echo h($ok ? 'OK' : implode(' ',$errs)); ?>
            </p>
        </section>
        <?php if ($ok): ?>
        <section class="summary-card">
            <h2>Ход алгоритма</h2>
            <table class="result-table">
                <thead><tr><th>Шаг</th><th>Массив</th><th>Действие</th></tr></thead>
                <tbody>
                <?php foreach($logs as $l): ?>
                    <tr><td><?php echo h($l['step']); ?></td><td><?php echo h($l['state']); ?></td><td><?php echo h($l['note']); ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        <section class="summary-card">
            <h2>Результат</h2>
            <p><strong>Отсортировано:</strong> <?php echo h(implode(', ',array_map('fmt',$sorted))); ?></p>
            <p><strong>Итераций:</strong> <?php echo h($iters); ?> | <strong>Время:</strong> <?php echo h(number_format($time,6,'.','')); ?> сек.</p>
        </section>
        <?php endif; ?>
    </main>
</div>
</body>
</html>