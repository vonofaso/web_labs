<?php
/**
 * Модуль редактирования записи
 */

if (!defined('APP_BOOTSTRAPPED')) {
    exit('Прямой доступ к модулю запрещен.');
}

/**
 * Рендер страницы редактирования контакта
 */
function render_edit(): string
{
    // Получаем и сортируем контакты для бокового списка выбора
    $contacts = sort_contacts(read_contacts(), 'last_name');
    
    // Если контактов нет — показываем сообщение и выходим
    if (empty($contacts)) {
        return '<section class="content-card"><h2>Редактирование записи</h2><p>В записной книжке пока нет контактов.</p></section>';
    }
    
    // Определяем выбранный контакт: из URL или первый из списка
    $selectedId = isset($_GET['id']) ? (int) $_GET['id'] : (int) $contacts[0]['id'];
    $selectedContact = get_contact($selectedId);
    
    // Если контакт по ID из URL не найден — берём первый
    if ($selectedContact === null) {
        $selectedContact = $contacts[0];
        $selectedId = (int) $selectedContact['id'];
    }
    
    $message = '';
    $messageClass = '';
    
    // Обработка POST-запроса — пользователь отправил форму редактирования
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_GET['p'] ?? '') === 'edit') {
        $postedId = isset($_GET['id']) ? (int) $_GET['id'] : $selectedId;
        [$candidate, $errors] = validate_contact_input($_POST); // Валидируем новые данные
        $existingContact = get_contact($postedId); // Оригинальный контакт из хранилища
        
        if ($existingContact === null) {
            // Контакт был удалён другим пользователем (?)
            $message = 'Запись для редактирования не найдена.';
            $messageClass = 'error';
        } elseif (!empty($errors)) {
            // Ошибки валидации — показываем их, форму оставляем заполненной
            $message = implode(' ', $errors);
            $messageClass = 'error';
            
            // Сохраняем ID и дату, чтобы не потерять служебные поля
            $candidate['id'] = $existingContact['id'];
            $candidate['added_at'] = $existingContact['added_at'];
            $selectedContact = $candidate;
            $selectedId = $postedId;
        } else {
            // Валидация пройдена — обновляем контакт
            $candidate['id'] = $existingContact['id'];
            $candidate['added_at'] = $existingContact['added_at'];
            
            $success = update_contact($postedId, $candidate);
            $message = $success ? 'Запись успешно обновлена.' : 'Ошибка: запись не обновлена.';
            $messageClass = $success ? 'success' : 'error';
            
            // Подгружаем актуальную версию из хранилища
            $selectedContact = get_contact($postedId) ?: $candidate;
            $selectedId = $postedId;
        }
    }
    
    ob_start();
    ?>
    <section class="content-card">
        <h2>Редактирование записи</h2>
        
        <!-- Список контактов для выбора (переключение между записями) -->
        <div class="record-list">
            <?php foreach ($contacts as $contact): ?>
                <a href="?p=edit&id=<?= (int) $contact['id']; ?>" class="<?= ((int) $contact['id'] === $selectedId) ? 'active' : ''; ?>">
                    <?= h(full_name($contact)); ?>
                </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Сообщение о результате операции -->
        <?php if ($message !== ''): ?>
            <p class="status <?= $messageClass; ?>"><?= h($message); ?></p>
        <?php endif; ?>
        
        <!-- Форма редактирования, action содержит id редактируемого контакта -->
        <?= render_contact_form('edit&id=' . $selectedId, $selectedContact, 'Сохранить изменения'); ?>
    </section>
    <?php
    return ob_get_clean();
}