<?php
namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

use Xorinzor\Shoutzor\Model\Music;

use ReflectionMethod;
use Exception;
use getID3;

class ApiController
{
    /* API Result codes */
    const CODE_SUCCESS      = 200;
    const CODE_BAD_REQUEST  = 400;
    const CODE_FORBIDDEN    = 403;
    const CODE_NOT_FOUND    = 404;
    const CODE_CANT_PROCESS = 422;

    /* API Result messages */
    const API_CALL_SUCCESS = [
        'code'      => self::CODE_SUCCESS,
        'message'   => 'API call succeeded'
    ];

    const INVALID_PARAMETER_VALUE = [
        'code'      => self::CODE_BAD_REQUEST,
        'message'   => 'Invalid Parameter Value(s) provided'
    ];

    const INVALID_METHOD = [
        'code'      => self::CODE_BAD_REQUEST,
        'message'   => 'Invalid API method'
    ];

    const NO_METHOD_PROVIDED = [
        'code'      => self::CODE_BAD_REQUEST,
        'message'   => 'No API method provided'
    ];

    const INVALID_SECRET = [
        'code'      => self::CODE_FORBIDDEN,
        'message'   => 'Invalid secret token provided'
    ];

    const METHOD_NOT_AVAILABLE = [
        'code'      => self::CODE_FORBIDDEN,
        'message'   => 'You do not have the permissions to access this method'
    ];

    const ITEM_NOT_FOUND = [
        'code'      => self::CODE_NOT_FOUND,
        'message'   => 'The API method could not find the object related to the provided parameters'
    ];

    const ERROR_IN_REQUEST = [
        'code'      => self::CODE_CANT_PROCESS,
        'message'   => 'An error is preventing this method from executing properly'
    ];

    private function formatOutput($data, $result = self::API_CALL_SUCCESS) {
        return [
            'data' => $data,
            'info' => $result
        ];
    }

    /**
     * Make sure the API commands are run by the server only
     * @todo make this a bit more secure rather then a localhost only check..
     * @throws Exception
     */
    protected function ensureLocalhost()
    {
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            throw new Exception("This API can only be accessed by the server!");
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

    /* API Methods */

    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     */
    public function apiAction()
    {
        if(App::request()->isMethod('POST'))
        {
            $params = App::request()->request->all();
        }
        else
        {
            $params = App::request()->query->all();
        }

        //If no method is provided, return false
        if(isset($params['method']) === false || is_null($params['method'])) {
            return $this->formatOutput(false, self::NO_METHOD_PROVIDED);
        }

        $method = $params['method'];

        /**
        * Check if the method exists
        */
        if (method_exists($this, $method))
        {
            $reflection = new ReflectionMethod($this, $method);

            //Valid callable methods are "public", anything else is protected or private
            if ($reflection->isPublic()) {
                return $this->$method($params);

            }
        }

        //The above validation check failed, return the invalid method response
        return $this->formatOutput(false, self::INVALID_METHOD);
    }

    /**
     * Automatically parses music files that have not been parsed yet
     * @method autoparse
     */
    public function autoparse() {
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

            return $this->formatOutput(true);

        } catch(Exception $e) {
            return $this->formatOutput($e->getMessage(), self::ERROR_IN_REQUEST);
        }
    }

    /**
     * Parses and converts music files
     * @method parse
     * @param music the ID of the music object to parse
     */
    public function parse($music = 0)
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

            //Our main storage path
            $root_path = $this->getPath(App::path() . '/' . App::module('system/finder')->config('storage'));

            //And a temporary directory within the storage
            $filepath = $root_path  . 'temp/' . $music->filename;

            //Make sure our root path exists and is writable
            if((!is_dir($root_path) && !mkdir($root_path)) ||  !is_writable($root_path)) {
                throw new Exception(__('Directory '.$root_path.' is not writable, Permission denied'));
            }

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
                throw new Exception(__('This song has already been uploaded'));
            }

            /*
            Perhaps implement some form of converting to mp3 here?
            Or preserve this method for downloading of youtube videos, and converting those to mp3's

            $music->save(array(
                'status' => Music::STATUS_ERROR
            ));
            */

            $music->save(array(
                'status' => Music::STATUS_FINISHED
            ));

            return $this->formatOutput(true);

        } catch(Exception $e) {
            return $this->formatOutput($e->getMessage(), self::ERROR_IN_REQUEST);
        }
    }

    /**
     * Handles the file uploads
     * @method upload
     * @param musicfile the file that is beeing uploaded
     */
    public function upload($params)
    {
        //Make sure file uploads are enabled
        if(App::module('shoutzor')->config('shoutzor.upload') == 0) {
            return $this->formatOutput(__('File uploads have been disabled'), self::METHOD_NOT_AVAILABLE);
        }

        //Make sure file uploads are enabled
        if(!App::user()->hasAccess("shoutzor: upload files")) {
            return $this->formatOutput(__('You have no permission to upload files'), self::METHOD_NOT_AVAILABLE);
        }

        //Our main storage path
        $root_path = $this->getPath(App::path() . '/' . App::module('system/finder')->config('storage'));

        //And a temporary directory within the storage
        $temp_path = $root_path  . 'temp/';

        //Make sure our root path exists and is writable
        if((!is_dir($root_path) && !mkdir($root_path)) ||  !is_writable($root_path)) {
            return $this->formatOutput(__('Directory '.$root_path.' is not writable, Permission denied'), self::ERROR_IN_REQUEST);
        }

        //Make sure our temporary directory exists and is writable
        if((!is_dir($temp_path) && !mkdir($temp_path)) || !is_writable($temp_path)) {
            return $this->formatOutput(__('Directory '.$temp_path.' is not writable, Permission denied'), self::ERROR_IN_REQUEST);
        }

        //Get the uploaded file
        $file = App::request()->files->get('musicfile');

        //If no file is uploaded
        if ($file === null) {
            return $this->formatOutput(__('The uploaded file is not valid'), self::INVALID_PARAMETER_VALUE);
        }

        //Make sure the uploaded file is uploaded correctly
        if($file->isValid() === false) {
            return $this->formatOutput(__('The uploaded file has not been uploaded correctly'), self::INVALID_PARAMETER_VALUE);
        }

        $filename = md5(uniqid()).'.'.$file->getClientOriginalName();

        //Save the file into our temporary directory
        $file->move($temp_path, $filename);

        $music = Music::create();
        $music->save(array(
            'title' => $file->getClientOriginalName(),
            'artist_id' => 0,
            'filename' => $filename,
            'uploader_id' => App::user()->id,
            'created' => (new \DateTime())->format('Y-m-d H:i:s'),
            'status' => Music::STATUS_UPLOADED,
            'amount_requested' => 0,
            'crc' => '',
            'duration' => 0
        ));

        $fileId = $music->id;

        //Prevent Divide by zero error
        $filesize = ($file->getClientSize() == 0) ? 1 : $file->getClientSize();

        $result = array(
            'id' => $fileId,
            'filename' => $file->getClientOriginalName(),
            'size' => $filesize / (1024 * 1024) //Filesize in MB
        );

        //No problems, return result
        return $this->formatOutput((array) $music);
    }

    /**
     * THE OLD UPLOAD METHOD, PARTS OF THIS NEED TO BE MOVED TO THE PARSE METHOD / CLASS
     */
    public function uploadOld($params)
    {
        try {
            require_once(__DIR__ . '/../Vendor/getid3/getid3.php');

            //Make sure file uploads are enabled
            if(App::module('shoutzor')->config('shoutzor.upload') == 0) {
                throw new Exception(__('File uploads have been disabled'));
            }

            //Make sure file uploads are enabled
            if(!App::user()->hasAccess("shoutzor: upload files")) {
                throw new Exception(__('You have no permission to upload files'));
            }

            //Our main storage path
            $root_path = $this->getPath(App::path() . '/' . App::module('system/finder')->config('storage'));

            //And a temporary directory within the storage
            $path = $root_path  . 'temp/';

            //Make sure our root path exists and is writable
            if((!is_dir($root_path) && !mkdir($root_path)) ||  !is_writable($root_path)) {
                throw new Exception(__('Directory '.$root_path.' is not writable, Permission denied'));
            }

            //Make sure our temporary directory exists and is writable
            if((!is_dir($path) && !mkdir($path)) || !is_writable($path)) {
                throw new Exception(__('Directory '.$path.' is not writable, Permission denied'));
            }

            //Get the uploaded file
            $file = App::request()->files->get('musicfile');

            //If no file is uploaded, throw error
            if ($file === null) {
                throw new Exception(__('No file uploaded'));
            }

            if($file->isValid()) {
                $filename = md5(uniqid()).'.'.$file->getClientOriginalName();
                $file->move($path, $filename);

                $exists = false;
                $crc = hash_file('crc32', $path . $filename);
                $music = Music::where(['crc = :hash'], ['hash' => $crc]);

                if($music->count() > 0) {
                    $exists = true;
                    $music = $music->first();
                }

                if($exists == false) {
                    //move it from the temp directory to the main storage directory
                    rename($path.$filename, $root_path.$filename);

                    $id3 = new getID3();
                    $info = $id3->analyze($root_path.$filename);
                    $time = $info['playtime_string'];
                    $duration = explode(":", $time);
                    if(isset($duration[2])) {
                        $duration_in_seconds = $duration[0] * 3600 + $duration[1] * 60 + round($duration[2]);
                    } else {
                        $duration_in_seconds = $duration[0] * 3600 + $duration[1] * 60;
                    }
                } else {
                    $duration_in_seconds = $music->duration;
                }

                $music = Music::create();
                $music->save(array(
                    'title' => $file->getClientOriginalName(),
                    'artist_id' => 0,
                    'filename' => $filename,
                    'uploader_id' => App::user()->id,
                    'created' => (new \DateTime())->format('Y-m-d H:i:s'),
                    'status' => ($exists) ? Music::STATUS_DUPLICATE : Music::STATUS_FINISHED,
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

            //No problems, return result
            return $this->formatOutput($result);

        } catch(Exception $e) {
            return $this->formatOutput($e->getMessage(), self::ERROR_IN_REQUEST);
        }
    }
}
