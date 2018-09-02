<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 25.07.2018
 * Time: 13:06
 */

namespace RR\Authorization;


use \RR\Exception\MakeDirectoryException;
use \RR\Exception\RequestException;
use \RR\Util\CURLHelper;
use \RR\Util\HTMLParseHelper;

abstract class AuthorizationHelper{
    const COOKIE_PATH = __DIR__.'/../../cookie';

    protected $curl;
    protected $login;
    protected $password;
    protected $force;

    /**
     * @var int
     */
    public $accountID;

    /**
     * @var string
     */
    public $cookiePath;


    /**
     * AuthorizationHelper constructor.
     * @param string $login
     * @param string $password
     * @param bool $force
     */
    public function __construct(string $login, string $password, bool $force){
        $this->curl = new CURLHelper();
        $this->login = $login;
        $this->password = $password;
        $this->force = $force;
    }

    /**
     * @throws MakeDirectoryException
     */
    public function authorize() : void {
        $cookiePath = static::getCookieDirectory(array_pop(
            explode(DIRECTORY_SEPARATOR, static::class))) . "/$this->login";
        $this->deleteIfExpired($cookiePath);
        $this->logIn($cookiePath);
        $this->authorizeRR($cookiePath);
    }

    abstract protected function logIn(string $cookiePath) : void;

    abstract protected function authorizeRR(string $cookiePath) : void;

    /**
     * @param $type
     * @return string
     * @throws MakeDirectoryException
     */
    protected static function getCookieDirectory(string $type = 'RR') : string{
        $path = static::COOKIE_PATH . DIRECTORY_SEPARATOR . $type;
        if(!(file_exists($path) || mkdir($path, 0777, true)))
            throw new MakeDirectoryException();

        return $path;
    }

    /**
     * @param $cookieRR
     * @return bool|int
     * @throws \RR\Exception\RequestException
     */
    protected function checkRRCookies(string $cookieRR){
        if(preg_match('/var id.+;/',
            $this->curl->get('rivalregions.com', $headers, 1, null, $cookieRR),
            $matches))
            return HTMLParseHelper::getNumeric($matches[0]);

        return false;
    }

    /**
     * @param string $cookiePath
     */
    protected function deleteIfExpired(string $cookiePath){
        if (file_exists($cookiePath))
            if ($this->force)
                unlink($cookiePath);
            else
                try {
                    $this->curl->get(static::COOKIE_CHECK_URL, $headers, 0,
                        null, $cookiePath);
                } catch (RequestException $exception) {
                    unlink($cookiePath);
                }
    }
}