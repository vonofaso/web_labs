<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ЛР7. Ввод массива и выбор сортировки</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="page">
        <header class="page-header">
            <div>
                <h1>ЛР7. Сортировка массива</h1>
                <p>Сафонов Александр Олегович, группа 241-352</p>
            </div>
        </header>

        <main class="panel">
            <h2>Форма ввода</h2>
            <p>Добавьте произвольное количество чисел, выберите алгоритм и откройте протокол сортировки в новой вкладке.</p>

            <form action="result.php" method="post" target="_blank" class="lab-form">
                <table id="elements" class="elements-table">
                    <tbody>
                        <tr>
                            <td class="element-index">0</td>
                            <td><input type="text" name="element0" placeholder="Введите число"></td>
                        </tr>
                    </tbody>
                </table>

                <input type="hidden" name="arrLength" id="arrLength" value="1">

                <label class="form-row">
                    <span>Алгоритм сортировки</span>
                    <select name="algorithm">
                        <option value="selection">Сортировка выбором</option>
                        <option value="bubble">Пузырьковая сортировка</option>
                        <option value="shell">Алгоритм Шелла</option>
                        <option value="gnome">Сортировка садового гнома</option>
                        <option value="quick">Быстрая сортировка</option>
                        <option value="native">Встроенная сортировка PHP</option>
                    </select>
                </label>

                <div class="actions">
                    <button type="button" class="secondary" onclick="addElement()">Добавить еще один элемент</button>
                    <button type="submit">Сортировать массив</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        function setHTML(element, html) {
            if ('innerHTML' in element) {
                element.innerHTML = html;
                return;
            }

            var range = document.createRange();
            range.selectNodeContents(element);
            range.deleteContents();
            element.appendChild(range.createContextualFragment(html));
        }

        function addElement() {
            var table = document.getElementById('elements').getElementsByTagName('tbody')[0];
            var index = table.rows.length;
            var row = table.insertRow(index);

            var cellIndex = row.insertCell(0);
            var cellInput = row.insertCell(1);

            cellIndex.className = 'element-index';
            cellIndex.textContent = index;

            setHTML(cellInput, '<input type="text" name="element' + index + '" placeholder="Введите число">');
            document.getElementById('arrLength').value = table.rows.length;
        }
    </script>
</body>
</html>
