<?php
namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;

use Xorinzor\Shoutzor\Model\Media;
use Xorinzor\Shoutzor\Model\Request;
use Xorinzor\Shoutzor\App\Parser;
use Xorinzor\Shoutzor\App\Liquidsoap\LiquidsoapManager;

use ReflectionMethod;
use Exception;
use DateTime;
use DateInterval;

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
        //Check whether the param values should be fetched from GET or POST
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
        //Make sure it's the server that requested this method
        if($this->ensureLocalhost() === false) {
            return $this->formatOutput(__('You have no access to this method'), self::METHOD_NOT_AVAILABLE);
        }

        //Get our configuration values
        $config = App::module('shoutzor')->config('shoutzor');
        $lastRun = $config['parserLastRun'];

        //Check if the last-run time was more then 1 minute ago to make sure no other parser processes are running
        if($lastRun > strtotime("-1 minute")) {
            return $this->formatOutput(__('A parser is already running or has been running too recently'), self::ERROR_IN_REQUEST);
        }

        //Get a list of items that need to be parsed
        $toParse = Media::where(['status = :status'], ['status' => Media::STATUS_UPLOADED])
                    ->where(['status = :status'], ['status' => Media::STATUS_ERROR])
                    ->limit($config['parserMaxItems'])->get();

        //Parse each item
        foreach($toParse as $item) {
            //Update the parserLastRun value to the current time
            App::config('shoutzor')->set('shoutzor', ['parserLastRun' => time()]);

            //Parse the item
            $this->parse(['id' => $item->id]);
        }

        //Return true
        return $this->formatOutput(true);
    }

    /**
     * Parses and converts music files
     * @method parse
     * @param id the ID of the media object to parse
     */
    public function parse($params)
    {
        if($this->ensureLocalhost() === false) {
            return $this->formatOutput(__('You have no access to this method'), self::METHOD_NOT_AVAILABLE);
        }

        if(FormValidation::isDigit($params['id'])) {
            return $this->formatOutput(__('Not a valid numerical value provided for the media ID'), self::INVALID_PARAMETER_VALUE);
        }

        //Fetch media object with provided ID
        $media = Media::find($params['id']);

        //Make sure the requested media object exists
        if(!$media instanceof Media) {
            return $this->formatOutput(__('No media object with the provided ID exists'), self::ITEM_NOT_FOUND);
        }

        if($media->status === Media::STATUS_FINISHED) {
            return $this->formatOutput(__('This media object has already been parsed'), self::ERROR_IN_REQUEST);
        }

        //Set our media file to the processing status
        $media->save(array(
            'status' => Media::STATUS_PROCESSING
        ));

        //Initialize our parser class
        $parser = new Parser();

        //Our main storage path
        $path = $parser->getMediaDir();

        //Get the path from the file in the temporary Directory
        $filepath = $parser->getTempMediaDir() . '/' . $media->filename;

        //Make sure our root path exists and is writable
        if((!is_dir($path) && !mkdir($path)) ||  !is_writable($path)) {
            return $this->formatOutput(__('Directory '.$path.' is not writable, Permission denied'), self::ERROR_IN_REQUEST);
        }

        //Make sure our file exists
        if (!file_exists($filepath)) {
            return $this->formatOutput(__('Media file '.$filepath.' does not exist'), self::ERROR_IN_REQUEST);
        }

        //Make sure our file is readable
        if (!is_readable($filepath)) {
            return $this->formatOutput(__('Cannot read media file '.$filepath.', Permission denied'), self::ERROR_IN_REQUEST);
        }

        //Since its just an audio file, parse immediately
        $media->status = $parser->parse($media);

        //If the parse succeeded, save it in the database
        if($media->status == Media::STATUS_FINISHED || $media->status == Media::STATUS_ERROR) {
            $media->save();
        }

        //If the parse failed, the status will be set to the relevant code. Also no need to save the record

        /*
            @TODO
            Perhaps implement some form of converting to mp3 here?
            Or preserve this method for downloading of youtube videos, and converting those to mp3's
        */

        return $this->formatOutput((array) $media);
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

        //Initialize our parser class
        $parser = new Parser();

        //Our temporary storage path
        $path = $parser->getTempMediaDir();

        //Make sure our temporary directory exists and is writable
        if((!is_dir($path) && !mkdir($path)) || !is_writable($path)) {
            return $this->formatOutput(__('Directory '.$path.' is not writable, Permission denied'), self::ERROR_IN_REQUEST);
        }

        //Get the uploaded file
        $file = App::request()->files->get('musicfile');

        //Make sure the uploaded file is uploaded correctly
        if($file === null) {
            return $this->formatOutput(__('No file has been uploaded with name: musicfile'), self::INVALID_PARAMETER_VALUE);
        }

        if($file->isValid() === false) {
            return $this->formatOutput(__('The uploaded file has not been uploaded correctly'), self::INVALID_PARAMETER_VALUE);
        }

        $filename = md5(uniqid("", true)).'.'.$file->getClientOriginalName();

        //Save the file into our temporary directory
        $file->move($path, $filename);

        $media = Media::create([
            'title' => $file->getClientOriginalName(),
            'artist_id' => 0,
            'filename' => $filename,
            'uploader_id' => App::user()->id,
            'created' => new DateTime(),
            'status' => Media::STATUS_UPLOADED,
            'amount_requested' => 0,
            'crc' => '',
            'duration' => 0
        ]);

        //Since its just an audio file, parse immediately
        $media->status = $parser->parse($media);

        //If the parse succeeded, save it in the database
        if($media->status == Media::STATUS_FINISHED || $media->status == Media::STATUS_ERROR) {
            $media->save();
        }

        //If the parse failed, the status will be set to the relevant code. Also no need to save the record

        //No problems, return result
        return $this->formatOutput((array) $media);
    }

    /**
     * Handles the file uploads
     * @method request
     * @param id the ID from the media file thats requested
     */
    public function request($params) {
        //Make sure file uploads are enabled
        if(!App::user()->hasAccess("shoutzor: add requests")) {
            return $this->formatOutput(__('You have no permission to request'), self::METHOD_NOT_AVAILABLE);
        }

        //Make sure file uploads are enabled
        if(App::module('shoutzor')->config('shoutzor.request') == 0) {
            return $this->formatOutput(__('File requests have been disabled'), self::METHOD_NOT_AVAILABLE);
        }

        //Validate the parameter value
        if(!is_numeric($params['id'])) {
            return $this->formatOutput(__('Not a valid numerical value provided for the media object ID'), self::INVALID_PARAMETER_VALUE);
        }

        //Check if the requested Music ID exists
        $media = Media::find($params['id']);
        if($media == null || !$media) {
            return $this->formatOutput(__('No media object with the provided ID exists'), self::ITEM_NOT_FOUND);
        }

        //Get the config options
        $config = App::module('shoutzor')->config('shoutzor');

        //Get the path to the file
        $filepath = $config['mediaDir'] . '/' . $media->filename;

        //Make sure the file is readable
        if (!is_readable($filepath)) {
            return $this->formatOutput(__('Cannot read music file '.$filepath.', Permission denied.'), self::ERROR_IN_REQUEST);
        }

        $canRequestDateTime = (new DateTime())->sub(new DateInterval('PT'.$config['mediaRequestDelay'].'M'))->format('Y-m-d H:i:s');

        //Check if the song hasnt been requested too soon ago
        $isRequestable = (Request::where('media_id = :id AND requesttime > :requesttime', ['id' => $media->id, 'requesttime' => $canRequestDateTime])->count() == 0) ? true : false;
        if ($isRequestable === false) {
            return $this->formatOutput(__('This song has been requested too recently'), self::ERROR_IN_REQUEST);
        }

        $canRequestDateTime = (new DateTime())->sub(new DateInterval('PT'.$config['userRequestDelay'].'M'))->format('Y-m-d H:i:s');

        //Check if the user hasnt already recently requested a song
        $canRequest = (Request::where('requester_id = :user AND requesttime > :requesttime', ['user' => App::user()->id, 'requesttime' => $canRequestDateTime])->count() == 0) ? true : false;
        if ($canRequest === false) {
            return $this->formatOutput(__('You already recently requested a song, try again in 10 minutes'), self::ERROR_IN_REQUEST);
        }

        //Add request to the playlist
        $liquidsoapManager = new liquidsoapManager();
        $liquidsoapManager->queueTrack($filepath);

        //Save request in the database
        $request = Request::create();
        $request->save(array(
            'media_id' => $media->id,
            'requester_id' => App::user()->id,
            'requesttime' => (new \DateTime())->format('Y-m-d H:i:s')
        ));

        return $this->formatOutput(true);
    }

    /**
     * Runs a liquidsoap command
     * @method liquidsoapcommand
     * @param type the type of the script to start
     * @param command the command to run
     * @param options extra options to provide with the command
     */
    public function liquidsoapcommand($params) {
        $liquidsoapManager = new LiquidsoapManager();

        switch($params['command']) {
            case "start":
                $result = $liquidsoapManager->startScript($params['type']);
                break;

            case "stop":
                $result = $liquidsoapManager->stopScript($params['type']);
                break;

            case "volume":
                $result = $liquidsoapManager->setVolume($params['type'], $params['options']);
                break;

            case "next":
                $result = $liquidsoapManager->nextTrack();
                break;

            default:
                return $this->formatOutput(__('Invalid command provided'), self::INVALID_PARAMETER_VALUE);
                break;
        }

        if($result) {
            return $this->formatOutput($result);
        }

        return $this->formatOutput(false, self::ERROR_IN_REQUEST);
    }
}
