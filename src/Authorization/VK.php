<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 25.07.2018
 * Time: 13:11
 */

namespace Authorization;


use Exception\AuthorizationException;
use Exception\RequestException;
use Util\HTMLParseHelper;

class VK extends AuthorizationHelper{
    const OAUTH_URL =
        'https://oauth.vk.com/authorize?client_id=3524629&display=page&scope=notify,friends&redirect_uri=http://rivalregions.com/main/vklogin&response_type=code&state=';

    /**
     * @throws RequestException
     * @throws \Exception\MakeDirectoryException
     * @throws AuthorizationException
     */
    public function authorization() : void{
        $cookiePath = static::getCookieDirectory('VK') . "/$this->login";

        if (file_exists($cookiePath))
            if ($this->force)
                unlink($cookiePath);
            else
                try { //TODO: CHECK
                    $this->curl->get("https://m.vk.com/id0", $headers, 0,
                        null, $cookiePath);
                } catch (RequestException $exception) {
                    unlink($cookiePath);
                }

        if(!file_exists($cookiePath)) {
            $body = $this->curl->get("https://m.vk.com", $headers, 0, $cookiePath);
            $this->curl->post(
                HTMLParseHelper::cut(substr($body, strpos($body, 'https://login.vk.com')), '"'),
                ["email" => $this->login, "pass" => $this->password],
                $headers,1, $cookiePath, $cookiePath);
        }

        try {
            $body = $this->curl->get("https://m.vk.com/id0", $headers, 0,
                null, $cookiePath);
            $this->authorizationRR(preg_replace( "/\D/", '',
                    HTMLParseHelper::cut(substr($body, strpos($body, 'href="/id')), '?')),
                $cookiePath);
        } catch (RequestException $exception){
            throw new AuthorizationException();
        }
    }

    /**
     * @param $id
     * @param $cookiePath
     * @return int
     * @throws AuthorizationException
     * @throws RequestException
     * @throws \Exception\MakeDirectoryException
     */
    protected function authorizationRR($id, $cookiePath) : void{
        $cookieRR = static::getCookieDirectory() . DIRECTORY_SEPARATOR . $id;

        if(file_exists($cookieRR) && ($this->force || $this->checkCookie($cookieRR) === false))
            unlink($cookieRR);

        if(!file_exists($cookieRR)) {
            $body =
                $this->curl->get(static::OAUTH_URL,$headers, 1,null, $cookiePath);

            $this->curl->get(
                HTMLParseHelper::cut(
                    substr($body, strpos($body, 'https://login.vk.com/?act=grant_access')), '"'),
                $headers,1,null, $cookiePath, static::OAUTH_URL);

            $id = HTMLParseHelper::cut(substr($headers, strpos($headers, 'viewer_id=') + 10), "&");
            $accessToken = substr(
                HTMLParseHelper::cut(substr($headers, strpos($headers, 'access_token=') + 13), "&"),
                0, 32);
            $hash = substr(
                HTMLParseHelper::cut(substr($headers, strpos($headers, 'auth_key=') + 9), "\n"),
                0, 32);

            $this->curl->get(
                "http://rivalregions.com/?id=$id&id=$id&gl_number=ru&gl_photo=&gl_photo_medium=&gl_photo_big=&tmz_sent=3&wdt_sent=1280&register_locale=ru&stateshow=&access_token=$accessToken&hash=$hash",
                $headers, 1, $cookieRR, null,
                "http://rivalregions.com/?api_url=http://api.vk.com/api.php&access_token=$accessToken&language=0&api_id=3201433&viewer_id=$id&user_id=2018017&stateshow=&auth_key=$hash");
        }

        if(($accountID = $this->checkCookie($cookieRR)) !== false) {
            $this->accountID = $accountID;
            $this->cookiePath = $cookieRR;
        } else
            throw new AuthorizationException();
    }
}