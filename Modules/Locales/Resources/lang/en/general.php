<?php return array(
    'module_name' => 'Locales Module',
    'title' => 'Language Manager',
    'header' => 'Localizations',
    'name' => 'Locales Module',
    'language' => 'Language',
    'cancel' => 'Cancel',
    'select_localisation' => 'Select Localization',
    'update_all_localisation' => 'Update all localization',
    'generate_new' => 'Generate New',
    'locale_code' => 'Code',
    'locale_name' => 'Name',
    'locale_path' => 'Path',
    'remove' => 'Remove',
    'locale_actions' => 'Actions',
    'translation' => 'Translation',
    'translate' => 'Translate',
    'files' => 'Files',
    'general_files' => 'General files',
    'module_files' => ':module module files',
    'generate' => 'Generate',
    'develop_info' =>
        '<code class="ml-4">If you do not want to see this message, turn off the debug mod.
        <br>If you are a developer, the module allows you to automatically generate localizations. 
        <br>Add the LOCALES_GENERATOR=true option to the .env file
        <br>After that you can just use the functions. Examples:<br><strong class="text-dark">
         __(\'file.key\'), __(\'file.key1.key2\'), __(\'module::file.key1.key2\'), 
         @lang(\'file.key1\'), @lang(\'module::file.key\', [\'default\' => \'Key value\'])</strong>
        <br>and if the file does not exist, it will be created with the key and value, and if the file already exists, the key and value will be added to it.
        <br>For the correct operation of the module, make sure that the rights to the directories and localization files belong to www-data</code>',
);
