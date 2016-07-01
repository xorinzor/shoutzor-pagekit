<?php

namespace Xorinzor\Shoutzor\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

use Xorinzor\Shoutzor\Model\Album;
use Xorinzor\Shoutzor\Model\Media;

/**
 * @Entity(tableClass="@shoutzor_artist")
 */
class Artist implements \JsonSerializable{

    use ModelTrait;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="string") */
    public $name;

    /** @Column(type="string") */
    public $summary;

    /** @Column(type="string") */
    public $image;

    /**
     * @ManyToMany(targetEntity="Xorinzor\Shoutzor\Model\Media", tableThrough="@shoutzor_media_artist", keyThroughFrom="artist_id", keyThroughTo="media_id")
     * @OrderBy({"title" = "ASC"})
     */
    public $media;

    /**
     * @ManyToMany(targetEntity="Xorinzor\Shoutzor\Model\Album", tableThrough="@shoutzor_artist_album", keyThroughFrom="artist_id", keyThroughTo="album_id")
     * @OrderBy({"title" = "ASC"})
     */
    public $album;

    public function getTopMedia() {
        $topTracks = Media::query()
                        ->select('m.*, COUNT(h.id) as popularity, h.played_at as played_at')
                        ->from('@shoutzor_media m')
                        ->leftJoin('@shoutzor_media_artist ma', 'ma.artist_id = '.$this->id)
                        ->leftJoin('@shoutzor_history h', 'h.media_id = m.id')
                        ->where('m.id = ma.media_id')
                        ->orderBy('popularity, m.title', 'DESC')
                        ->limit(5)
                        ->related(['artist', 'album'])
                        ->get();

        return $topTracks;
    }

    public function getAlbums() {
        $albums = Album::query()
                        ->leftJoin('@shoutzor_artist_album aa', 'aa.artist_id = '.$this->id)
                        ->where('id = aa.album_id')
                        ->related('artist')
                        ->get();

        return $albums;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray([], []);
    }
}
