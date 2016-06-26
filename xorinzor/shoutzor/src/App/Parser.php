<?php

namespace Xorinzor\Shoutzor\App;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Media;

use getID3;

require_once(__DIR__ . '/../Vendor/getid3/getid3.php');

class Parser {

    private $id3;
    private $mediaDir;
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

    public function parse(Media &$media) {
        //If a media file is already finished there is no point in parsing it
        if($media->status === Media::STATUS_FINISHED) {
            return Media::STATUS_FINISHED;
        }

        $media->hash = $this->calculateHash($this->tempMusicDir . '/' . $media->filename);

        //It's a duplicate, remove it and return the result code
        if($existing = $this->exists($media)) {
            //Remove the temporary file
            unlink($this->tempMusicDir . '/' . $media->filename);

            //Return the duplicate statuscode
            return Media::STATUS_DUPLICATE;
        }

        //Analyze the duration of the media file
        $media->duration = $this->getDuration($media);

        //Not a duplicate, move the file from the temp to the permanent directory.
        //Until a file finishes parsing completely, the file will never be moved to the permanent directory
        rename($this->tempMusicDir . '/' . $media->filename, $this->musicDir . '/' . $media->filename);

        //Return the finished statuscode
        return Media::STATUS_FINISHED;
    }

    /**
     * Checks if the provided instance is unique
     * @return false|Music
     */
    public function exists(Media $media) {
        $obj = Media::where('crc = :hash AND status = :status', ['hash' => $media->hash, 'status' => Media::STATUS_FINISHED]);

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
    public function getDuration(Media $media) {
        $info = $this->id3->analyze($this->tempMusicDir . '/' . $media->filename);
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
