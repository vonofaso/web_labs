<?php
/**
 * Главный файл приложения
 * Точка входа, маршрутизация и подключение модулей
 */

date_default_timezone_set('Europe/Moscow'); // Часовой пояс для дат
define('APP_BOOTSTRAPPED', true);           // Флаг для запрета прямого доступа к модулям

// Белые списки допустимых параметров (защита от невалидных значений)
$allowedPages = ['view', 'add', 'edit', 'delete'];
$allowedSorts = ['added', 'last_name', 'birth_date'];

// Извлекаем параметры из URL с проверкой допустимости
$page = $_GET['p'] ?? 'view';
if (!in_array($page, $allowedPages, true)) {
    $page = 'view'; // Если параметр не из списка — по умолчанию просмотр
}

$sort = $_GET['sort'] ?? 'added';
if (!in_array($sort, $allowedSorts, true)) {
    $sort = 'added'; // По умолчанию сортировка по добавлению
}

// Номер страницы для пагинации (не может быть отрицательным)
$pageNumber = isset($_GET['page']) ? max(0, (int) $_GET['page']) : 0;

// Подключаем общие модули (хранилище и меню нужны всем страницам)
require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/menu.php';

// Маршрутизация: подключаем нужный модуль и рендерим его
switch ($page) {
    case 'add':
        require_once __DIR__ . '/add.php';
        $content = render_add();
        break;
    case 'edit':
        require_once __DIR__ . '/edit.php';
        $content = render_edit();
        break;
    case 'delete':
        require_once __DIR__ . '/delete.php';
        $content = render_delete();
        break;
    case 'view':
    default:
        require_once __DIR__ . '/viewer.php';
        $content = render_viewer($sort, $pageNumber);
        break;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР9. Записная книжка</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <header class="page-header">
            <div>
                <h1>ЛР9. Записная книжка</h1>
                <p>Модульная PHP-структура с просмотром, добавлением, редактированием и удалением записей.</p>
            </div>
            <div class="header-meta">
                <!-- Показываем, какое хранилище активно -->
                <span>Хранилище: <?= h(storage_mode() === 'sqlite' ? 'SQLite' : 'JSON'); ?></span>
                <span><?= date('d.m.Y H:i:s'); ?></span>
            </div>
        </header>

        <?= render_menu($page, $sort); ?> <!-- Навигационное меню -->
        <?= $content; ?>                   <!-- Контент выбранной страницы -->
    </div>
</body>
</html>