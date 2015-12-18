<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Music;
use Xorinzor\Shoutzor\Model\Vlc;
use Xorinzor\Shoutzor\Model\Telnet;
use \Exception;

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
     * @Route("/addrequest", methods="POST")
     * @Request({"music": "int"})
     */
    public function addrequestAction($music = 0)
    {
        try {
            if(!$music) {
                throw new Exception(__("Invalid request ID"));
            }

            $this->ensureLocalhost();

            $music = Music::find($music);
            if($music == null || !$music) {
                throw new Exception(__('Music with this ID does not exist'));
            }

            $filepath = $music->filename;

            if (!is_readable($filepath)) {
                throw new Exception(__('Cannot read music file '.$filepath.', Permission denied.'));
            }

            $this->connectToTelnet();
            $output = $this->telnet->exec(Vlc::getCommandText(Vlc::CONTROL_ENQUEUE) . " $filepath");

            return array('result' => true, 'output' => $output);

        } catch(Exception $e) {
            App::abort(400, $e->getMessage());
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

    /**
     * Rebuilds the VLC script with the config parameters as provided in the admin panel
     */
    protected function buildVlcConfig() {
        try {
            $config = App::module('shoutzor')->config['vlc'];

            $settings = array(
                'placeholder' => $config['stream']['video']['placeholder'],
                'logo' => $config['stream']['video']['logo']['path'],
                'logo_transparency' => $config['stream']['video']['logo']['transparency'],
                'logo_x_pos' => $config['stream']['video']['logo']['x'],
                'logo_y_pos' => $config['stream']['video']['logo']['y'],
                'telnetport' => $config['telnet']['port'],
                'telnetpassword' => $config['telnet']['password'],
                'threads' => $config['transcoding']['threads'],
                'vcodec' => $config['transcoding']['vcodec'],
                'acodec' => $config['transcoding']['acodec'],
                'videoquality' => $config['transcoding']['videoquality'],
                'audioquality' => $config['transcoding']['audioquality'],
                'bitrate' => $config['transcoding']['bitrate'],
                'width' => $config['video']['width'],
                'height' => $config['video']['height'],
                'output_destination' => $config['output']['host'],
                'output_port' => $config['output']['port'],
                'output_mount' => $config['output']['mount'],
                'output_password' => $config['output']['password']
            );

            $command_template = <<<EOT
        vlc "\$placeholder" \\
        --ttl 12 \\
        --one-instance \\
        --intf telnet \\
        --telnet-port=\$telnetport \\
        --telnet-password=\$telnetpassword \\
        --loop \\
        --quiet \\
        --sout-theora-quality=\$videoquality \\
        --sout-vorbis-quality=\$audioquality \\
        --sout "#transcode{sfilter=logo{file='\$logo',x=\$logo_x_pos,y=\$logo_y_pos,transparency=\$logo_transparency},deinterlace,hq,threads=\$threads,vcodec=\$vcodec,acodec=\$acodec,ab=\$bitrate,channels=2,width=\$width,height=\$height}:std{access=shout,mux=ogg,dst=source:\$output_password@\$output_destination:\$output_port/\$output_mount}" --sout-keep
EOT;

            $script = '';

            //Add the configuration options
            foreach($settings as $setting=>$value) {
                $script .= $setting . '=' . $value . PHP_EOl;
            }

            //Add the template for the command to the script
            $script .= $command_template;

            $path = $this->getPath(App::module('shoutzor')->config('root_path')) . 'scripts/vlc.sh';

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
        $this->telnet = new Telnet('localhost', '4212', 5);

        if($this->telnet->login('password') != Telnet::TELNET_OK) {
            throw new \Exception("Could not authenticate to the VLC telnet interface");
        }
    }

    protected function getPath($path = '')
    {
        $root = strtr(App::path(), '\\', '/');
        $path = $this->normalizePath($root.'/'.App::request()->get('root').'/'.App::request()->get('path').'/'.$path);

        if(substr($path, -1) !== '/') {
            $path .= '/';
        }

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