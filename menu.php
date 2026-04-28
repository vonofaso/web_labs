<?php
/**
 * Модуль генерации меню навигации
 */

if (!defined('APP_BOOTSTRAPPED')) {
    exit('Прямой доступ к модулю запрещен.');
}

/**
 * Рендер главного меню и подменю сортировки
 * 
 * @param string $currentPage Текущая страница (view/add/edit/delete)
 * @param string $currentSort Текущий режим сортировки (added/last_name/birth_date)
 * @return string HTML-код меню
 */
function render_menu(string $currentPage, string $currentSort): string
{
    // Пункты главного меню
    $mainItems = [
        'view'   => 'Просмотр',
        'add'    => 'Добавление записи',
        'edit'   => 'Редактирование записи',
        'delete' => 'Удаление записи',
    ];
    
    // Варианты сортировки (показываются только на странице просмотра)
    $sortItems = [
        'added'      => 'По добавлению',
        'last_name'  => 'По фамилии',
        'birth_date' => 'По дате рождения',
    ];
    
    // Собираем главное меню: перебираем пункты, активный выделяем классом
    $html = '<nav class="main-menu">';
    foreach ($mainItems as $key => $label) {
        $activeClass = ($currentPage === $key) ? ' class="active"' : '';
        $html .= '<a href="?p=' . $key . '"' . $activeClass . '>' . h($label) . '</a>';
    }
    $html .= '</nav>';
    
    // Подменю сортировки — показываем только на странице просмотра
    if ($currentPage === 'view') {
        $html .= '<nav class="sub-menu">';
        foreach ($sortItems as $key => $label) {
            $activeClass = ($currentSort === $key) ? ' class="active"' : '';
            $html .= '<a href="?p=view&sort=' . $key . '"' . $activeClass . '>' . h($label) . '</a>';
        }
        $html .= '</nav>';
    }
    
    return $html;
}