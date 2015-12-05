<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

class SiteController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return [
            '$view' => [
                'title' => 'Dashboard',
                'name' => 'shoutzor:views/index.php'
            ]
        ];
    }

    /**
     * @Route("/visualizer")
     */
    public function visualizerAction()
    {
        return [
            '$view' => [
                'title' => 'Visualizer',
                'name' => 'shoutzor:views/visualizer.php',
                'layout' => false
            ]
        ];
    }

    public function redirectAction()
    {
        return App::response()->redirect('@shoutzor/greet', ['name' => 'Someone']);
    }

    public function jsonAction()
    {
        return ['message' => 'There is nothing here. Move along.'];
    }

    public function downloadAction()
    {
        return App::response()->download('extensions/shoutzor/extension.svg');
    }

    function forbiddenAction()
    {
        App::abort(401, __('Permission denied.'));
    }
}
