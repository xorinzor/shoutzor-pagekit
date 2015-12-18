<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class VlcController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('VLC Settings'),
                'name'  => 'shoutzor:views/admin/vlc.php'
            ],
            '$data' => [
                'config' => App::module('shoutzor')->config()
            ]
        ];
    }
}
