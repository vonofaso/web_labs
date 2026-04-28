<?php
/**
 * Модуль удаления записи
 */

if (!defined('APP_BOOTSTRAPPED')) {
    exit('Прямой доступ к модулю запрещен.');
}

/**
 * Рендер страницы удаления контакта
 */
function render_delete(): string
{
    $message = '';        // Текст сообщения пользователю
    $messageClass = '';   // CSS-класс сообщения
    
    // Если передан delete_id — выполняем удаление
    if (isset($_GET['delete_id'])) {
        $contact = get_contact((int) $_GET['delete_id']); // Получаем контакт до удаления (нужен для сообщения)
        
        if ($contact !== null && delete_contact((int) $_GET['delete_id'])) {
            // Успешно удалили
            $message = 'Запись с фамилией "' . h($contact['last_name']) . '" успешно удалена.';
            $messageClass = 'success';
        } else {
            // Контакт не найден или ошибка удаления
            $message = 'Ошибка: запись не удалена.';
            $messageClass = 'error';
        }
    }
    
    // Получаем список для отображения (сортируем по фамилии для удобства поиска)
    $contacts = sort_contacts(read_contacts(), 'last_name');
    
    ob_start();
    ?>
    <section class="content-card">
        <h2>Удаление записи</h2>
        
        <?php if ($message !== ''): ?>
            <p class="status <?= $messageClass; ?>"><?= h($message); ?></p>
        <?php endif; ?>
        
        <?php if (empty($contacts)): ?>
            <p>В записной книжке нет записей для удаления.</p>
        <?php else: ?>
            <p>Нажмите на запись, которую хотите удалить:</p>
            <div class="record-list delete-list">
                <?php foreach ($contacts as $contact): ?>
                    <!-- При клике запрашиваем подтверждение через JS -->
                    <a href="?p=delete&delete_id=<?= (int) $contact['id']; ?>"
                       onclick="return confirm('Удалить запись: <?= h(short_name($contact)); ?>?')">
                        <?= h(short_name($contact)); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}