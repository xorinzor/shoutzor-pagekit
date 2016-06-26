<?php

namespace Xorinzor\Shoutzor\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

/**
 * @Entity(tableClass="@shoutzor_requestlist")
 */
class Request implements \JsonSerializable{

    use ModelTrait;

    /** @Column(type="integer") @Id */
    public $id;

    /** @Column(type="integer") @Media_id */
    public $media_id;

    /** @Column(type="integer") @Requester_id */
    public $requester_id;

    /** @Column(type="datetime") */
    public $requesttime;

    /**
     * @BelongsTo(targetEntity="Xorinzor\Shoutzor\Model\Media", keyFrom="media_id")
     */
    public $media;

    /**
     * @BelongsTo(targetEntity="Pagekit\User\Model\User", keyFrom="requester_id")
     */
    public $user;

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        return $this->toArray([], []);
    }
}
