<?php

namespace Xorinzor\Shoutzor\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

use Xorinzor\Shoutzor\Model\Artist;
use Xorinzor\Shoutzor\Model\Album;

/**
 * @Entity(tableClass="@shoutzor_media")
 */
class Media implements \JsonSerializable{

    use ModelTrait;

    /* song pending processing */
    const STATUS_UPLOADED = 0;

    /* song is beeing processed */
    const STATUS_PROCESSING = 1;

    /* song has successfully been processed */
    const STATUS_FINISHED = 2;

    /* an error occured during any of the previous steps */
    const STATUS_ERROR = 3;

    /* This song has already been uploaded */
    const STATUS_DUPLICATE = 4;

    /* The duration of this song exceeds the limit configured */
    const STATUS_DURATION_TOO_LONG = 5;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="string") */
    public $title;

    /** @Column(type="string") */
    public $filename;

    /** @Column(type="integer") */
    public $uploader_id;

    /** @Column(type="integer") */
    public $status;

    /** @Column(type="datetime") */
    public $created;

    /** @Column(type="string") */
    public $crc;

    /** @Column(type="integer") */
    public $duration;

    //Dirty fix - Field for SiteController - History::played_at
    public $played_at;

    /**
     * @ManyToMany(targetEntity="Xorinzor\Shoutzor\Model\Artist", tableThrough="@shoutzor_media_artist", keyThroughFrom="media_id", keyThroughTo="artist_id")
     * @OrderBy({"name" = "ASC"})
     */
    public $artist;

    /**
     * @ManyToMany(targetEntity="Xorinzor\Shoutzor\Model\Album", tableThrough="@shoutzor_media_album", keyThroughFrom="media_id", keyThroughTo="album_id")
     * @OrderBy({"name" = "ASC"})
     */
    public $album;

    /**
     * @BelongsTo(targetEntity="Pagekit\User\Model\User", keyFrom="uploader_id")
     */
    public $user;

    public static function getStatuses()
    {
        return [
            self::STATUS_UPLOADED => __('Uploaded'),
            self::STATUS_PROCESSING => __('Processing'),
            self::STATUS_FINISHED => __('Finished'),
            self::STATUS_ERROR => __('Error')
        ];
    }

    public function getStatusText()
    {
        $statuses = self::getStatuses();

        return isset($statuses[$this->status]) ? $statuses[$this->status] : __('Unknown');
    }

    public static function getHistory() {
        $history = Media::query()
                        ->select('m.*, h.played_at as played_at')
                        ->from('@shoutzor_media m')
                        ->leftJoin('@shoutzor_history h', 'h.media_id = m.id')
                        ->where('h.media_id = m.id')
                        ->orderBy('h.played_at', 'DESC')
                        ->limit(5)
                        ->related(['artist', 'album'])
                        ->get();

        return $history;
    }

    public static function getQueued() {
        $queued = Media::query()
                        ->select('m.*')
                        ->from('@shoutzor_media m')
                        ->leftJoin('@shoutzor_requestlist r', 'r.media_id = m.id')
                        ->where('r.media_id = m.id')
                        ->orderBy('r.id', 'ASC')
                        ->related(['artist', 'album'])
                        ->get();

        return $queued;
    }

    public static function getNowplaying() {
        $history = Media::query()
                        ->select('m.*, h.played_at as played_at')
                        ->from('@shoutzor_media m')
                        ->leftJoin('@shoutzor_history h', 'h.media_id = m.id')
                        ->where('h.media_id = m.id')
                        ->orderBy('h.played_at', 'DESC')
                        ->limit(1)
                        ->related(['artist', 'album'])
                        ->first();

        return $history;
    }

    public static function isRecentlyPlayed($id, $canRequestDateTime) {
        //Check if the media file has been recently played
        return (History::where('media_id = :id AND played_at > :maxTime', ['id' => $id, 'maxTime' => $canRequestDateTime])->count() == 0) ? false : true;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $data = $this->toArray([], []);

        $data['artist'] = array();
        if(!is_null($this->artist)) {
            foreach($this->artist as $artist) {
                $data['artist'][] = $artist->jsonSerialize();
            }
        }

        $data['album'] = array();
        if(!is_null($this->album)) {
            foreach($this->album as $album) {
                $data['album'][] = $album->jsonSerialize();
            }
        }

        return $data;
    }
}
