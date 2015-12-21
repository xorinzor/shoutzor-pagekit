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
        $uploaded = Music::where(['status = :finished'], ['finished' => Music::STATUS_FINISHED])->orderBy('created', 'DESC')->related(['artist', 'user'])->limit(8)->get();
        $requested = Music::where(['amount_requested > 0 AND status = :finished'], ['finished' => Music::STATUS_FINISHED])->orderBy('amount_requested', 'DESC')->related(['artist', 'user'])->limit(8)->get();

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

        $uploads = Music::where(['uploader_id = :uploader AND status != :finished'], ['uploader' => App::user()->id, 'finished' => Music::STATUS_FINISHED])->orderBy('created', 'DESC')->related(['artist', 'user'])->get();

        return [
            '$view' => [
                'title' => __('Upload Manager'),
                'name' => 'shoutzor:views/uploadmanager.php'
            ],

            'uploads' => $uploads
        ];
    }

    /**
     * @Route("/search", methods="GET")
     * @Request({"q":"string"})
     */
    public function searchAction($q = "")
    {
        if(empty($q)) {
            return [
                '$view' => [
                    'title' => 'Search',
                    'name' => 'shoutzor:views/search_error.php'
                ]
            ];
        }
        $query = Music::where(['status = :finished AND (title LIKE :search OR filename LIKE :search)'], ['finished' => Music::STATUS_FINISHED, 'search' => "%{$q}%"])
                ->orderBy('created', 'DESC');

        $total = $query->count();

        $results = $query->related(['artist'])
                ->limit(10)
                ->get();

        return [
            '$view' => [
                'title' => 'Search',
                'name' => 'shoutzor:views/search.php'
            ],

            'searchterm' => htmlspecialchars($q),
            'total' => $total,
            'results' => $results
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
