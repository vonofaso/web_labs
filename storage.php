<?php
/**
 * Модуль хранения данных
 * Поддерживает SQLite как основное хранилище и JSON как резервное
 * Содержит функции для CRUD-операций, валидации, сортировки и форматирования
 */

if (!defined('APP_BOOTSTRAPPED')) {
    exit('Прямой доступ к модулю запрещен.');
}

// ============================================
// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// ============================================

/**
 * Экранирование HTML-символов для безопасного вывода
 * Защита от XSS-атак
 */
function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Получение пути к директории с данными
 * Создаёт директорию, если она не существует
 */
function data_dir(): string
{
    $dir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true); // Рекурсивно создаём с правами 0777
    }
    return $dir;
}

/**
 * Определение доступного режима хранения
 * SQLite используется, если доступно PDO-расширение с драйвером sqlite
 * Иначе откатываемся на JSON
 */
function storage_mode(): string
{
    if (class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers(), true)) {
        return 'sqlite';
    }
    return 'json';
}

// ============================================
// НАЧАЛЬНЫЕ ДАННЫЕ (СИДЫ)
// ============================================

/**
 * Начальный набор контактов для заполнения пустого хранилища
 * Используется при первом запуске приложения
 */
function seed_contacts(): array
{
    return [
        ['id' => 1, 'last_name' => 'Соколов', 'first_name' => 'Денис', 'middle_name' => 'Владимирович', 'gender' => 'Мужской', 'birth_date' => '1995-05-10', 'phone' => '+7 (903) 555-12-34', 'address' => 'Санкт-Петербург, Невский пр-т, 25', 'email' => 'sokolov.denis@example.com', 'comment' => 'Team lead проекта.', 'added_at' => '2026-04-01 09:00:00'],
        ['id' => 2, 'last_name' => 'Волкова', 'first_name' => 'Алина', 'middle_name' => 'Евгеньевна', 'gender' => 'Женский', 'birth_date' => '1997-11-18', 'phone' => '+7 (916) 123-45-67', 'address' => 'Казань, ул. Баумана, 10', 'email' => 'volkova.alina@example.com', 'comment' => 'Frontend-разработчик.', 'added_at' => '2026-04-01 09:15:00'],
        ['id' => 3, 'last_name' => 'Медведев', 'first_name' => 'Артём', 'middle_name' => 'Алексеевич', 'gender' => 'Мужской', 'birth_date' => '1994-08-03', 'phone' => '+7 (925) 987-65-43', 'address' => 'Екатеринбург, пр-т Ленина, 50', 'email' => 'medvedev.artem@example.com', 'comment' => 'Backend-разработчик.', 'added_at' => '2026-04-01 09:30:00'],
        ['id' => 4, 'last_name' => 'Крылова', 'first_name' => 'Елизавета', 'middle_name' => 'Андреевна', 'gender' => 'Женский', 'birth_date' => '2000-12-07', 'phone' => '+7 (985) 111-22-33', 'address' => 'Новосибирск, ул. Советская, 15', 'email' => 'krylova.elizaveta@example.com', 'comment' => 'UI/UX дизайнер.', 'added_at' => '2026-04-01 09:45:00'],
        ['id' => 5, 'last_name' => 'Тихонов', 'first_name' => 'Глеб', 'middle_name' => 'Сергеевич', 'gender' => 'Мужской', 'birth_date' => '1996-03-22', 'phone' => '+7 (977) 444-55-66', 'address' => 'Нижний Новгород, ул. Большая Покровская, 8', 'email' => 'tihonov.gleb@example.com', 'comment' => 'Тестировщик QA.', 'added_at' => '2026-04-01 10:00:00'],
        ['id' => 6, 'last_name' => 'Григорьева', 'first_name' => 'Полина', 'middle_name' => 'Максимовна', 'gender' => 'Женский', 'birth_date' => '2001-09-14', 'phone' => '+7 (926) 777-88-99', 'address' => 'Самара, ул. Ленинградская, 32', 'email' => 'grigorieva.polina@example.com', 'comment' => 'Project manager.', 'added_at' => '2026-04-01 10:15:00'],
        ['id' => 7, 'last_name' => 'Борисов', 'first_name' => 'Матвей', 'middle_name' => 'Дмитриевич', 'gender' => 'Мужской', 'birth_date' => '1998-06-30', 'phone' => '+7 (901) 333-22-11', 'address' => 'Ростов-на-Дону, ул. Пушкинская, 45', 'email' => 'borisov.matvey@example.com', 'comment' => 'DevOps инженер.', 'added_at' => '2026-04-01 10:30:00'],
        ['id' => 8, 'last_name' => 'Давыдова', 'first_name' => 'Ксения', 'middle_name' => 'Ильинична', 'gender' => 'Женский', 'birth_date' => '1999-04-19', 'phone' => '+7 (915) 222-33-44', 'address' => 'Уфа, пр-т Октября, 20', 'email' => 'davydova.kseniya@example.com', 'comment' => 'Аналитик данных.', 'added_at' => '2026-04-01 10:45:00'],
        ['id' => 9, 'last_name' => 'Куликов', 'first_name' => 'Егор', 'middle_name' => 'Николаевич', 'gender' => 'Мужской', 'birth_date' => '1995-10-11', 'phone' => '+7 (909) 666-77-88', 'address' => 'Омск, ул. Ленина, 12', 'email' => 'kulikov.egor@example.com', 'comment' => 'Системный администратор.', 'added_at' => '2026-04-01 11:00:00'],
        ['id' => 10, 'last_name' => 'Беляева', 'first_name' => 'Александра', 'middle_name' => 'Юрьевна', 'gender' => 'Женский', 'birth_date' => '2002-02-28', 'phone' => '+7 (950) 999-00-11', 'address' => 'Красноярск, ул. Мира, 55', 'email' => 'belyaeva.aleksandra@example.com', 'comment' => 'Контент-менеджер.', 'added_at' => '2026-04-01 11:15:00'],
        ['id' => 11, 'last_name' => 'Федотов', 'first_name' => 'Роман', 'middle_name' => 'Васильевич', 'gender' => 'Мужской', 'birth_date' => '1997-07-16', 'phone' => '+7 (964) 444-33-22', 'address' => 'Воронеж, ул. Плехановская, 18', 'email' => 'fedotov.roman@example.com', 'comment' => 'Security специалист.', 'added_at' => '2026-04-01 11:30:00'],
        ['id' => 12, 'last_name' => 'Николаева', 'first_name' => 'Варвара', 'middle_name' => 'Кирилловна', 'gender' => 'Женский', 'birth_date' => '2000-08-25', 'phone' => '+7 (903) 555-66-77', 'address' => 'Пермь, ул. Сибирская, 42', 'email' => 'nikolaeva.varvara@example.com', 'comment' => 'HR и рекрутинг.', 'added_at' => '2026-04-01 11:45:00'],
    ];
}

// ============================================
// РАБОТА С SQLite
// ============================================

/**
 * Путь к файлу базы данных SQLite
 */
function sqlite_path(): string
{
    return data_dir() . DIRECTORY_SEPARATOR . 'contacts.sqlite';
}

/**
 * Получение подключения к SQLite (синглтон)
 * При первом вызове создаёт таблицу и заполняет начальными данными
 */
function sqlite(): PDO
{
    static $pdo = null; // Статическая переменная хранит соединение между вызовами
    
    // Если уже подключены — возвращаем существующее соединение
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    // Создаём новое подключение
    $pdo = new PDO('sqlite:' . sqlite_path());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Бросать исключения при ошибках
    
    // Создаём таблицу, если её ещё нет
    $pdo->exec('
        CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            last_name TEXT NOT NULL,
            first_name TEXT NOT NULL,
            middle_name TEXT NOT NULL,
            gender TEXT NOT NULL,
            birth_date TEXT NOT NULL,
            phone TEXT NOT NULL,
            address TEXT NOT NULL,
            email TEXT NOT NULL,
            comment TEXT NOT NULL,
            added_at TEXT NOT NULL
        )
    ');
    
    // Если таблица пустая — заполняем начальными данными из seed_contacts()
    $count = (int) $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();
    if ($count === 0) {
        $stmt = $pdo->prepare('
            INSERT INTO contacts (last_name, first_name, middle_name, gender, birth_date, phone, address, email, comment, added_at)
            VALUES (:last_name, :first_name, :middle_name, :gender, :birth_date, :phone, :address, :email, :comment, :added_at)
        ');
        
        foreach (seed_contacts() as $contact) {
            $stmt->execute([
                ':last_name'   => $contact['last_name'],
                ':first_name'  => $contact['first_name'],
                ':middle_name' => $contact['middle_name'],
                ':gender'      => $contact['gender'],
                ':birth_date'  => $contact['birth_date'],
                ':phone'       => $contact['phone'],
                ':address'     => $contact['address'],
                ':email'       => $contact['email'],
                ':comment'     => $contact['comment'],
                ':added_at'    => $contact['added_at'],
            ]);
        }
    }

    return $pdo;
}

// ============================================
// РАБОТА С JSON (резервное хранилище)
// ============================================

/**
 * Путь к файлу JSON-хранилища
 */
function json_path(): string
{
    return data_dir() . DIRECTORY_SEPARATOR . 'contacts.json';
}

/**
 * Запись контактов в JSON-файл
 * Использует эксклюзивную блокировку для защиты от одновременной записи
 */
function write_contacts_json(array $contacts): void
{
    file_put_contents(
        json_path(),
        json_encode(array_values($contacts), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), // array_values сбрасывает ключи
        LOCK_EX // Эксклюзивная блокировка на время записи
    );
}

/**
 * Генерация следующего ID для JSON-хранилища
 * Находит максимальный существующий ID и увеличивает на 1
 */
function next_json_id(array $contacts): int
{
    $max = 0;
    foreach ($contacts as $contact) {
        $max = max($max, (int) $contact['id']);
    }
    return $max + 1;
}

// ============================================
// ОСНОВНЫЕ ОПЕРАЦИИ (CRUD)
// ============================================

/**
 * Получение всех контактов
 * Автоматически выбирает источник данных в зависимости от режима хранения
 */
function read_contacts(): array
{
    // SQLite режим — простой SELECT без параметров
    if (storage_mode() === 'sqlite') {
        return sqlite()
            ->query('SELECT * FROM contacts ORDER BY id ASC')
            ->fetchAll(PDO::FETCH_ASSOC); // Возвращаем ассоциативный массив
    }
    
    // JSON режим (резервный)
    $path = json_path();
    
    // Если файла нет — создаём его с начальными данными
    if (!is_file($path)) {
        file_put_contents(
            $path,
            json_encode(seed_contacts(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
            LOCK_EX
        );
    }
    
    // Читаем и декодируем JSON
    $contacts = json_decode((string) file_get_contents($path), true);
    return is_array($contacts) ? $contacts : []; // Защита от битого JSON
}

/**
 * Получение одного контакта по ID
 * Возвращает null, если контакт не найден
 */
function get_contact(int $id): ?array
{
    // Перебираем все контакты (для JSON это необходимо, для SQLite можно было бы сделать точечный запрос)
    foreach (read_contacts() as $contact) {
        if ((int) $contact['id'] === $id) {
            return $contact;
        }
    }
    return null;
}

/**
 * Добавление нового контакта
 * Возвращает true при успехе (для JSON всегда true)
 */
function add_contact(array $contact): bool
{
    // SQLite режим — подготовленный запрос для безопасности
    if (storage_mode() === 'sqlite') {
        return sqlite()
            ->prepare('
                INSERT INTO contacts (last_name, first_name, middle_name, gender, birth_date, phone, address, email, comment, added_at)
                VALUES (:last_name, :first_name, :middle_name, :gender, :birth_date, :phone, :address, :email, :comment, :added_at)
            ')
            ->execute([
                ':last_name'   => $contact['last_name'],
                ':first_name'  => $contact['first_name'],
                ':middle_name' => $contact['middle_name'],
                ':gender'      => $contact['gender'],
                ':birth_date'  => $contact['birth_date'],
                ':phone'       => $contact['phone'],
                ':address'     => $contact['address'],
                ':email'       => $contact['email'],
                ':comment'     => $contact['comment'],
                ':added_at'    => $contact['added_at'],
            ]);
    }
    
    // JSON режим — читаем, добавляем, записываем обратно
    $contacts = read_contacts();
    $contact['id'] = next_json_id($contacts); // Генерируем новый ID
    $contacts[] = $contact;                   // Добавляем в массив
    write_contacts_json($contacts);           // Сохраняем в файл
    
    return true;
}

/**
 * Обновление существующего контакта
 * Возвращает false, если контакт не найден
 */
function update_contact(int $id, array $contact): bool
{
    // SQLite режим — UPDATE с подготовленным запросом
    if (storage_mode() === 'sqlite') {
        return sqlite()
            ->prepare('
                UPDATE contacts
                SET last_name = :last_name,
                    first_name = :first_name,
                    middle_name = :middle_name,
                    gender = :gender,
                    birth_date = :birth_date,
                    phone = :phone,
                    address = :address,
                    email = :email,
                    comment = :comment
                WHERE id = :id
            ')
            ->execute([
                ':last_name'   => $contact['last_name'],
                ':first_name'  => $contact['first_name'],
                ':middle_name' => $contact['middle_name'],
                ':gender'      => $contact['gender'],
                ':birth_date'  => $contact['birth_date'],
                ':phone'       => $contact['phone'],
                ':address'     => $contact['address'],
                ':email'       => $contact['email'],
                ':comment'     => $contact['comment'],
                ':id'          => $id, // WHERE id = :id
            ]);
    }
    
    // JSON режим — ищем контакт по ID и заменяем его
    $contacts = read_contacts();
    foreach ($contacts as &$item) { // &$item — ссылка, чтобы менять элемент напрямую
        if ((int) $item['id'] === $id) {
            $contact['id'] = $item['id'];           // Сохраняем оригинальный ID
            $contact['added_at'] = $item['added_at']; // Сохраняем оригинальную дату добавления
            $item = $contact;                        // Заменяем контакт
            write_contacts_json($contacts);          // Сохраняем изменения
            return true;
        }
    }
    
    return false; // Контакт с таким ID не найден
}

/**
 * Удаление контакта
 * Возвращает false, если контакт не найден
 */
function delete_contact(int $id): bool
{
    // SQLite режим — DELETE с подготовленным запросом
    if (storage_mode() === 'sqlite') {
        return sqlite()
            ->prepare('DELETE FROM contacts WHERE id = :id')
            ->execute([':id' => $id]);
    }
    
    // JSON режим — фильтруем массив, исключая удаляемый контакт
    $contacts = read_contacts();
    $filtered = [];
    $deleted = false;
    
    foreach ($contacts as $contact) {
        if ((int) $contact['id'] === $id) {
            $deleted = true; // Нашли контакт для удаления
            continue;        // Пропускаем его (не добавляем в отфильтрованный массив)
        }
        $filtered[] = $contact;
    }
    
    // Если нашли и удалили — записываем обновлённый массив
    if ($deleted) {
        write_contacts_json($filtered);
    }
    
    return $deleted;
}

// ============================================
// СОРТИРОВКА И РАБОТА СО СТРОКАМИ
// ============================================

/**
 * Приведение строки к нижнему регистру с поддержкой UTF-8
 * Использует mb_strtolower если доступно, иначе ручной маппинг для кириллицы
 */
function mb_lower(string $value): string
{
    if (function_exists('mb_strtolower')) {
        return mb_strtolower($value, 'UTF-8');
    }
    
    // Fallback для кириллицы, если mbstring не установлено
    $map = [
        'А' => 'а', 'Б' => 'б', 'В' => 'в', 'Г' => 'г', 'Д' => 'д', 'Е' => 'е', 'Ё' => 'ё',
        'Ж' => 'ж', 'З' => 'з', 'И' => 'и', 'Й' => 'й', 'К' => 'к', 'Л' => 'л', 'М' => 'м',
        'Н' => 'н', 'О' => 'о', 'П' => 'п', 'Р' => 'р', 'С' => 'с', 'Т' => 'т', 'У' => 'у',
        'Ф' => 'ф', 'Х' => 'х', 'Ц' => 'ц', 'Ч' => 'ч', 'Ш' => 'ш', 'Щ' => 'щ', 'Ъ' => 'ъ',
        'Ы' => 'ы', 'Ь' => 'ь', 'Э' => 'э', 'Ю' => 'ю', 'Я' => 'я',
    ];
    
    return strtolower(strtr($value, $map));
}

/**
 * Сравнение двух строк без учёта регистра для сортировки
 * Возвращает отрицательное, ноль или положительное число
 */
function compare_text(string $left, string $right): int
{
    return strcmp(mb_lower($left), mb_lower($right));
}

/**
 * Сортировка контактов по выбранному критерию
 * Использует usort с разной логикой сравнения в зависимости от параметра
 */
function sort_contacts(array $contacts, string $sort): array
{
    usort($contacts, function ($a, $b) use ($sort) {
        // Сортировка по фамилии (затем по имени, затем по отчеству)
        if ($sort === 'last_name') {
            foreach (['last_name', 'first_name', 'middle_name'] as $field) {
                $cmp = compare_text($a[$field], $b[$field]);
                if ($cmp !== 0) {
                    return $cmp; // Как только нашли различие — возвращаем результат
                }
            }
            return $a['id'] <=> $b['id']; // Если ФИО одинаковые — по ID
        }
        
        // Сортировка по дате рождения (затем по фамилии для стабильности)
        if ($sort === 'birth_date') {
            $cmp = strcmp($a['birth_date'], $b['birth_date']);
            if ($cmp !== 0) {
                return $cmp;
            }
            return compare_text($a['last_name'], $b['last_name']); // Одинаковые даты — по фамилии
        }
        
        // Сортировка по дате добавления (по умолчанию)
        return strcmp($a['added_at'], $b['added_at']) ?: ($a['id'] <=> $b['id']);
    });
    
    return $contacts;
}

// ============================================
// ФОРМАТИРОВАНИЕ ДАННЫХ
// ============================================

/**
 * Полное ФИО: "Иванов Иван Иванович"
 */
function full_name(array $contact): string
{
    return trim("{$contact['last_name']} {$contact['first_name']} {$contact['middle_name']}");
}

/**
 * Сокращённое ФИО: "Иванов И.И."
 * Берёт первую букву имени и отчества с точкой
 */
function short_name(array $contact): string
{
    $first = $contact['first_name'] !== '' ? mb_substr($contact['first_name'], 0, 1) . '.' : '';
    $middle = $contact['middle_name'] !== '' ? mb_substr($contact['middle_name'], 0, 1) . '.' : '';
    return trim("{$contact['last_name']} {$first}{$middle}");
}

/**
 * Форматирование даты из YYYY-MM-DD в DD.MM.YYYY
 * Использует регулярное выражение для разбора строки
 */
function format_date(string $date): string
{
    if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $date, $matches)) {
        return "{$matches[3]}.{$matches[2]}.{$matches[1]}"; // Меняем порядок: день.месяц.год
    }
    return $date; // Если формат не совпал — возвращаем как есть
}

/**
 * Обрезка строки с поддержкой UTF-8
 * Если mb_substr недоступна — использует обычную substr
 */
function utf8_substr(string $value, int $start, int $length): string
{
    if (function_exists('mb_substr')) {
        return mb_substr($value, $start, $length, 'UTF-8');
    }
    return substr($value, $start, $length);
}

// ============================================
// ФОРМЫ И ВАЛИДАЦИЯ
// ============================================

/**
 * Пустой контакт для формы добавления
 * Используется как начальное состояние полей
 */
function empty_contact(): array
{
    return [
        'last_name'   => '',
        'first_name'  => '',
        'middle_name' => '',
        'gender'      => 'Мужской', // Значение по умолчанию
        'birth_date'  => '',
        'phone'       => '',
        'address'     => '',
        'email'       => '',
        'comment'     => '',
    ];
}

/**
 * Валидация данных контакта из пользовательского ввода
 * Очищает данные (trim) и проверяет обязательные поля
 * 
 * @param array $source Неочищенные данные из $_POST
 * @return array [contact, errors] — очищенный массив и список ошибок
 */
function validate_contact_input(array $source): array
{
    // Очищаем все поля от пробелов по краям
    $contact = [
        'last_name'   => trim($source['last_name'] ?? ''),
        'first_name'  => trim($source['first_name'] ?? ''),
        'middle_name' => trim($source['middle_name'] ?? ''),
        'gender'      => trim($source['gender'] ?? 'Мужской'),
        'birth_date'  => trim($source['birth_date'] ?? ''),
        'phone'       => trim($source['phone'] ?? ''),
        'address'     => trim($source['address'] ?? ''),
        'email'       => trim($source['email'] ?? ''),
        'comment'     => trim($source['comment'] ?? ''),
    ];
    
    $errors = [];
    
    // Проверка обязательных полей
    if ($contact['last_name'] === '') {
        $errors[] = 'Не указана фамилия.';
    }
    
    if ($contact['first_name'] === '') {
        $errors[] = 'Не указано имя.';
    }
    
    // Проверка допустимых значений пола
    if (!in_array($contact['gender'], ['Мужской', 'Женский'], true)) {
        $errors[] = 'Выбран некорректный пол.';
    }
    
    // Проверка формата даты (YYYY-MM-DD)
    if ($contact['birth_date'] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $contact['birth_date'])) {
        $errors[] = 'Дата рождения должна быть задана в формате ГГГГ-ММ-ДД.';
    }
    
    if ($contact['phone'] === '') {
        $errors[] = 'Не указан телефон.';
    }
    
    if ($contact['address'] === '') {
        $errors[] = 'Не указан адрес.';
    }
    
    // Проверка email через встроенный фильтр PHP
    if ($contact['email'] === '' || !filter_var($contact['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный e-mail.';
    }
    
    return [$contact, $errors]; // Возвращаем и очищенные данные, и ошибки
}

/**
 * Рендер HTML-формы для добавления/редактирования контакта
 * Использует буферизацию вывода для захвата HTML
 * 
 * @param string $action URL-action формы (add или edit&id=N)
 * @param array $contact Текущие данные контакта для заполнения полей
 * @param string $submitLabel Текст на кнопке отправки
 * @return string HTML-код формы
 */
function render_contact_form(string $action, array $contact, string $submitLabel): string
{
    ob_start(); // Начинаем буферизацию вывода
    ?>
    <form method="post" action="?p=<?= h($action); ?>" class="contact-form">
        <label>
            <span>Фамилия</span>
            <input type="text" name="last_name" value="<?= h($contact['last_name']); ?>">
        </label>
        
        <label>
            <span>Имя</span>
            <input type="text" name="first_name" value="<?= h($contact['first_name']); ?>">
        </label>
        
        <label>
            <span>Отчество</span>
            <input type="text" name="middle_name" value="<?= h($contact['middle_name']); ?>">
        </label>
        
        <label>
            <span>Пол</span>
            <select name="gender">
                <option value="Мужской" <?= $contact['gender'] === 'Мужской' ? 'selected' : ''; ?>>Мужской</option>
                <option value="Женский" <?= $contact['gender'] === 'Женский' ? 'selected' : ''; ?>>Женский</option>
            </select>
        </label>
        
        <label>
            <span>Дата рождения</span>
            <input type="date" name="birth_date" value="<?= h($contact['birth_date']); ?>">
        </label>
        
        <label>
            <span>Телефон</span>
            <input type="text" name="phone" value="<?= h($contact['phone']); ?>">
        </label>
        
        <label>
            <span>Адрес</span>
            <input type="text" name="address" value="<?= h($contact['address']); ?>">
        </label>
        
        <label>
            <span>E-mail</span>
            <input type="email" name="email" value="<?= h($contact['email']); ?>">
        </label>
        
        <label class="full-width">
            <span>Комментарий</span>
            <textarea name="comment" rows="4"><?= h($contact['comment']); ?></textarea>
        </label>
        
        <button type="submit" class="submit-button"><?= h($submitLabel); ?></button>
    </form>
    <?php
    return ob_get_clean(); // Возвращаем содержимое буфера и очищаем его
}