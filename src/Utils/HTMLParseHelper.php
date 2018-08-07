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