<?php

namespace PouyaSoft\AppzaBundle\Services;

use DateTime;

class GlobalFunctions
{
    /**
     * @param array $array_a
     * @param array $array_b
     * @return array
     */
    public static function ArrayEntityDiff($array_a, $array_b)
    {
        return array_udiff(
            is_array($array_a) ? $array_a : $array_a->toArray(),
            is_array($array_b) ? $array_b : $array_b->toArray(),
            function ($a, $b) {return $a->getId() - $b->getId();}
            );
    }

    public static function getDomain($url)
    {
        $domain = preg_replace(['/^http:\/\//', '/^https:\/\//', '/^www./'], '', $url);

        return explode('/', $domain)[0];
    }

    public static function getUniqueCode($length)
    {
//        $pool  = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $pool = "abcdefghijklmnopqrstuvwxyz";
        $pool .= "0123456789";

        $max = strlen($pool);

        $result = "";
        for ($i=0; $i < $length; $i++)
            $result .= $pool[random_int(0, $max-1)];

        return $result;
    }

    public static function startTime(DateTime $datetime = null, $hour = null) {
        return $datetime->setTime($hour != null? $hour: 0, 0, 0);
    }

    public static function endTime(DateTime $datetime = null, $hour = null) {
        return $hour != null? $datetime->setTime($hour, 0, 0): $datetime->setTime(23, 59, 59);
    }
}