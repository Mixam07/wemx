<?php return array(
    'title' => 'Менеджер мов',
    'header' => 'Локалізація',
    'name' => 'Модуль Локалізацій',
    'language' => 'Мова',
    'cancel' => 'Скасувати',
    'select_localisation' => 'Виберіть локалізацію',
    'update_all_localisation' => 'Оновити всю локалізацію',
    'generate_new' => 'Створити нову',
    'locale_code' => 'Код',
    'locale_name' => 'Ім\'я',
    'locale_path' => 'Шлях',
    'remove' => 'Видалити',
    'locale_actions' => 'Дії',
    'translation' => 'Переклад',
    'translate' => 'Перекласти',
    'files' => 'Файли',
    'general_files' => 'Загальні файли',
    'module_files' => 'Файли модуля :module',
    'module_name' => 'Локалізації',
    'generate' => 'Генерувати',
    'develop_info' =>
        '<code class="ml-4">Якщо ви не хочите бачити це повідомлення виккніть дебаг мод.
        <br>Якщо ви розробник, модуль дозволяє автоматично генеревути локалізаціЇ. 
        <br>Додайте параметр LOCALES_GENERATOR=true до файлу .env
        <br>Після цього ви можете просто икористовувати функції. Приклади:<br><strong class="text-dark">
         __(\'file.key\'), __(\'file.key1.key2\'), __(\'module::file.key1.key2\'), 
         @lang(\'file.key1\'), @lang(\'module::file.key\', [\'default\' => \'Key value\'])</strong>
        <br>і якщо файла не існую він буде створений із ключе та значенням а якщо файл уже існую у нього буде добавлений ключ та значення.
        <br>Для коректної роботи модуля переконайтеся що права на каталоги та файли локалізація належать www-data</code>',
);