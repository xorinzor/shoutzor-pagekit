<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class AudioController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Audio Settings'),
                'name'  => 'shoutzor:views/admin/audio.php'
            ],
            '$data' => [
                'config' => App::module('shoutzor')->config()
            ]
        ];
    }
}
