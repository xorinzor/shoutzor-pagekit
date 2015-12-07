<?php

namespace Xorinzor\Shoutzor\Model;

use Pagekit\Application as App;

/**
 * @Entity(tableClass="@shoutzor_songs")
 */
class Song  implements \JsonSerializable {

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="string") */
    public $filename;

    /** @var array */
    protected static $properties = [
        'id' => 'getId',
        'filename' => 'getFilename',
    ];

    public function __CONSTRUCT($filename, $path) {
        $this->filename = '';
        $this->path 	= '';
        $this->setFilename($filename);
        $this->setPath($path);
    }

    public function getFilename() 	{
        return $this->filename;
    }

    public function getPath() 		{
        return $this->path;
    }

    public function setFilename($filename) {
        if(security::valid("FILENAME", $filename) $this->filename = $filename;
        return $this;
    }

    public function setPath($path) {
        if(is_dir($path)):
            if(substr($path, -1) != '/' && substr($path, -1) != '\\') $path .= '/'; //Make sure path ends with a slash
            $this->path = $path;
        endif;
        return $this;
    }

    public function getFileContent() {
        return (is_file($this->path . $this->filename)) ? file_get_contents($this->path . $this->filename) : false;
    }

    public function setFileContent($content) {
        if(!is_file($this->path . $this->filename)) return false;
        file_put_contents($this->path . $this->filename, $content, LOCK_EX);
    }
}