<?php

return [

    'name' => 'Downloads Module',
    'icon' => 'https://imgur.png',
    'author' => 'WemX',
    'version' => '1.0.0',
    'wemx_version' => '1.0.0',

    'elements' => [

        'main_menu' =>
        [
            [
                'name' => 'Downloads',
                'icon' => '<i class="bx bxs-cloud-download"></i>',
                'href' => '/downloads',
                'style' => '',
            ],
            // ... add more menu items
        ],

        'admin_menu' => 
        [
            [
                'name' => 'Downloads',
                'icon' => '<i class="fas fa-solid fa-download"></i>',
                'type' => 'dropdown',
                'items' => [
                    [
                        'name' => 'Downloads',
                        'href' => '/admin/downloads/',
                    ],
                    [
                        'name' => 'Create',
                        'href' => '/admin/downloads/create',
                    ],
                ],
            ],
            // ... add more menu items
        ],

    ],

];
