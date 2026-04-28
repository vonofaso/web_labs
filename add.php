<?php
/**
 * Модуль добавления новой записи
 */

if (!defined('APP_BOOTSTRAPPED')) {
    exit('Прямой доступ к модулю запрещен.');
}

/**
 * Рендер страницы добавления контакта
 */
function render_add(): string
{
    $message = '';        // Текст сообщения пользователю
    $messageClass = '';   // CSS-класс сообщения (success/error)
    $contact = empty_contact(); // Заготовка пустых полей формы
    
    // Если пришёл POST-запрос на адрес ?p=add — обрабатываем форму
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['p'] ?? '') === 'add') {
        // Валидируем и очищаем входные данные
        [$contact, $errors] = validate_contact_input($_POST);
        
        if (!empty($errors)) {
            // Есть ошибки — показываем их, форма остаётся заполненной
            $message = implode(' ', $errors);
            $messageClass = 'error';
        } else {
            // Ошибок нет — проставляем дату добавления и сохраняем
            $contact['added_at'] = date('Y-m-d H:i:s');
            $success = add_contact($contact);
            
            // Формируем сообщение о результате
            $message = $success ? 'Запись успешно добавлена.' : 'Ошибка: запись не добавлена.';
            $messageClass = $success ? 'success' : 'error';
            
            if ($success) {
                // При успехе очищаем форму для следующего ввода
                $contact = empty_contact();
            }
        }
    }
    
    // Буферизация вывода для захвата HTML
    ob_start();
    ?>
    <section class="content-card">
        <h2>Добавление записи</h2>
        
        <?php if ($message !== ''): ?>
            <p class="status <?= $messageClass; ?>"><?= h($message); ?></p>
        <?php endif; ?>
        
        <?= render_contact_form('add', $contact, 'Добавить запись'); ?>
    </section>
    <?php
    return ob_get_clean(); // Возвращаем захваченный HTML
}