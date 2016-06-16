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
    'nodes' => [

        'shoutzor/home' => [
            'name' => '@shoutzor/home',
            'label' => 'Dashboard',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::indexAction',
            'protected' => true,
            'frontpage' => true,
            'active' => '@shoutzor/home'
        ],

        'shoutzor/visualizer' => [
            'name' => '@shoutzor/visualizer',
            'label' => 'Shoutzor Visualizer',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::visualizerAction',
            'protected' => true,
            'active' => '@shoutzor/visualizer'
        ],

        'shoutzor/uploadmanager' => [
            'name' => '@shoutzor/uploadmanager',
            'label' => 'Upload manager',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::uploadManagerAction',
            'protected' => true,
            'active' => '@shoutzor/uploadmanager'
        ],

        'shoutzor/search' => [
            'name' => '@shoutzor/search',
            'label' => 'Search',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::searchAction',
            'protected' => true,
            'active' => '@shoutzor/search'
        ]
*/


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

        '/shoutzor/visualizer' => [
            'name' => '@shoutzor/admin/visualizer',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\VisualizerController'
        ],

        '/shoutzor/audio' => [
            'name' => '@shoutzor/admin/audio',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\AudioController'
        ],

        '/shoutzor/vlc' => [
            'name' => '@shoutzor/admin/vlc',
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\VlcController'
        ],

        '/shoutzor/api' => [
            'name' => '@shoutzor/api',
            'controller' => [
                'Xorinzor\\Shoutzor\\Controller\\MusicApiController',
                'Xorinzor\\Shoutzor\\Controller\\VlcmanagerApiController',
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

            // Label to display
            'label' => 'Shoutzor',

            // Icon to display
            'icon' => 'shoutzor:icon.png',

            // URL this menu item links to
            'url' => '@shoutzor/admin',

            // Optional: Expression to check if menu item is active on current url
            // 'active' => '@hello*'

            // Optional: Limit access to roles which have specific permission assigned
            'access' => 'shoutzor: manage shoutzor'
        ],

        'shoutzor: settings' => [

            // Parent menu item, makes this appear on 2nd level
            'parent' => 'shoutzor',

            // See above
            'label' => 'Shoutzor',
            'icon' => 'shoutzor:icon.png',
            'url' => '@shoutzor/admin',
            'access' => 'shoutzor: manage shoutzor'
        ],

        'shoutzor: visualizer' => [
            'parent' => 'shoutzor',
            'label' => 'Visualizer',
            'url' => '@shoutzor/admin/visualizer',
            'access' => 'shoutzor: manage visualizer settings'
        ],

        'shoutzor: audio' => [
            'parent' => 'shoutzor',
            'label' => 'Audio',
            'url' => '@shoutzor/admin/audio',
            'access' => 'shoutzor: manage audio settings'
        ],

        'shoutzor: vlc' => [
            'parent' => 'shoutzor',
            'label' => 'VLC',
            'url' => '@shoutzor/admin/vlc',
            'access' => 'shoutzor: manage vlc settings'
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

        'shoutzor: manage visualizer settings' => [
            'title' => 'Manage Shoutzor Visualizer Settings'
        ],

        'shoutzor: manage audio settings' => [
            'title' => 'Manage Shoutzor Audio Settings'
        ],

        'shoutzor: manage vlc settings' => [
            'title' => 'Manage Shoutzor VLC Service Settings'
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

        'visualizer' => [
            'enabled' => 0,
        ],

        'vlc' => [
            'transcoding' => [
                'bitrate' => 192,
                'threads' => 4,
                'acodec' => 'vorbisenc',
                'vcodec' => 'theoraenc',
                'videoquality' => 10,
                'audioquality' => 10
            ],

            'stream' => [
                'output' => [
                    'host'      => 'localhost',
                    'mount'     => 'shoutzor.ogg',
                    'port'      => 8000,
                    'password'  => 'replaceme'
                ],

                'video' => [
                    'placeholder' => '../images/placeholder.ogg',
                    'width' => 1920,
                    'height' => 1080,
                    'fps' => 60,
                    'logo' => [
                        'x' => 5,
                        'y' => 5,
                        'transparency' => 255,
                        'path' => "../images/shoutzor-christmas-logo-small.png"
                    ]
                ]
            ],

            'telnet' => [
                'port' => 4212,
                'password' => 'replaceme'
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
