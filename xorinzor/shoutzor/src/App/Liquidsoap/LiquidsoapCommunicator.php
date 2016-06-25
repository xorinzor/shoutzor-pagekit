<?php

namespace Xorinzor\Shoutzor\App\Liquidsoap;

use Exception;

class LiquidsoapCommunicator {

	private $socketLocation; //must have www-data:www-data permission
    private $socket;

    public function __construct($socketLocation) {
        $this->socketLocation = $socketLocation;

		try {
	        $this->socket = $this->createSocket();
		} catch(Exception $e) {
			throw $e;
		}
    }

    public function __destruct() {
		if($this->socket !== null) {
        	socket_close($this->socket);
		}
    }

    private function createSocket() {
        $sock = socket_create(AF_UNIX, SOCK_STREAM, 0);

		if($sock == FALSE) {
			throw new Exception("Unable to create socket: " . socket_strerror(socket_last_error($sock)));
		}

		if(!socket_connect($sock, $this->socketLocation, null)) {
			throw new Exception('Unable to connect to '. $this->socketLocation . socket_strerror(socket_last_error($sock)));
		}

		return $sock;
    }

    private function sendCommand($command, $failed = false) {
		$msg = "$command\n\0";
		$length = strlen($msg);
		$retval = array();
		$sent = socket_write($this->socket,$msg,$length);

		if($sent === false)
        {
			throw Exception("Unable to write to socket: " .socket_strerror(socket_last_error($this->socket)));
			return false;
		}

		if($sent < $length)
        {
            //Not everything got through, resending
			$msg = substr($msg, $sent);
			$length -= $sent;

            //Cancel current transaction
            socket_write($this->socket, "exit\n\0", $length);

            //This command failed before, and did again now
            //To prevent a loop, return false
            if($failed) {
                return false;
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
					socket_write($this->socket, "exit\n\0", $length);
					break;
				}

				$retval[] = trim($buffer);
			}

			return $retval;
		}
    }

    public function command($cmd) {
		if($this->socket == null) {
			return false;
		}

		try {
    		return $this->sendCommand($cmd);
    	} catch(Exception $e) {
    		return false;
    	}
    }
}
