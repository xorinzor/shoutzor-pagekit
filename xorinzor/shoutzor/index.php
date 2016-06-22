<?php

use Pagekit\Application;

return [
    'name' => 'shoutzor',
    'type' => 'extension',
    'main' => function (Application $app) {
        // bootstrap code
    },

    'autoload' => [
        'Xorinzor\\Shoutzor\\' => 'src'
    ],

    /*
     * Define routes.
     */
    'routes' => [
        '/'     => [
            'name' => '@shoutzor/home',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::indexAction'
        ],

        '/upload-manager' => [
            'name' => '@shoutzor/uploadmanager',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::uploadManagerAction'
        ],

        '/search' => [
            'name' => '@shoutzor/search',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::searchAction'
        ],

        '/shoutzor' => [
            'name' => '@shoutzor/admin',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\ShoutzorController'
        ],

        '/shoutzor/audio' => [
            'name' => '@shoutzor/admin/audio',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\AudioController'
        ],

        '/shoutzor/liquidsoap' => [
            'name' => '@shoutzor/admin/liquidsoap',
            'controller' => [
                'Xorinzor\\Shoutzor\\Controller\\LiquidsoapController'
            ]
        ],

        '/shoutzor/api' => [
            'name' => '@shoutzor/api',
            'controller' => [
                'Xorinzor\\Shoutzor\\Controller\\MusicApiController',
                'Xorinzor\\Shoutzor\\Controller\\MusicconverterApiController'
            ]
        ]
    ],

    /*
     * Define menu items for the backend.
     */
    'menu' => [

        // name, can be used for menu hierarchy
        'shoutzor' => [
            'label' => 'Shoutzor',
            'icon' => 'shoutzor:icon.png',
            'url' => '@shoutzor/admin',
            'access' => 'shoutzor: manage shoutzor'
        ],

        'shoutzor: settings' => [
            'parent' => 'shoutzor',
            'label' => 'Shoutzor',
            'icon' => 'shoutzor:icon.png',
            'url' => '@shoutzor/admin',
            'access' => 'shoutzor: manage shoutzor'
        ],

        'shoutzor: audio' => [
            'parent' => 'shoutzor',
            'label' => 'Audio',
            'url' => '@shoutzor/admin/audio',
            'access' => 'shoutzor: manage audio settings'
        ],

        'shoutzor: liquidsoap' => [
            'parent' => 'shoutzor',
            'label' => 'Liquidsoap',
            'url' => '@shoutzor/admin/liquidsoap',
            'access' => 'shoutzor: manage liquidsoap settings'
        ]
    ],

    /*
     * Define permissions.
     * Will be listed in backend and can then be assigned to certain roles.
     */
    'permissions' => [

        // Unique name.
        // Convention: extension name and speaking name of this permission (spaces allowd)
        'shoutzor: manage shoutzor' => [
            'title' => 'Manage Shoutzor Settings'
        ],

        'shoutzor: manage audio settings' => [
            'title' => 'Manage Shoutzor Audio Settings'
        ],

        'shoutzor: manage liquidsoap settings' => [
            'title' => 'Manage Shoutzor liquidsoap Settings'
        ],

        'shoutzor: upload files' => [
            'title' => 'Upload files to shoutzor'
        ],

        'shoutzor: add requests' => [
            'title' => 'Make requests for the shoutzor playlist'
        ]

    ],

    /*
     * Link to a settings screen from the extensions listing.
     */
    'settings' => '@shoutzor/admin',

    /*
     * Default module configuration.
     * Can be overwritten by changed config during runtime.
     */
    'config' => [

        'root_path' => __DIR__,

        'search' => [
            'results_per_page' => 10,
            'max_results_per_page' => 20
        ],

        'shoutzor' => [
            'upload' => 3,
            'request' => 2
        ],

        'liquidsoap' => [
            'stream' => [
                'output' => [
                    'host'      => 'localhost',
                    'mount'     => '/shoutzor.mp3',
                    'port'      => 8000,
                    'password'  => 'hackme'
                ],
                'wrapperinput' => [
                    'host' => 'localhost',
                    'mount' => '/input',
                    'port' => 1337,
                    'password' => 'hackme'
                ]
            ]
        ]

    ],

    /*
     * Listen to events.
     */
    'events' => [

        'view.scripts' => function ($event, $scripts) {
            $scripts->register('shoutzor-settings', 'shoutzor:app/bundle/settings.js', '~extensions');
        }

    ]

];
