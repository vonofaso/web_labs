<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    
    <?php $page_title = "Главная страница - Охрана природы"; ?>
    <title><?php echo $page_title; ?></title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <div class="header-content">
        <h2>Охрана природы</h2>
        <ul class="menu">
            <li>
                <a href="<?php
                    $name='Главная';
                    $link='index.php';
                    $current_page=true;
                    echo $link;
                ?>"<?php
                    if($current_page) echo ' class="selected-menu"';
                ?>><?php echo $name; ?></a>
            </li>
            <li>
                <a href="<?php
                    $name='О нас';
                    $link='page1.php';
                    $current_page=false;
                    echo $link;
                ?>"<?php
                    if($current_page) echo ' class="selected-menu"';
                ?>><?php echo $name; ?></a>
            </li>
            <li>
                <a href="<?php
                    $name='Услуги';
                    $link='page2.php';
                    $current_page=false;
                    echo $link;
                ?>"<?php
                    if($current_page) echo ' class="selected-menu"';
                ?>><?php echo $name; ?></a>
            </li>
        </ul>
    </div>
</div>

<div class="content">
    <h1>Добро пожаловать на сайт об охране природы</h1>
    
    <p>Природа — это всё, что нас окружает: леса, реки, озёра, горы, животные и растения. Сохранить это богатство для будущих поколений — наша общая задача. На этом сайте мы рассказываем о том, как каждый человек может внести свой вклад в защиту окружающей среды.</p>
    
    <h2>Почему это важно?</h2>
    <p>Ежегодно на планете исчезают тысячи видов растений и животных. Загрязнение воздуха и воды приводит к ухудшению здоровья людей и изменению климата. Экологические проблемы касаются каждого жителя Земли, независимо от возраста и профессии.</p>
    
    <h2>Фотография природы</h2>
    <div class="image-block">
    <?php echo '<img src="img/foto' . (date('s') % 2 == 0 ? '1' : '2') . '.jpg" alt="Услуги" class="nature-photo">'; ?>
        <p><i>Фотография обновляется каждую секунду (проверьте чётность секунды: сейчас <?php echo date('s'); ?> сек.)</i></p>
    </div>
    
    <h2>Сравнение экологических проблем</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Проблема</th>
            <th>Причина</th>
            <th>Решение</th>
        </tr>
        <?php
        echo '<tr>';
        echo '<td>Загрязнение воздуха</td>';
        echo '<td>Выхлопные газы, заводы</td>';
        echo '<td>Электромобили, фильтры</td>';
        echo '</tr>';
        ?>
        <tr>
            <td><?php echo 'Вырубка лесов'; ?></td>
            <td><?php echo 'Древесина, стройка'; ?></td>
            <td><?php echo 'Посадка новых деревьев'; ?></td>
        </tr>
    </table>
    
    <h2>Ещё одна фотография</h2>
    <div class="image-block">
        <img src="img/foto1.jpg" alt="Статичная фотография природы" class="nature-photo">
    </div>
    
    <h2>Как помочь природе?</h2>
    <p>1. Сортируйте мусор и сдавайте вторсырьё на переработку.</p>
    <p>2. Экономьте воду и электричество.</p>
    <p>3. Не покупайте лишние вещи — производство наносит вред экологии.</p>
    <p>4. Участвуйте в субботниках и посадке деревьев.</p>
    <p>5. Рассказывайте друзьям о важности экологичного образа жизни.</p>
</div>

<div class="footer">
    <div class="footer-content">
        <p>Сафонов Александр Олегович, группа 241-352</p>
        <p>Лабораторная работа № А-1: Простейшая программа на PHP. Конвертация статического контента в динамический.</p>
        <?php
        date_default_timezone_set(timezoneId: 'Europe/Moscow');
        $current_date = date('d.m.Y');
        $current_time = date('H-i-s');
        echo '<p>Сформировано ' . $current_date . ' в ' . $current_time . '</p>';
        ?>
    </div>
</div>

</body>
</html>