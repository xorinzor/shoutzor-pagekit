<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Music;
use \Exception;

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
        try {
            //Make sure file uploads are enabled
            if(App::module('shoutzor')->config('shoutzor.upload') == 0) {
                throw new Exception(__('File uploads have been disabled'));
            }

            //Todo check if user has permission to upload a file

            $root_path = $this->getPath(App::module('system/finder')->config('storage'));
            $path = $root_path;

            if (!is_dir($path) || !is_writable($path)) {
                throw new Exception(__('Cannot move file to '.$path.', Permission denied.'));
            }

            $file = App::request()->files->get('musicfile');

            if ($file === null) {
                throw new Exception(__('No file uploaded.'));
            }

            if($file->isValid()) {
                $file->move($path, $file->getClientOriginalName());

                $music = Music::create();
                $music->save(array(
                    'title' => '',
                    'artist_id' => 0,
                    'filename' => $file->getClientOriginalName(),
                    'uploader_id' => App::user()->id,
                    'is_video' => false,
                    'created' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'status' => 0,
                    'amount_requested' => 0
                ));

                $fileId = $music->id;
            } else {
                $fileId = 0;
            }

            //Prevent Divide by zero error
            $filesize = ($file->getClientSize() == 0) ? 1 : $file->getClientSize();

            $result = array(
                'id' => $fileId, //todo replace with Mysql insert ID
                'filename' => $file->getClientOriginalName(),
                'size' => $filesize / (1024 * 1024), //Filesize in MB
                'isValid' => (($fileId == 0) ? false : true)
            );

            return array('result' => true, 'info' => $result);

        } catch(Exception $e) {
            App::abort(400, $e->getMessage());
        }
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

    protected function getPath($path = '')
    {
        $root = strtr(App::path(), '\\', '/');
        $path = $this->normalizePath($root.'/'.App::request()->get('root').'/'.App::request()->get('path').'/'.$path);

        return 0 === strpos($path, $root) ? $path : false;
    }

    /**
     * Normalizes the given path
     *
     * @param  string $path
     * @return string
     */
    protected function normalizePath($path)
    {
        $path   = str_replace(['\\', '//'], '/', $path);
        $prefix = preg_match('|^(?P<prefix>([a-zA-Z]+:)?//?)|', $path, $matches) ? $matches['prefix'] : '';
        $path   = substr($path, strlen($prefix));
        $parts  = array_filter(explode('/', $path), 'strlen');
        $tokens = [];

        foreach ($parts as $part) {
            if ('..' === $part) {
                array_pop($tokens);
            } elseif ('.' !== $part) {
                array_push($tokens, $part);
            }
        }

        return $prefix . implode('/', $tokens);
    }
}