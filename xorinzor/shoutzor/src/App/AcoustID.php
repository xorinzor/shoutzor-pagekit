<?php
namespace Xorinzor\Shoutzor\App;

use Pagekit\Application as App;
use Symfony\Component\Process\Process;

class AcoustID {

    private $enabled;
    private $appKey;
    private $requirementDir;

    public function __construct() {
        $config = App::module('shoutzor')->config();

        $this->enabled = $config['acoustid']['enabled'];
        $this->appKey = $config['acoustid']['appKey'];
        $this->requirementDir = realpath($config['root_path'] . '/../shoutzor-requirements/acoustid');
    }

    public function isEnabled() {
        return $this->enabled;
    }

    public function getFileFingerprint($file) {
        $output = array();
        $returnCode = 0;

        $process = new Process($this->requirementDir . '/fpcalc ' . $file);
        $process->run();

        //exec($this->requirementDir . '/fpcalc ' . $file, $output, $returnCode);

        //The return code is not 0 (success), return false
        if($returnCode !== 0) {
            return false;
        }

        $output = explode("\n", $process->getoutput());
        $result = array();
        foreach($output as $item) {
            if(empty($item)) continue;

            $temp = explode("=", $item);

            $result[strtolower($temp[0])] = $temp[1];
        }


        return $result;
    }

    public function lookup($duration, $fingerprint) {
        //Make sure AcoustID is enabled
        if($this->enabled === false) {
            return false;
        }

        $url = 'http://api.acoustid.org/v2/lookup?client=' . $this->appKey;
        $url .= '&meta=recordings+releasegroups&duration=' . $duration;
        $url .= '&fingerprint=' . $fingerprint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json')); // Assuming you're requesting JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

        // If using JSON...
        $data = json_decode($response);

        //An error occured.
        if($data->status === "error") {
            //@TODO $data->error->message contains the error information, perhaps log it?
            return false;
        }

        return $data->results[0]->recordings;
    }

    public function getMediaInfo($filename) {
        //Check if AcoustID is enabled
        if($this->enabled === false) {
            return false;
        }

        //Get the fingerprint from the media file
        $data = $this->getFileFingerprint($filename);

        //Errorchecking
        if($data === false) {
            return false;
        }

        //Get matching information for the provided fingerprint
        $data = $this->lookup($data['duration'], $data['fingerprint']);

        //Errorchecking
        if($data === false) {
            return false;
        }

        //Get the media file title
        $info['title'] = isset($data[0]->title) ? $data[0]->title : false; //False means no-data for this tag.

        //Get the media file artists
        if(isset($data[0]->artists)) {
            $info['artist'] = array();
            foreach($data[0]->artists as $artist) {
                $info['artist'][] = $artist->name;
            }
        } else {
            //False means no-data for this tag.
            $info['artist'] = false;
        }

        //Get the media file albums
        //Make sure the group exists in the first element (best-match)
        if(isset($data[0]->releasegroups)) {
            $info['album'] = array();
            foreach($data as $item) {
                //Check for every item if the releasegroups element exists to prevent errors
                if(isset($item->releasegroups)) {
                    foreach($item->releasegroups as $release) {
                        if(strtolower($release->type) !== "album") continue;
                        $info['album'][] = $release->title;
                    }
                }
            }
        } else {
            //False means no-data for this tag.
            $info['album'] = false;
        }

        //Return the results
        return $info;
    }

}
