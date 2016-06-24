<?php

namespace Xorinzor\Shoutzor\App;

class Liquidsoap {

	private $socketLocation; //must have www-data:www-data permission
    private $socket;

    public function __construct($socketLocation) {
        $this->socketLocation = $socketLocation;
        $this->socket = $this->createSocket();
    }

    public function __destruct() {
        socket_close($this->socket);
    }

    private function createSocket() {
        $this->socket = socket_create(AF_UNIX,SOCK_STREAM,0);

		if($this->socket == FALSE) {
			throw new Exception("Unable to create socket: " . socket_strerror(socket_last_error()));
		}

		if(!socket_connect($this->socket, $this->socketLocation, null)) {
			throw new Exception('Unable to connect to '. $this->socketLocation . socket_strerror(socket_last_error()));
		}
    }

    private function sendCommand($command, $failed = false) {
		$msg = "$command\n\0";
		$length = strlen($msg);
		$retval = array();
		$sent = socket_write($this->socket,$msg,$length);

		if($sent === false)
        {
			throw Exception("Unable to write to socket: " .socket_strerror(socket_last_error()));
			return false;
		}

		if($sent < $length)
        {
            //Not everything got through, resending
			$msg = substr($msg, $sent);
			$length -= $sent;

            //Cancel current transaction
            socket_write($this->socket,"exit\n\0",$length);

            //This command failed before, and did again now
            //To prevent a loop, return null
            if($failed) {
                return null;
            } else {
                //This was the first attempt at the command
                //Re-executing the command
                return $this->sendCommand($command, true);
            }
		}
        else
        {
			while ($buffer = socket_read($this->socket, 4096, PHP_NORMAL_READ))
            {
                //Liquidsoap send an END\r message for each interaction
				if ($buffer == "END\r")
                {
					socket_write($this->socket,"exit\n\0",$length);
					break;
				}

				$retval[] = trim($buffer);
			}

			return $retval;
		}
    }

    public function command($cmd) {
		try {
    		return $this->sendCommand($cmd);
    	} catch(Exception $e) {
    		debug::addLine("DEBUG", $e->getMessage(), __FILE__, __LINE__);
    		return false;
    	}
    }

	public function isRunning() {
		return ($this->command('uptime', $customsocket) !== false);
	}

    public function setVolume($volume) {
    	return $this->command('sound.volume 0 '.$volume);
    }

	public function nextTrack() {
    	return $this->command('shoutzorqueue.skip');
    }

    public function requestTrack($filename) {
    	return $this->command('shoutzorqueue.push '.$filename);
    }

    public function isUp() {
		return $this->command('uptime', $customsocket);
    }

}
