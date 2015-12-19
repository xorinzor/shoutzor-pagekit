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

    /** @Column(type="integer") @Music_id */
    public $music_id;

    /** @Column(type="integer") @Requester_id */
    public $requester_id;

    /** @Column(type="datetime") */
    public $requesttime;

    /**
     * @BelongsTo(targetEntity="Xorinzor\Shoutzor\Model\Music", keyFrom="music_id")
     */
    public $music;

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