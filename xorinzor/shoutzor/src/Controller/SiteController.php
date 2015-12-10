<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Music;

class SiteController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $uploaded = Music::where('1=1')->orderBy('created', 'DESC')->related(['artist', 'user'])->limit(8)->get();
        $requested = Music::where('amount_requested > 0')->orderBy('amount_requested', 'DESC')->related(['artist', 'user'])->limit(8)->get();

        return [
            '$view' => [
                'title' => __('Dashboard'),
                'name' => 'shoutzor:views/index.php'
            ],
            'uploaded' => $uploaded,
            'requested' => $requested
        ];
    }

    /**
     * @Route("/visualizer")
     */
    public function visualizerAction()
    {
        return [
            '$view' => [
                'title' => __('Visualizer'),
                'name' => 'shoutzor:views/visualizer.php',
                'layout' => false
            ]
        ];
    }

    /**
     * @Route("/uploadmanager", methods="GET")
     */
    public function uploadManagerAction()
    {

        $uploads = Music::where(['uploader_id = ?'], [App::user()->id])->orderBy('status', 'ASC')->related(['artist', 'user'])->get();

        return [
            '$view' => [
                'title' => __('Upload Manager'),
                'name' => 'shoutzor:views/uploadmanager.php'
            ],

            'uploads' => $uploads
        ];
    }

    /**
     * @Route("/uploadmanager", methods="POST")
     */
    public function uploadAction()
    {
        App::module('shoutzor')->config('allow_uploads');

        $file = App::request()->files->get('file');

        if ($file === null || !$file->isValid()) {
            App::abort(400, __('No file uploaded.'));
        }

        $result = array('result' => false);

        return $result;
    }

    /**
     * @Route("/search")
     */
    public function searchAction()
    {
        return [
            '$view' => [
                'title' => 'Search',
                'name' => 'shoutzor:views/search.php'
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
