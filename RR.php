<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 24.07.2018
 * Time: 1:40
 */


namespace RR;

use function simplehtmldom_1_5\str_get_html;

require_once 'vendor/autoload.php';
require_once 'vendor/sunra/php-simple-html-dom-parser/Src/Sunra/PhpSimple/HtmlDomParser.php';

class RR{
    const COOKIE_PATH = '/cookie';

    private $login;

    /**
     * @param string $login
     * @param string $password
     * @return RR
     */
    public static function VK($login, $password) : RR{
        $cookiePath = "$login.txt";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://m.vk.com");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiePath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $loginUrl = str_get_html(curl_exec($ch))->find("form", 0)->action;
        curl_close($ch);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $loginUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [ "email" => $login, "pass" => $password ]);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookiePath);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookiePath);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);

        return new static($login);
    }

    private function __construct($login){
        $this->login = $login;
    }
}