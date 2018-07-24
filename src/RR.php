<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 24.07.2018
 * Time: 1:40
 */


namespace RR;

use Exception\AuthorizationException;
use Exception\MakeDirectoryException;
use Exception\RequestException;
use Util\CURLHelper;
use Util\HTMLParseHelper;

require_once __DIR__.'/../vendor/autoload.php';

class RR{
    const COOKIE_PATH = __DIR__.'/../cookie';

    private $login;

    /**
     * @var CURLHelper
     */
    private static $curl;


    private function __construct($login){
        $this->login = $login;
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $force
     * @return RR
     * @throws MakeDirectoryException
     * @throws \Exception\RequestException
     * @throws AuthorizationException
     */
    public static function VK($login, $password, $force = false): RR {
        static::$curl = new CURLHelper();
        $cookiePath = static::getCookieDirectory('VK') . "/$login";

        if (file_exists($cookiePath))
            if ($force)
                unlink($cookiePath);
            else
                try { //TODO: CHECK
                    static::$curl->get("https://m.vk.com/id0", $headers, 0,
                        null, $cookiePath);
                } catch (RequestException $exception) {
                    unlink($cookiePath);
                }

        if(!file_exists($cookiePath)) {
            $body = static::$curl->get("https://m.vk.com", $headers, 0, $cookiePath);
            static::$curl->post(
                HTMLParseHelper::cut(substr($body, strpos($body, 'https://login.vk.com')), '"'),
                ["email" => $login, "pass" => $password],
                $headers,
                1,
                $cookiePath,
                $cookiePath
            );
        }

        try {
            $body = static::$curl->get("https://m.vk.com/id0", $headers, 0,
                null, $cookiePath);
            $id = preg_replace( "/\D/", '', HTMLParseHelper::cut(substr($body, strpos($body, 'href="/id')), '?'));
            return static::RRAuthVK($id, $cookiePath, $force);
        } catch (RequestException $exception){
            throw new AuthorizationException();
        }
    }

    /**
     * @param $type
     * @return string
     * @throws MakeDirectoryException
     */
    private static function getCookieDirectory($type = 'RR') : string{
        $path = static::COOKIE_PATH
            . DIRECTORY_SEPARATOR
            . $type;

        if(file_exists($path) || mkdir($path, 0777, true))
            return $path;
        else
            throw new MakeDirectoryException();
    }

    /**
     * @param $id
     * @param $cookiePath
     * @param $force
     * @return RR
     * @throws MakeDirectoryException
     * @throws RequestException
     * @throws AuthorizationException
     */
    private static function RRAuthVK($id, $cookiePath, $force){
        $cookieRR = static::getCookieDirectory() . DIRECTORY_SEPARATOR . $id;

        if(file_exists($cookieRR) && ($force || static::checkCookie($cookieRR) === false))
            unlink($cookieRR);

        if(!file_exists($cookieRR)) {
            $body = static::$curl->get(
                'https://oauth.vk.com/authorize?client_id=3524629&display=page&scope=notify,friends&redirect_uri=http://rivalregions.com/main/vklogin&response_type=code&state=',
                $headers,
                1,
                null,
                $cookiePath
            );

            static::$curl->get(
                HTMLParseHelper::cut(
                    substr($body, strpos($body, 'https://login.vk.com/?act=grant_access')),
                    '"'),
                $headers,
                1,
                null,
                $cookiePath,
                'https://oauth.vk.com/authorize?client_id=3524629&display=page&scope=notify,friends&redirect_uri=http://rivalregions.com/main/vklogin&response_type=code&state='
            );

            $id = intval(
                HTMLParseHelper::cut(substr($headers, strpos($headers, 'viewer_id=') + 10), "&")
            );
            $accessToken = substr(
                HTMLParseHelper::cut(substr($headers, strpos($headers, 'access_token=') + 13), "&"),
                0, 32);
            $hash = substr(
                HTMLParseHelper::cut(substr($headers, strpos($headers, 'auth_key=') + 9), "\n"),
                0, 32);

            static::$curl->get(
                "http://rivalregions.com/?id=$id&id=$id&gl_number=ru&gl_photo=&gl_photo_medium=&gl_photo_big=&tmz_sent=3&wdt_sent=1280&register_locale=ru&stateshow=&access_token=$accessToken&hash=$hash",
                $headers, 1, $cookieRR, null,
                "http://rivalregions.com/?api_url=http://api.vk.com/api.php&access_token=$accessToken&language=0&api_id=3201433&viewer_id=$id&user_id=2018017&stateshow=&auth_key=$hash");
        }

        if(($accountID = static::checkCookie($cookieRR)) !== false)
            return new static($accountID);
        else
            throw new AuthorizationException();
    }

    /**
     * @param $cookieRR
     * @return bool|int
     * @throws RequestException
     */
    private static function checkCookie($cookieRR){
        $body = static::$curl->get(
            'rivalregions.com',
            $headers,
            1,
            null,
            $cookieRR
        );

        if(($pos = strpos($body, "var id")) !== false)
            return intval(preg_replace("/\D/", '',
                HTMLParseHelper::cut(substr($body, $pos), "\n")));

        return false;
    }
}