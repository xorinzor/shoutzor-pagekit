<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Music;
use Xorinzor\Shoutzor\Model\Vlc;
use Xorinzor\Shoutzor\Model\Telnet;
use Xorinzor\Shoutzor\Model\Request;
use \Exception;
use Symfony\Component\Process\Process;

/**
 * @Route("vlcmanager", name="vlcmanager")
 */
class VlcmanagerApiController
{
    private $telnet;

    /**
     * @Route("/", methods="GET")
     */
    public function indexAction()
    {
        try {
            $this->ensureLocalhost();

            $this->connectToTelnet();

            /* return server status */
            return array('result' => true);
        } catch(Exception $e) {
            App::abort(400, $e->getMessage());
        }
    }

    /**
     * @Route("/start", methods="GET")
     */
    public function startAction() {
        if($this->isRunning()) {
            return array('result' => false);
        }

        $this->buildVlcConfig();

        $path = $this->getPath(App::module('shoutzor')->config('root_path')) . 'assets/scripts/';

        $process = new Process($path.'vlc.sh > /dev/null 2>&1 &');
        $process->run();

        return array('result' => true);
    }

    /**
     * @Route("/addrequest", methods="POST")
     * @Request({"music": "int"})
     */
    public function addrequestAction($music = 0, $force = false)
    {
        try {
            if(!$music) {
                throw new Exception(__("Invalid request ID"));
            }

            //Make sure file uploads are enabled
            if(!App::user()->hasAccess("shoutzor: add requests")) {
                throw new Exception(__('You have no permission to request'));
            }

            //Make sure file uploads are enabled
            if(App::module('shoutzor')->config('shoutzor.request') == 0) {
                throw new Exception(__('File requests have been disabled'));
            }

            //Check if the requested Music ID exists
            $music = Music::find($music);
            if($music == null || !$music) {
                throw new Exception(__('Music with this ID does not exist'));
            }

            //Make sure the file is readable
            $root_path = $this->getPath(App::path().'/'.App::module('system/finder')->config('storage'));
            $filepath = $root_path . $music->filename;

            if (!is_readable($filepath)) {
                throw new Exception(__('Cannot read music file '.$filepath.', Permission denied.'));
            }

            if($force === false) {
                $isRequestable = (Request::where(['music_id = :id AND requesttime < NOW() - INTERVAL 30 MINUTE'], ['id' => $music->id])->count() > 0) ? false : true;
                if (!$isRequestable) {
                    throw new Exception(__('This song has been requested too recently'));
                }

                $canRequest = (Request::where(['requester_id = :id AND requesttime < NOW() - INTERVAL 10 MINUTE'], ['id' => App::user()->id])->count() > 0) ? false : true;
                if (!$canRequest) {
                    throw new Exception(__('You already recently requested a song, try again in 10 minutes'));
                }
            }

            //Add request to the playlist
            $this->connectToTelnet();

            //Add the request to the playlist
            try {
                $this->telnet->exec(Vlc::getCommandText(Vlc::CONTROL_ENQUEUE) . " $filepath");
            } catch(Exception $e) {
                //It works, just ignore it.
            }

            //Save request in the database
            $request = Request::create();
            $request->save(array(
                'music_id' => $music->id,
                'requester_id' => App::user()->id,
                'requesttime' => (new \DateTime())->format('Y-m-d H:i:s')
            ));

            return array('result' => true);

        } catch(Exception $e) {
            return array('result' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * @Route("/control", methods="POST")
     * @Request({"action": "int"})
     */
    public function controlAction($action = 0)
    {
        try {
            $this->ensureLocalhost();

            $this->connectToTelnet();
            $output = $this->telnet->exec(Vlc::getCommandText($action));

            /* return server status */
            return array('result' => true, 'output' => $output);
        } catch(Exception $e) {
            App::abort(400, $e->getMessage());
        }
    }

    /**
     * @Route("/autorequester", methods="GET")
     */
    public function autorequesterAction() {
        $last_request = Request::where('1=1')->orderBy('requesttime', 'DESC')->related(['user', 'music'])->limit(1)->get();

        $last_request_time = strtotime($last_request[0]->requesttime->date);
        $request_end_time = $last_request_time + strtotime($last_request[0]->music->duration);

        $recent = Request::where('requesttime < NOW() - INTERVAL 10 MINUTE')->get();
        $builder = '';
        foreach($recent as $item) {
            $builder .= $item->id.',';
        }

        $builder = rtrim($builder, ",");


        $random = Music::where('id NOT IN('.$builder.')')->orderBy('RAND()')->limit(1);
        if($random->count() == 0) {
            $random = Music::where('1=1')->orderBy('RAND()')->limit(1);
        }

        $song = $random->get();
        $this->addrequestAction($song->id, true);

        return array('result' => true);
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

    protected function isRunning() {
        try {
            $this->connectToTelnet();
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    /**
     * Rebuilds the VLC script with the config parameters as provided in the admin panel
     */
    protected function buildVlcConfig() {
        try {
            $config = App::module('shoutzor')->config['vlc'];

            $settings = array(
                'placeholder' =>            '"'.$config['stream']['video']['placeholder'].'"',
                'logo' =>                   '"'.$config['stream']['video']['logo']['path'].'"',
                'logo_transparency' =>      $config['stream']['video']['logo']['transparency'],
                'logo_x_pos' =>             $config['stream']['video']['logo']['x'],
                'logo_y_pos' =>             $config['stream']['video']['logo']['y'],
                'telnetport' =>             $config['telnet']['port'],
                'telnetpassword' =>         '"'.$config['telnet']['password'].'"',
                'threads' =>                $config['transcoding']['threads'],
                'vcodec' =>                 $config['transcoding']['vcodec'],
                'acodec' =>                 $config['transcoding']['acodec'],
                'videoquality' =>           $config['transcoding']['videoquality'],
                'audioquality' =>           $config['transcoding']['audioquality'],
                'bitrate' =>                $config['transcoding']['bitrate'],
                'width' =>                  $config['stream']['video']['width'],
                'height' =>                 $config['stream']['video']['height'],
                'output_destination' =>     '"'.$config['stream']['output']['host'].'"',
                'output_port' =>            $config['stream']['output']['port'],
                'output_mount' =>           '"'.$config['stream']['output']['mount'].'"',
                'output_password' =>        '"'.$config['stream']['output']['password'].'"'
            );

            $command_template = "\n\n" . 'vlc "$placeholder" --ttl=12 --one-instance --intf=telnet --telnet-port=$telnetport --telnet-password=$telnetpassword --loop --quiet --sout-theora-quality=$videoquality --sout-vorbis-quality=$audioquality --sout "#transcode{sfilter=logo{file=\'$logo\',x=$logo_x_pos,y=$logo_y_pos,transparency=$logo_transparency},deinterlace,hq,threads=$threads,vcodec=$vcodec,acodec=$acodec,ab=$bitrate,channels=2,width=$width,height=$height}:std{access=shout,mux=ogg,dst=source:$output_password@$output_destination:$output_port/$output_mount}" --sout-keep';

            $script = '#!/bin/bash'."\n\n";

            //Add the configuration options
            foreach($settings as $setting=>$value) {
                $script .= $setting . '=' . $value . "\n";
            }

            //Add the template for the command to the script
            $script .= $command_template;

            $path = $this->getPath(App::module('shoutzor')->config('root_path')) . 'assets/scripts/vlc.sh';

            if (!is_writable($path)) {
                throw new Exception(__('Cannot edit VLC.sh file at ' . $path . ', Permission denied.'));
            }

            //Write to the script
            file_put_contents($path, $script, LOCK_EX);

            return array('result' => true);

        } catch(Exception $e) {
            App::abort(400, $e->getMessage());
        }
    }

    /**
     * Connect to the VLC Telnet interface
     */
    protected function connectToTelnet() {
        $config = App::module('shoutzor')->config['vlc'];

        $this->telnet = new Telnet('localhost', $config['telnet']['port'], 5);

        $login = $this->telnet->login($config['telnet']['password']);

        if($login != Telnet::TELNET_OK) {
            throw new \Exception("Could not authenticate to the VLC telnet interface");
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