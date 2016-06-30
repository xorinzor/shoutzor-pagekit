<?php

namespace Xorinzor\Shoutzor\App;

use Pagekit\Application as App;
use Xorinzor\Shoutzor\Model\Media;
use Xorinzor\Shoutzor\Model\Artist;
use Xorinzor\Shoutzor\Model\Album;
use Xorinzor\Shoutzor\App\AcoustID;

use getID3;
use getid3_lib;
use getid3_writetags;

require_once(__DIR__ . '/../Vendor/getid3/getid3.php');
require_once(__DIR__ . '/../Vendor/getid3/write.php');

class Parser {

    private $id3;
    private $mediaDir;
    private $tempMediaDir;

    public function __construct() {
        $this->id3 = new getID3();
        $this->mediaDir = App::module('shoutzor')->config('shoutzor')['mediaDir'];
        $this->tempMediaDir = $this->mediaDir . '/temp';
    }

    public function getMediaDir() {
        return $this->mediaDir;
    }

    public function getTempMediaDir() {
        return $this->tempMediaDir;
    }

    public function parse(Media &$media) {
        //If the file is already processing, we dont have to do this again.
        if($media->status === Media::STATUS_PROCESSING) {
            return Media::STATUS_PROCESSING;
        }

        //If a media file is already finished there is no point in parsing it
        if($media->status === Media::STATUS_FINISHED) {
            return Media::STATUS_FINISHED;
        }

        //@TODO before we can start fixing media files with the ERROR status, we need to know what could go wrong
        if($media->status === Media::STATUS_ERROR) {
            return Media::STATUS_ERROR;
        }

        $media->crc = $this->calculateHash($this->tempMediaDir . '/' . $media->filename);

        //It's a duplicate, remove it and return the result code
        if($existing = $this->exists($media)) {
            //Remove the temporary file
            unlink($this->tempMediaDir . '/' . $media->filename);

            //Return the duplicate statuscode
            return Media::STATUS_DUPLICATE;
        }

        //Get the media file duration in seconds
        $media->duration = $this->getDuration($media);

        //If the duration exceeds our limit, return error
        if($media->duration > App::module('shoutzor')->config('shoutzor')['uploadDurationLimit'] * 60) {
            $media->status = Media::STATUS_DURATION_TOO_LONG;
            return Media::STATUS_DURATION_TOO_LONG;
        }

        //Not a duplicate, move the file from the temp to the permanent directory.
        //Until a file finishes parsing completely, the file will never be moved to the permanent directory
        rename($this->tempMediaDir . '/' . $media->filename, $this->mediaDir . '/' . $media->filename);

        //Set the status of the media file to processing
        $media->save(['status' => Media::STATUS_PROCESSING]);

        //Create AcoustID instance
        $acoustid = new AcoustID();

        //get the metatags from the media file
        $defaultTags = $this->getID3Tags($media);

        //Check if acoustID is enabled.
        if($acoustid->isEnabled()) {
            $tags = $acoustid->getMediaInfo($this->mediaDir . '/' . $media->filename);

            if($tags !== false) {
                //If no title was found, set the manually fetched title
                if($tags['title'] === false) {
                    $tags['title'] = $defaultTags['title'];
                }

                //If no artist data was found, set the manually fetched artist data
                if($tags['artist'] === false) {
                    $tags['artist'] = $defaultTags['artist'];
                }

                //If no album data was found, set the manually fetched album data
                if($tags['album'] === false) {
                    $tags['album'] = $defaultTags['album'];
                }
            } else {
                //Fetching AcoustID Data failed
                $tags = $defaultTags;
            }
        } else {
            //AcoustID is disabled
            $tags = $defaultTags;
        }

        //Set the title of the media file
        $media->title = $tags['title'];

        //Add artists for each track
        if(is_array($tags['artist'])) {
            foreach($tags['artist'] as $artist) {
                $this->addArtist($media, $artist);
            }
        }

        //Add artists for each track
        if(is_array($tags['album'])) {
            foreach($tags['album'] as $album) {
                $this->addAlbum($media, $album);
            }
        }

        //Set the correct ID3 tags on the file
        $this->writeID3Tags($this->mediaDir . '/' . $media->filename, $tags['title'], implode(", ", $tags['artist']));

        //We're finished, set the status of the media file to finished
        $media->save(['status' => Media::STATUS_FINISHED]);

        //Return the finished statuscode
        return Media::STATUS_FINISHED;
    }

    public function addArtist(Media $media, $name, $image = '') {
        $artist = Artist::query()->where('name = :name', ['name' => $name]);

        if($artist->count() > 0) {
            $artist = $artist->first();
        } else {
            $artist = Artist::create();
            $artist->save([
                'name' => $name,
                'image' => $image
            ]);
        }

        //App::db()->createQueryBuilder();

        //@TODO add ManyToMany relation to the table @shoutzor_media_artist
    }

    public function addAlbum(Media $media, $title, $image = '') {
        $album = Album::query()->where('title = :title', ['title' => $title]);

        if($album->count() > 0) {
            $album = $album->first();
        } else {
            $album = Album::create();
            $album->save([
                'title' => $title,
                'image' => $image
            ]);
        }

        //@TODO add ManyToMany relation to the table @shoutzor_media_album
    }

    public function getID3Tags(Media $media) {

        $this->id3->option_md5_data        = true;
		$this->id3->option_md5_data_source = true;

		// Analyze file
        $info = $this->id3->analyze($this->tempMediaDir . '/' . $media->filename);
		getid3_lib::CopyTagsToComments($info);

        //Default values
		$result = array(
				'title' 	=> App::module('shoutzor')->config('shoutzor')['useFilenameIfUntitled'] == 1 ? preg_replace('/(^[^.]*.)|(\\.[^.\\s]{2,4}$)/', '', $media->filename) : 'Untitled',
				'artist' 	=> array(),
				'album' 	=> array()
			);

        //Check if any Tags are available
        if(isset($info['comments_html'])):
            //Get the media title
    		if(isset($info['comments_html']['title']) && !empty($info['comments_html']['title'][0])):
    			$result['title'] = ucwords(preg_replace("/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i", '', html_entity_decode(strtolower($info['comments_html']['title'][0]))));
    		endif;

            //Get the artists
    		if(isset($info['comments_html']['artist']) && is_array($info['comments_html']['artist'])):
    			foreach($info['comments_html']['artist'] as $artist):
    				$artist = preg_replace("/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i", '', $artist);
    				$artist = preg_replace('/(\(\)|\[\])/', '', $artist);
    				if(!empty($artist)):
    					$artist = ucwords(html_entity_decode(strtolower($artist)));
    					$artist = preg_split('/\s*(Feat\.|Vs\.|Ft\.)\s*/', $artist);
    					$artist = implode(" /// ", $artist);
    					$artist = preg_split('/\s*(&|Feat|Vs|Ft|\/\/\/)\s*/', $artist);
    					foreach($artist as $a):
    						$result['artist'][] = array(
    								'name' => html_entity_decode($a)
    							);
    					endforeach;
    				endif;
    			endforeach;
    		endif;

            //Get the album titles
    		if(isset($info['comments_html']['album']) && is_array($info['comments_html']['album'])):
    			$a = 0;
    			foreach($info['comments_html']['album'] as $album):
    				$album = preg_replace("/[a-zA-Z]*[:\/\/]*[A-Za-z0-9\-_]+\.+[A-Za-z0-9\.\/%&=\?\-_]+/i", '', $album);
    				$album = preg_replace('/(\(\)|\[\])/', '', $album);
    				if(!empty($album)):
    					$result['album'][] = array(
    							'title' => html_entity_decode($album),
    							'cover' => (isset($info['comments']['picture'][$a]['data'])) ? $info['comments']['picture'][$a]['data'] : null
    						);
    				endif;
    				$a++;
    			endforeach;
    		endif;
        endif;

        return $result;
    }

    /**
     * Write ID3 Data to the specified file
     */
    public function writeID3Tags($file, $title, $artist) {
        $tagwriter = new getid3_writetags();
        $tagwriter->filename = '/path/to/file.mp3';
        $tagwriter->tagformats = array('id3v1', 'id3v2.3');

        // set various options (optional)
        $tagwriter->overwrite_tags = true;
        $tagwriter->tag_encoding = 'UTF-8';
        $tagwriter->remove_other_tags = true;

        if(!is_array($artist)) {
            $artist = array($artist);
        }

        $tagData = [
            'title' => array($title),
            'artist' => $artist
        ];

        //Add our new Tag Data
        $tagwriter->tag_data = $tagData;

        // write tags
        if ($tagwriter->WriteTags()) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Checks if the provided instance is unique
     * @return false|Music
     */
    public function exists(Media $media) {
        $obj = Media::where('crc = :crc AND status = :status', ['crc' => $media->crc, 'status' => Media::STATUS_FINISHED]);

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
        $info = $this->id3->analyze($this->tempMediaDir . '/' . $media->filename);
        $time = $info['playtime_string'];
        $time = explode(':',$time);

        $hours = (count($time) == 3) ? $time[0] : 0;
        $mins = (count($time) > 1) ? $time[count($time)-2] : 0;
        $secs = $time[count($time)-1];

        $seconds = mktime($hours,$mins,$secs) - mktime(0,0,0);

        return $seconds;
    }

}
