<?php

namespace Xorinzor\Shoutzor\Controller;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Music;
use Xorinzor\Shoutzor\Model\Vlc;
use Xorinzor\Shoutzor\Model\Telnet;
use \Exception;

/**
 * @Route("vlc", name="vlc")
 */
class VlcApiController
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
            $output = $this->telnet->exec(Vlc::CONTROL_ENQUEUE . " $filepath");

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
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            throw new \Exception("This API can only be accessed by the server!");
        }
    }

    /**
     * Connect to the VLC Telnet interface
     */
    protected function connectToTelnet() {
        $this->telnet = new Telnet('localhost', '4212', 5, '$');
        $this->telnet->connect();
        if($this->telnet->login('password') != Telnet::TELNET_OK) {
            throw new \Exception("Could not authenticate to the VLC telnet interface");
        }
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