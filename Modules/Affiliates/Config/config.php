<?php

return [

    'name' => 'Affiliates Module',
    'icon' => 'https://imgur.png',
    'author' => 'WemX',
    'version' => '1.0.0',
    'wemx_version' => '1.0.0',

    'elements' => [

        'user_dropdown' =>
        [
            [
                'name' => 'Affiliates',
                'icon' => '<i class="bx bx-link"></i><i class="fas fa-solid fa-handshake"></i>',
                'href' => '/affiliates',
                'style' => '',
            ],
        ],
        'admin_menu' =>
        [

            [
                'name' => 'Affiliates',
                'icon' => '<i class="fas fa-solid fa-handshake"></i>',
                'type' => 'dropdown',
                'items' => [
                    [
                        'name' => 'Settings',
                        'href' => '/admin/affiliates/settings',
                    ],

                    [
                        'name' => 'Affiliates',
                        'href' => '/admin/affiliates',
                    ],

                    [
                        'name' => 'Payouts',
                        'href' => '/admin/affiliates/payouts',
                    ],
                ],
            ],

        ],
    ],

];
