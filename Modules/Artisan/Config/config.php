<?php

return [

    'name' => 'Artisan',
    'icon' => 'https://imgur.png',
    'author' => 'GIGABAIT',
    'version' => '1.0.5',
    'wemx_version' => '>=2.0.0',

    'elements' => [
        'admin_menu' =>
            [
                [
                    'name' => 'Artisan',
                    'icon' => '<i class="fas fa-terminal"></i>',
                    'href' => '/admin/artisan',
                    'style' => '',
                ],
            ],

    ],

    'commands' => [
        [
            'name' => 'Clear Cache',
            'command' => 'cache:clear',
            'description' => 'Flush the application cache',
        ],
        [
            'name' => 'Clear Config',
            'command' => 'config:clear',
            'description' => 'Remove the configuration cache file',
        ],
        [
            'name' => 'Clear Route',
            'command' => 'route:clear',
            'description' => 'Remove the route cache file',
        ],
        [
            'name' => 'Clear View',
            'command' => 'view:clear',
            'description' => 'Clear all compiled view files',
        ],
        [
            'name' => 'Clear All Cache',
            'command' => 'optimize:clear',
            'description' => 'Clear View, Route, Config and Application Cache',
        ],
        [
            'name' => 'Migrate',
            'command' => 'migrate --force',
            'description' => 'Run the database migrations',
        ],
        [
            'name' => 'Storage Link',
            'command' => 'storage:link',
            'description' => 'Create the symbolic links configured for the application',
        ],
        [
            'name' => 'Update Module',
            'command' => 'module:update',
            'description' => 'Update module assets',
        ],
        [
            'name' => 'Send Emails',
            'command' => 'cron:emails:send',
            'description' => 'Send pending emails',
        ],
        [
            'name' => 'Suspend Cancelled Orders',
            'command' => 'cron:orders:suspend-cancelled',
            'description' => 'Suspend orders that are cancelled and past grace period',
        ],
        [
            'name' => 'Suspend Expired Orders',
            'command' => 'cron:orders:suspend-expired',
            'description' => 'Suspend orders that are expired',
        ],
        [
            'name' => 'Terminate Orders',
            'command' => 'cron:orders:terminate-suspended',
            'description' => 'Terminate orders that are suspended',
        ],
        [
            'name' => 'Delete Expired Payments',
            'command' => 'cron:payments:delete-expired',
            'description' => 'Delete payments that are expired and unpaid',
        ],
        [
            'name' => 'Update Permissions',
            'command' => 'permissions:save',
            'description' => 'Update permissions from routes',
        ],
        [
            'name' => 'Check Subscriptions',
            'command' => 'subscriptions:check',
            'description' => 'Check and update subscription statuses',
        ]
    ],
];
