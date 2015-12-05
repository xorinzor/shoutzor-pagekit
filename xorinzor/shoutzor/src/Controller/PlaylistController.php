<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class PlaylistController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Playlist Settings'),
                'name'  => 'shoutzor:views/admin/playlist.php'
            ],
            '$data' => [
                'config' => App::module('shoutzor')->config()
            ]
        ];
    }
}
