<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <?php $page_title = "О нас - Охрана природы"; ?>
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
                    $current_page=false;
                    echo $link;
                ?>"<?php
                    if($current_page) echo ' class="selected-menu"';
                ?>><?php echo $name; ?></a>
            </li>
            <li>
                <a href="<?php
                    $name='О нас';
                    $link='page1.php';
                    $current_page=true;
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
    <h1>О нашем проекте</h1>
    
    <p>Мы — команда энтузиастов, которые верят, что даже небольшие действия каждого человека могут изменить мир. Наш проект создан для того, чтобы делиться знаниями об экологии и вдохновлять людей на заботу об окружающей среде.</p>
    
    <h2>Наша миссия</h2>
    <p>Сделать экологичный образ жизни доступным и понятным для каждого. Мы пишем статьи, снимаем видео и организуем мероприятия по уборке территорий и посадке деревьев.</p>
    
    <div class="image-block">
<?php echo '<img src="img/foto' . (date('s') % 2 == 0 ? '1' : '2') . '.jpg" alt="Услуги" class="nature-photo">'; ?>    </div>
    
    <h2>Наша команда</h2>
    <ul>
        <li>Александра — экоактивист, организатор субботников</li>
        <li>Дмитрий — биолог, эксперт по редким видам</li>
        <li>Елена — дизайнер, создаёт экологичную упаковку</li>
    </ul>
    
    <h2>Достижения за год</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>Мероприятие</th>
            <th>Участников</th>
            <th>Результат</th>
        </tr>
        <?php
        echo '<tr>';
        echo '<td>Субботник в парке</td>';
        echo '<td>50 человек</td>';
        echo '<td>Собрано 200 кг мусора</td>';
        echo '</tr>';
        ?>
        <tr>
            <td><?php echo 'Посадка деревьев'; ?></td>
            <td><?php echo '30 человек'; ?></td>
            <td><?php echo '150 саженцев'; ?></td>
        </tr>
    </table>
</div>

<div class="footer">
    <div class="footer-content">
        <p>Сафонов Александр Олегович, группа 241-352</p>
        <p>Лабораторная работа № А-1: Простейшая программа на PHP. Конвертация статического контента в динамический.</p>
    <?php date_default_timezone_set(timezoneId: 'Europe/Moscow'); echo '<p>Сформировано ' . date('d.m.Y') . ' в ' . date('H-i-s') . '</p>'; ?>
    </div>
</div>

</body>
</html>