<?php

return [

    'name' => 'Pterodactyl',
    'icon' => 'https://imgur.png',
    'author' => 'GIGABAIT',
    'version' => '2.1.3',
    'wemx_version' => '>=2.0.0',
    'service' => \App\Services\Pterodactyl\Service::class,

    'elements' => [

        'admin_menu' =>
        [

            [
                'name' => 'admin.pterodactyl',
                'icon' => '<i class="fas fa-solid fa-dragon"></i>',
                'type' => 'dropdown',
                'items' => [
                    [
                        'name' => 'admin.configuration',
                        'href' => '/admin/pterodactyl/config',
                    ],
                    [
                        'name' => 'admin.nodes',
                        'href' => '/admin/pterodactyl/nodes',
                    ],
                    [
                        'name' => 'admin.users',
                        'href' => '/admin/pterodactyl/users',
                    ],
                    [
                        'name' => 'admin.servers',
                        'href' => '/admin/pterodactyl/servers',
                    ],
                    [
                        'name' => 'admin.logs',
                        'href' => '/admin/pterodactyl/logs'
                    ],
                    [
                        'name' => 'Debug',
                        'href' => '/admin/pterodactyl/debug'
                    ],
                    [
                        'name' => 'admin.clear_cache',
                        'href' => '/admin/pterodactyl/clear/cache',
                    ]
                ],
            ],

        ],

    ],

];
