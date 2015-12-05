<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class VisualizerController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Hello Settings'),
                'name'  => 'shoutzor:views/admin/visualizer.php'
            ],
            '$data' => [
                'config' => App::module('shoutzor')->config()
            ]
        ];
    }
}
