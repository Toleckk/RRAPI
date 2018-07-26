<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 25.07.2018
 * Time: 13:06
 */

namespace Authorization;


use Exception\MakeDirectoryException;
use Util\CURLHelper;
use Util\HTMLParseHelper;

abstract class AuthorizationHelper{
    const COOKIE_PATH = __DIR__.'/../cookie';

    protected $curl;
    protected $login;
    protected $password;
    protected $force;

    /**
     * @var int
     */
    protected $accountID;

    /**
     * @var string
     */
    protected $cookiePath;


    /**
     * AuthorizationHelper constructor.
     * @param string $login
     * @param string $password
     * @param bool $force
     */
    public function __construct($login, $password, $force){
        $this->curl = new CURLHelper();
        $this->login = $login;
        $this->password = $password;
        $this->force = $force;
    }


    abstract public function authorization() : void;

    abstract protected function authorizationRR($id, $cookiePath) : void;

    /**
     * @return int
     */
    public function getAccountID() : int{
        return $this->accountID;
    }

    /**
     * @return string
     */
    public function getCookiePath(): string{
        return $this->cookiePath;
    }

    /**
     * @param $type
     * @return string
     * @throws MakeDirectoryException
     */
    protected static function getCookieDirectory($type = 'RR') : string{
        $path = static::COOKIE_PATH
            . DIRECTORY_SEPARATOR
            . $type;

        if(file_exists($path) || mkdir($path, 0777, true))
            return $path;
        else
            throw new MakeDirectoryException();
    }

    /**
     * @param $cookieRR
     * @return bool|int
     * @throws \Exception\RequestException
     */
    protected function checkCookie($cookieRR){
        $body = $this->curl->get(
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