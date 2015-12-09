<?php

namespace Xorinzor\Shoutzor\Model;

use Pagekit\Application as App;
use Pagekit\System\Model\DataModelTrait;
use Pagekit\Database\ORM\ModelTrait;

/**
 * @Entity(tableClass="@shoutzor_music")
 */
class Music implements \JsonSerializable{

    use ModelTrait, DataModelTrait;

    /* song pending processing */
    const STATUS_UPLOADED = 0;

    /* song is beeing processed */
    const STATUS_PROCESSING = 1;

    /* song has successfully been processed */
    const STATUS_FINISHED = 2;

    /* an error occured during any of the previous steps */
    const STATUS_ERROR = 3;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="string") */
    public $title;

    /** @Column(type="integer") */
    public $artist_id;

    /** @Column(type="string") */
    public $filename;

    /** @Column(type="integer") */
    public $uploader_id;

    /** @Column(type="boolean") */
    public $is_video;

    /**
     * @BelongsTo(targetEntity="Xorinzor\Shoutzor\Model\Artist", keyFrom="artist_id")
     */
    public $artist;

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

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray([], []);
    }
}