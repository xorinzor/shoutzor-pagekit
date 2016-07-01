<?php

namespace Xorinzor\Shoutzor\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

/**
 * @Entity(tableClass="@shoutzor_album")
 */
class Album implements \JsonSerializable{

    use ModelTrait;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="string") */
    public $title;

    /** @Column(type="string") */
    public $summary;

    /** @Column(type="string") */
    public $image;

    /**
     * @ManyToMany(targetEntity="Xorinzor\Shoutzor\Model\Media", tableThrough="@shoutzor_media_album", keyThroughFrom="album_id", keyThroughTo="media_id")
     * @OrderBy({"name" = "ASC"})
     */
    public $media;

    /**
     * @ManyToMany(targetEntity="Xorinzor\Shoutzor\Model\Artist", tableThrough="@shoutzor_artist_album", keyThroughFrom="album_id", keyThroughTo="artist_id")
     * @OrderBy({"name" = "ASC"})
     */
    public $artist;

    public function getMedia() {
        $topTracks = Media::query()
                        ->select('m.*')
                        ->from('@shoutzor_media m')
                        ->leftJoin('@shoutzor_media_album ma', 'ma.album_id = '.$this->id)
                        ->where('m.id = ma.media_id')
                        ->orderBy('m.title', 'ASC')
                        ->related(['artist', 'album'])
                        ->get();

        return $topTracks;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray([], []);
    }
}
