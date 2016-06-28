<?php

namespace Xorinzor\Shoutzor\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

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

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray([], []);
    }
}
