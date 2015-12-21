<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use \Exception;
use Symfony\Component\Process\Process;
use Xorinzor\Shoutzor\Model\Music;

/**
 * @Route("musicconverter", name="musicconverter")
 */
class MusicconverterApiController
{
    private $telnet;

    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        try {
            $this->ensureLocalhost();

            /* return server status */
            return array('result' => true);
        } catch(Exception $e) {
            App::abort(400, $e->getMessage());
        }
    }

    /**
     * @Route("/autoparse", methods="GET")
     */
    public function autoparseAction() {
        try {
            $this->ensureLocalhost();

            $root_path = $this->getPath(App::path() . '/' . App::module('system/finder')->config('storage'));
            $path = $root_path . 'last_run.txt';

            if(!file_exists($path)) {
                file_put_contents($path, '0');
            }

            if(file_get_contents($path) > strtotime("-1 minute")) {
                throw new Exception(__('A parser is already running or has been running too recently'));
            }

            $toParse = Music::where(['status = :status'], ['status' => Music::STATUS_UPLOADED])->get();

            foreach($toParse as $item) {
                file_put_contents($path, time());
                $this->parseAction($item->id);
            }

            return array('result' => true);

        } catch(Exception $e) {
            App::abort(400, $e->getMessage());
        }
    }

    /**
     * @Route("/parse", methods="GET")
     * @Request({"music": "int"})
     */
    public function parseAction($music = 0)
    {
        try {
            $this->ensureLocalhost();

            //Check if the requested Music ID exists
            $music = Music::find($music);
            if($music == null || !$music) {
                throw new Exception(__('Music with this ID does not exist'));
            }

            $music->save(array(
                'status' => Music::STATUS_PROCESSING
            ));

            //Make sure the file is readable
            $root_path = $this->getPath(App::path() . '/' . App::module('system/finder')->config('storage'));
            $filepath = $root_path . 'temp/' . $music->filename;

            if (!is_readable($filepath)) {
                throw new Exception(__('Cannot read music file '.$filepath.', Permission denied.'));
            }

            //Make sure this file hasn't already been uploaded
            $calculated = hash_file('crc32b', $filepath);
            if(Music::where(['(status = ' . Music::STATUS_FINISHED . ' OR status = ' . Music::STATUS_PROCESSING . ') AND crc = :hash'], ['hash' => $calculated])->count() > 0) {
                $music->save(array(
                    status => Music::STATUS_DUPLICATE
                ));

                unlink($filepath);
                throw new Exception(__('File with this hash already exists'));
            }

            //Start conversion
            $outputPath = $root_path . $music->filename.'.ogg';

            $process = new Process('avconv -i ' . $filepath . ' -acodec libvorbis -vcodec libtheora '. $outputPath);
            $process->start();

            $lastrun_path = $root_path . 'last_run.txt';

            while($process->isRunning()) {
                file_put_contents($lastrun_path, time());
                sleep(1);
            }

            var_dump($outputPath);
            var_dump(file_exists($outputPath));

            //If the new file has been generated, delete the old one
            if(file_exists($outputPath)) {

                $music->save(array(
                    'status' => Music::STATUS_FINISHED
                ));

                unlink($filepath);
            } else {
                $music->save(array(
                    'status' => Music::STATUS_ERROR
                ));
            }

            return array('result' => true);

        } catch(Exception $e) {
            App::abort(400, $e->getMessage());
        }
    }

    /**
     * Make sure the API commands are run by the server only
     * @todo make this a bit more secure rather then a localhost only check..
     * @throws Exception
     */
    protected function ensureLocalhost()
    {
        return true;

        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            throw new \Exception("This API can only be accessed by the server!");
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