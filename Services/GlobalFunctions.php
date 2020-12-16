<?php

namespace PouyasoftBundle\Services;

use Countable;
use Doctrine\ORM\EntityManager;

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

    public static function getUniqueCode($length, $containLetter = false, $containUpperCaseLetter = false)
    {
        $pool = "0123456789";
        if($containLetter) $pool .= "abcdefghijklmnopqrstuvwxyz";
        if($containUpperCaseLetter) $pool .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $max = strlen($pool);

        $result = "";
        for ($i=0; $i < $length; $i++)
            $result .= $pool[random_int(0, $max-1)];

        return $result;
    }

    public static function arrayColumnDeep(array $array, $column, $index_key = null, $subArrayProperty = null)
    {
        if($subArrayProperty)
            foreach ($array as $key => $value)
                if (isset($value->{$subArrayProperty}) && (is_array($value->{$subArrayProperty}) || $value->{$subArrayProperty} instanceof Countable) && count($value->{$subArrayProperty}) > 0)
                    $value->{$subArrayProperty} = self::arrayColumnDeep($value->{$subArrayProperty}->toArray(), null, $index_key, $subArrayProperty);

        return array_column($array, $column, $index_key);
    }

    public static function arrayDeepCopy($arr) {
        $newArray = array();
        foreach($arr as $key => $value) {
            if(is_array($value)) $newArray[$key] = self::arrayDeepCopy($value);
            else if(is_object($value)) $newArray[$key] = clone $value;
            else $newArray[$key] = $value;
        }
        return $newArray;
    }

    public static function arrayMergeDeep($arr1, $arr2) {
        $merged = $arr1;
        foreach ($arr2 as $k => $v) {
            if (is_array($v) && isset($arr1[$k]) && is_array($arr1[$k]))
                $merged[$k] = self::arrayMergeDeep($arr1[$k], $v);
            else
                $merged[$k] = $v;
        }
        return $merged;
    }
}