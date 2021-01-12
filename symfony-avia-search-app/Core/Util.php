<?php


namespace App\Core;


class Util
{
    public static function calculateMinutes(\DateInterval $int)
    {
        $days = $int->format('%a');

        return ($days * 24 * 60) + ($int->h * 60) + $int->i;
    }

    public static function calculateHours(\DateInterval $int)
    {
        $days = $int->format('%a');

        return ($days * 24) + $int->h;
    }

    public static function departureReturnDate(string $dateTime)
    {
        return \DateTime::createFromFormat('dmy', $dateTime, new \DateTimeZone('UTC'))->setTime(0,0,0);
    }
}