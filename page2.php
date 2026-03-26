<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <?php $page_title = "Услуги - Охрана природы"; ?>
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
                    $current_page=true;
                    echo $link;
                ?>"<?php
                    if($current_page) echo ' class="selected-menu"';
                ?>><?php echo $name; ?></a>
            </li>
        </ul>
    </div>
</div>

<div class="content">
    <h1>Наши услуги</h1>
    
    <p>Мы предлагаем образовательные и консультационные услуги для школ, компаний и частных лиц.</p>
    
    <h2>Для школ</h2>
    <p>Эко-уроки, квесты и мастер-классы для детей всех возрастов.</p>
    
    <div class="image-block">
<?php echo '<img src="img/foto' . (date('s') % 2 == 0 ? '1' : '2') . '.jpg" alt="Услуги" class="nature-photo">'; ?>    </div>
    
    <h2>Для бизнеса</h2>
    <p>Аудит экологичности офиса, внедрение раздельного сбора отходов, зелёный PR.</p>
    
    <h2>Стоимость услуг</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>Услуга</th>
            <th>Длительность</th>
            <th>Цена</th>
        </tr>
        <?php
        echo '<tr>';
        echo '<td>Эко-урок в школе</td>';
        echo '<td>45 минут</td>';
        echo '<td>3000 руб.</td>';
        echo '</tr>';
        ?>
        <tr>
            <td><?php echo 'Консультация для бизнеса'; ?></td>
            <td><?php echo '1 час'; ?></td>
            <td><?php echo '5000 руб.'; ?></td>
        </tr>
    </table>
    
    <h2>Контакты</h2>
    <p>Email: info@lab.ru</p>
    <p>Телефон: +71234567890</p>
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