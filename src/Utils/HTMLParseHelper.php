<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 24.07.2018
 * Time: 11:59
 */

namespace Util;


class HTMLParseHelper{

    /**
     * @param string $pattern
     * @param string $str
     * @return null|string
     */
    public static function find(string $pattern, string $str){
        if(isset($str) && isset($pattern) && preg_match($pattern, $str, $matches))
            return $matches[0];
        return null;
    }

    /**
     * @param string $pattern
     * @param string $str
     * @return null|array
     */
    public static function findAll(string $pattern, string $str) : array{
        if(isset($str) && isset($pattern) && preg_match_all($pattern, $str, $matches))
            return $matches[0];
        return null;
    }

    /**
     * @param $str
     * @param $endSymbol
     * @return string
     */
    public static function cut($str, $endSymbol): string{
        $result = '';
        for ($i = 0; $i < strlen($str) && $str[$i] != $endSymbol; $i++)
            $result .= $str[$i];

        return $result;
    }

    public static function getNumeric($str){
        return static::deleteAll('/\D/', $str);
    }

    public static function deleteAll($pattern, $str){
        return is_null($str) ? null : preg_replace($pattern, '', $str);
    }
}