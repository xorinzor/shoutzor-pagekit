<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

/**
 * @Access(admin=true)
 */
class LiquidsoapController
{
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => __('Liquidsoap Settings'),
                'name'  => 'shoutzor:views/admin/liquidsoap.php'
            ],
            '$data' => [
                'config' => App::module('shoutzor')->config()
            ]
        ];
    }
}
