<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Music;
use \Exception;
use Symfony\Component\Process\Process;

require_once(__DIR__ . '/../Vendor/getid3/getid3.php');

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
                $filename = md5(uniqid()).'.'.$file->getClientOriginalName();
                $file->move($path, $filename);

                $is_video = $this->isVideo($path . $filename);

                $exists = false;
                $crc = hash_file('crc32', $path . $filename);
                if(Music::where(['crc = :hash'], ['hash' => $crc])->count() > 0) {
                    $exists = true;
                }

                if($exists == false) {
                    if($is_video) {
                        $time = exec("ffmpeg -i " . $path . $filename . " 2>&1 | grep 'Duration' | cut -d ' ' -f 4 | sed s/,//");
                        $duration = explode(":", $time);
                        if(isset($duration[2])) {
                            $duration_in_seconds = $duration[0] * 3600 + $duration[1] * 60 + round($duration[2]);
                        } else {
                            $duration_in_seconds = $duration[0] * 3600 + $duration[1] * 60;
                        }
                    } else {
                        //If it's a audio file, move it from the temp directory to the main storage directory
                        rename($path.$filename, $root_path.$filename);

                        $id3 = new \getID3();
                        $info = $id3->analyze($root_path.$filename);
                        $time = $info['playtime_string'];
                        $duration = explode(":", $time);
                        if(isset($duration[2])) {
                            $duration_in_seconds = $duration[0] * 3600 + $duration[1] * 60 + round($duration[2]);
                        } else {
                            $duration_in_seconds = $duration[0] * 3600 + $duration[1] * 60;
                        }
                    }
                }

                $music = Music::create();
                $music->save(array(
                    'title' => $file->getClientOriginalName(),
                    'artist_id' => 0,
                    'filename' => $filename,
                    'uploader_id' => App::user()->id,
                    'is_video' => $is_video,
                    'created' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'status' => ($exists) ? Music::STATUS_DUPLICATE : (($is_video) ? Music::STATUS_UPLOADED : Music::STATUS_FINISHED),
                    'amount_requested' => 0,
                    'crc' => $crc,
                    'duration' => $duration_in_seconds
                ));

                $fileId = $music->id;
            } else {
                $fileId = 0;
            }

            //Prevent Divide by zero error
            $filesize = ($file->getClientSize() == 0) ? 1 : $file->getClientSize();

            $result = array(
                'id' => $fileId,
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
        try {
            $proc = new Process('ffprobe -v quiet -print_format json -show_streams '.$filename);
            $proc->run();

            // executes after the command finishes
            if (!$proc->isSuccessful()) {
                throw new Exception(__('Failed to run the ffprobe command'));
            }

            $data = json_decode($proc->getOutput());

            //Check if the file contains a video stream
            foreach($data->streams as $stream) {
                if($stream->codec_type == 'video') {
                    return true;
                }
            }

            return false;
        } catch(Exception $e) {
            return false;
        }
    }

    protected function getPath($path = '')
    {
        $path = $this->normalizePath($path);

        if(substr($path, -1) !== '/') {
            $path .= '/';
        }

        return $path;
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