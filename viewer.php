<?php
/**
 * Модуль просмотра записей
 * Реализует табличный вывод с сортировкой и пагинацией
 */

if (!defined('APP_BOOTSTRAPPED')) {
    exit('Прямой доступ к модулю запрещен.');
}

/**
 * Рендер страницы просмотра контактов
 * 
 * @param string $sort Режим сортировки
 * @param int $page Номер текущей страницы
 * @return string HTML-код страницы
 */
function render_viewer(string $sort, int $page): string
{
    // Читаем все контакты и сортируем согласно выбранному режиму
    $contacts = sort_contacts(read_contacts(), $sort);
    
    // Параметры пагинации: по 10 записей на страницу
    $perPage = 10;
    $total = count($contacts);                           // Всего записей
    $totalPages = max(1, (int) ceil($total / $perPage)); // Всего страниц (минимум 1)
    $currentPage = max(0, min($page, $totalPages - 1));  // Текущая страница в допустимом диапазоне
    $currentContacts = array_slice($contacts, $currentPage * $perPage, $perPage); // Записи для текущей страницы
    
    // Названия сортировки для вывода пользователю
    $sortTitles = [
        'added'      => 'по порядку добавления',
        'last_name'  => 'по фамилии',
        'birth_date' => 'по дате рождения',
    ];
    
    ob_start();
    ?>
    <section class="content-card">
        <h2>Содержимое записной книжки</h2>
        <p>Текущий режим сортировки: <strong><?= h($sortTitles[$sort] ?? $sortTitles['added']); ?></strong>.</p>
        
        <?php if (empty($currentContacts)): ?>
            <p>Записей пока нет.</p>
        <?php else: ?>
            <!-- Таблица с контактами -->
            <table class="contacts-table">
                <thead>
                    <tr>
                        <th>ФИО</th>
                        <th>Пол</th>
                        <th>Дата рождения</th>
                        <th>Телефон</th>
                        <th>Адрес</th>
                        <th>E-mail</th>
                        <th>Комментарий</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currentContacts as $contact): ?>
                        <tr>
                            <td><?= h(full_name($contact)); ?></td>
                            <td><?= h($contact['gender']); ?></td>
                            <td><?= h(format_date($contact['birth_date'])); ?></td>
                            <td><?= h($contact['phone']); ?></td>
                            <td><?= h($contact['address']); ?></td>
                            <td><?= h($contact['email']); ?></td>
                            <td><?= h($contact['comment']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- Пагинация: показываем только если страниц больше одной -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 0; $i < $totalPages; $i++): ?>
                        <a href="?p=view&sort=<?= h($sort); ?>&page=<?= $i; ?>" class="<?= $i === $currentPage ? 'active' : ''; ?>">
                            <?= $i + 1; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
    <?php
    return ob_get_clean();
}