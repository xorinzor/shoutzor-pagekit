<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Music;

/**
 * @Route("music", name="music")
 */
class MusicApiController
{
    /**
     * @Route("/", methods="GET")
     * @Request({"filter": "array", "page":"int"})
     */
    public function indexAction($filter = [], $page = 0)
    {
        return array();
    }

    /**
     * @Route("/upload", methods="POST")
     */
    public function uploadAction()
    {
        //Make sure file uploads are enabled
        $allowed = App::module('shoutzor')->config('allow_uploads');

        //Todo check if user has permission to upload a file

        $file = App::request()->files->get('musicfile');

        if ($file === null) {
            App::abort(400, __('No file uploaded.'));
        }

        if($file->isValid()) {
            //Todo store file, add to database table, return JSON object with ID for status-tracking
        }


        //Prevent Divide by zero error
        $filesize = ($file->getClientSize() == 0) ? 1 : $file->getClientSize();

        $result = array(
            'id' => 0, //todo replace with Mysql insert ID
            'filename' => $file->getClientOriginalName(),
            'size' => $filesize / (1024 * 1024), //Filesize in MB
            'isValid' => $file->isValid()
        );

        $result = array('result' => true, 'info' => $result);

        return $result;
    }

    /**
     * @Route("/request", methods="POST", requirements={"id"="\d+"})
     * @Request({"id": "int"})
     */
    public function requestAction($id = 0)
    {
        //Check if the ID is invalid or the music item is not found
        if (!$id || !$music = Music::find($id)) {
            App::abort(404, __('Music not found.'));
        }

        //Music item is found, continue ($music now contains the item).


        return array();
    }
}