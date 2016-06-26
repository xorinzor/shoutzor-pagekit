<?php

namespace Xorinzor\Shoutzor\App\Liquidsoap;

use Exception;

class LiquidsoapCommunicator {

	private $socketLocation; //must have www-data:www-data permission
    private $socket;

    public function __construct($socketLocation) {
        $this->socketLocation = $socketLocation;
		$this->socket = $this->createSocket();
    }

	public function __destruct() {
		if($this->socket !== null) {
			$this->socketWrite("exit");
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

	/**
	 * Function that writes a message fully to the socket, even if its too long
	 */
	private function socketWrite($message) {
		if($this->socket == null) {
			return false;
		}

		//String terminator
		$message .= "\n";

		//Get the length of our message
		$length = strlen($message);

		while (true) {
			$sent = socket_write($this->socket, $message, $length);

			if ($sent === false) {
				break;
			}

			// Check if the entire message has been sent
			if ($sent < $length) {
				//Not the entire message has been sent yet
				//Get the part of the massage that has yet to be sent
				$message = substr($message, $sent);

				//Get the length of the part that has yet to be sent
				$length -= $sent;
			} else {
				//Everything is sent, exit loop
				break;
			}
		}

		return true;
	}

    private function sendCommand($command, $failed = false) {
		$sent = $this->socketWrite($command);

		if($sent === false) {
			throw Exception("Unable to write to socket: " .socket_strerror(socket_last_error($this->socket)));
			return false;
		}


		$retval = array();

		//Read output
		while ($buffer = socket_read($this->socket, 512, PHP_NORMAL_READ)) {
            //Liquidsoap send an END\r message for each interaction
			if ($buffer == "END\r") {
				break;
			}

			$retval[] = trim($buffer);
		}

		return $retval;
    }

    public function command($cmd) {
		if($this->socket == null) {
			return false;
		}

		try {
    		$result = $this->sendCommand($cmd);
    	} catch(Exception $e) {
			$result = false;
    	}

		return $result;
    }
}
