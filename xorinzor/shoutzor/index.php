<?php

use Pagekit\Application;

/*
 * This array is the module definition.
 * It's used by Pagekit to load your extension and register all things
 * that your extension provides (routes, menu items, php classes etc)
 */
return [

    /*
     * Define a unique name.
     */
    'name' => 'shoutzor',

    /*
     * Define the type of this module.
     * Has to be 'extension' here. Can be 'theme' for a theme.
     */
    'type' => 'extension',

    /*
     * Main entry point. Called when your extension is both installed and activated.
     * Either assign an closure or a string that points to a PHP class.
     * Example: 'main' => 'Pagekit\\Hello\\HelloExtension'
     */
    'main' => function (Application $app) {

        // bootstrap code

    },

    /*
     * Register all namespaces to be loaded.
     * Map from namespace to folder where the classes are located.
     * Remember to escape backslashes with a second backslash.
     */
    'autoload' => [

        'Xorinzor\\Shoutzor\\' => 'src'

    ],

    /*
     * Define nodes. A node is similar to a route with the difference
     * that it can be placed anywhere in the menu structure. The
     * resulting route is therefore determined on runtime.
     */
    'nodes' => [

        'shoutzor/home' => [

            // The name of the node route
            'name' => '@shoutzor/home',

            // Label to display in the backend
            'label' => 'Dashboard',

            // The controller for this node. Each controller action will be mounted
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::indexAction',

            // A unique node that cannot be deleted, resides in "Not Linked" by default
            'protected' => true,

            'frontpage' => true,

            'active' => '@shoutzor/home'

        ],

        'shoutzor/visualizer' => [

            // The name of the node route
            'name' => '@shoutzor/visualizer',

            // Label to display in the backend
            'label' => 'Shoutzor Visualizer',

            // The controller for this node. Each controller action will be mounted
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::visualizerAction',

            // A unique node that cannot be deleted, resides in "Not Linked" by default
            'protected' => true,

            'active' => '@shoutzor/visualizer'

        ],

        'shoutzor/uploadmanager' => [

            // The name of the node route
            'name' => '@shoutzor/uploadmanager',

            // Label to display in the backend
            'label' => 'Upload manager',

            // The controller for this node. Each controller action will be mounted
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::uploadManagerAction',

            // A unique node that cannot be deleted, resides in "Not Linked" by default
            'protected' => true,

            'active' => '@shoutzor/uploadmanager'

        ],

        'shoutzor/search' => [

            // The name of the node route
            'name' => '@shoutzor/search',

            // Label to display in the backend
            'label' => 'Search',

            // The controller for this node. Each controller action will be mounted
            'controller' => 'Xorinzor\\Shoutzor\\Controller\\SiteController::searchAction',

            // A unique node that cannot be deleted, resides in "Not Linked" by default
            'protected' => true,

            'active' => '@shoutzor/search'

        ]

    ],


    /*
     * Define routes.
     */
    'routes' => [
        '/shoutzor' => [
            'name' => '@shoutzor/admin',
            'controller' => [
                'Xorinzor\\Shoutzor\\Controller\\ShoutzorController'
            ]
        ],

        '/shoutzor/visualizer' => [
            'name' => '@shoutzor/admin/visualizer',
            'controller' => [
                'Xorinzor\\Shoutzor\\Controller\\VisualizerController'
            ]
        ],

        '/shoutzor/audio' => [
            'name' => '@shoutzor/admin/audio',
            'controller' => [
                'Xorinzor\\Shoutzor\\Controller\\AudioController'
            ]
        ],

        '/shoutzor/vlc' => [
            'name' => '@shoutzor/admin/vlc',
            'controller' => [
                'Xorinzor\\Shoutzor\\Controller\\VlcController'
            ]
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
