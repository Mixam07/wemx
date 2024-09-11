<?php

return [

    'name' => 'Tickets Module',
    'icon' => 'https://imgur.png',
    'author' => 'WemX',
    'version' => '1.0.0',
    'wemx_version' => '1.0.0',

    'elements' => [

        'main_menu' =>
        [
            [
                'name' => 'Tickets',
                'icon' => "<i class='bx bxs-chat' ></i>",
                'href' => '/tickets',
                'style' => '',
            ],
        ],

        'apps' =>
        [
            [
                'name' => 'Tickets',
                'icon' => "<i class='bx bxs-chat' ></i>",
                'href' => '/tickets',
                'style' => '',
            ],
        ],

        'admin_menu' =>
        [

            [
                'name' => 'Tickets',
                'icon' => '<i class="fas fa-ticket-alt"></i>',
                'type' => 'dropdown',
                'items' => [
                    [
                        'name' => 'Settings',
                        'href' => '/admin/tickets/settings',
                    ],
                    [
                        'name' => 'Tickets',
                        'href' => '/admin/tickets',
                    ],

                    [
                        'name' => 'Departments',
                        'href' => '/admin/tickets/departments',
                    ],

                    [
                        'name' => 'Responders',
                        'href' => '/admin/tickets/responders',
                    ],
                ],
            ],

        ],
    ],

];
