<?php

namespace Xorinzor\Shoutzor\App;

class Utility {

    public static function secondsToTime($seconds) {
        $days = floor($seconds / 86400);
        $seconds -= ($days * 86400);

        $hours = floor($seconds / 3600);
        $seconds -= ($hours * 3600);

        $minutes = floor($seconds / 60);
        $seconds -= ($minutes * 60);

        $values = array(
            'day'    => $days,
            'hour'   => $hours,
            'minute' => $minutes,
            'second' => $seconds
        );

        $parts = array();

        foreach ($values as $value) {
            if ($value > 0) {
                if($value < 10) {
                    $value = '0'.$value;
                }
                $parts[] = $value;
            }
        }

        return implode(':', $parts);
    }

}
