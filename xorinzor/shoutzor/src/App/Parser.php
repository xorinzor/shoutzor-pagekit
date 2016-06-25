<?php

namespace Xorinzor\Shoutzor\App;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Music;

use getID3;

require_once(__DIR__ . '/../Vendor/getid3/getid3.php');

class Parser {

    private $id3;
    private $musicDir;
    private $tempMusicDir;

    public function __construct() {
        $this->id3 = new getID3();
        $this->musicDir = App::module('shoutzor')->config('shoutzor')['musicDir'];
        $this->tempMusicDir = $this->musicDir . '/temp';
    }

    public function getMusicDir() {
        return $this->musicDir;
    }

    public function getTempMusicDir() {
        return $this->tempMusicDir;
    }

    public function parse(Music &$music) {
        $music->hash = $this->calculateHash($music->file);

        //It's a duplicate, remove it and return the result code
        if($existing = $this->exists($music)) {
            //Remove the temporary file
            unlink($this->tempMusicDir . '/' . $music->filename);

            //Return the duplicate statuscode
            return Music::STATUS_DUPLICATE;
        }

        //Analyze the duration of the media file
        $music->duration = $this->getDuration($music);

        //Not a duplicate, move the file from the temp to the permanent directory.
        //Until a file finishes parsing completely, the file will never be moved to the permanent directory
        rename($this->tempMusicDir . '/' . $music->filename, $this->musicDir . '/' . $music->filename);

        //Return the finished statuscode
        return Music::STATUS_FINISHED;
    }

    /**
     * Checks if the provided instance is unique
     * @return false|Music
     */
    public function exists(Music $music) {
        $obj = Music::where('crc = :hash AND status = :status', ['hash' => $music->hash, 'status' => Music::STATUS_FINISHED]);

        if($obj->count() == 0) {
            return false;
        }

        return $obj->first();
    }

    /**
     * Calculates the hash of a file
     */
    public function calculateHash($file) {
        return hash_file('crc32', $file);
    }

    /**
     * Calculates the duration (in seconds) of a file
     */
    public function getDuration(Music $music) {
        $info = $this->id3->analyze($this->tempMusicDir . '/' . $filename);
        $time = $info['playtime_string'];
        $duration = explode(":", $time);

        if(isset($duration[2])) {
            $duration_in_seconds = $duration[0] * 3600 + $duration[1] * 60 + round($duration[2]);
        } else {
            $duration_in_seconds = $duration[0] * 3600 + $duration[1] * 60;
        }

        return $duration_in_seconds;
    }

}
