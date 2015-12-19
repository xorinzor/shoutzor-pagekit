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

            //Make sure file uploads are enabled
            if(!App::user()->hasAccess("shoutzor: upload files")) {
                throw new Exception(__('You have no permission to upload files'));
            }

            $root_path = $this->getPath(App::path() . '/' . App::module('system/finder')->config('storage'));
            $path = $root_path  . 'temp/';

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
                    'is_video' => $this->isVideo($path . $file->getClientOriginalName()),
                    'created' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'status' => 0,
                    'amount_requested' => 0,
                    'crc' => ''
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

    protected function isVideo($filename)
    {
        $proc = new Process('ffprobe -v quiet -print_format json -show_streams '.$filename);
        $proc->run();

        // executes after the command finishes
        if (!$proc->isSuccessful()) {
            throw new Exception(__('Failed to run the ffprobe command'));
        }

        $data = json_decode($proc->getOutput());

        //Check if the file contains a video stream
        foreach($data['streams'] as $stream) {
            if($stream['codec_type'] == 'video') {
                return true;
            }
        }

        return false;
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