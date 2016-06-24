<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class ControlsController
{
    public function indexAction()
    {

        $baseConfig = App::module('shoutzor')->config('liquidsoap');
        $config = App::config('liquidsoap')->toArray();
        $config = array_merge($baseConfig, $config);
        $config = array_merge($config, $_POST); //Set the value to the new POST data

        //socketPath . '/wrapper'
        //socketPath . '/shoutzor'

        return [
            '$view' => [
                'title' => __('Shoutzor Controls'),
                'name'  => 'shoutzor:views/admin/controls.php'
            ],
            'config' => $config
        ];
    }
}
