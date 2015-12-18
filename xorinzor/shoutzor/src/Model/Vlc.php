<?php

namespace Xorinzor\Shoutzor\Model;

use Pagekit\Application as App;
use Pagekit\Database\ORM\ModelTrait;

/**
 * @Entity
 */
class Vlc {

    use ModelTrait;

    const CONTROL_PLAY = 1;
    const CONTROL_PAUSE = 2;
    const CONTROL_STOP = 3;
    const CONTROL_NEXT = 4;
    const CONTROL_ENQUEUE = 5;

    public static function getCommands()
    {
        return [
            self::CONTROL_PLAY => 'play',
            self::CONTROL_PAUSE => 'pause',
            self::CONTROL_STOP => 'stop',
            self::CONTROL_NEXT => 'next',
            self::CONTROL_ENQUEUE => 'enqueue'
        ];
    }

    public static function getCommandText($command)
    {
        $commands = self::getCommands();

        if(isset($commands[$command])) {
            return $commands[$command];
        }

        throw new \Exception("Invalid VLC command");
    }
}