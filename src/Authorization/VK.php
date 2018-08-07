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
use Util\HTMLParseHelper as Parser;

class VK extends AuthorizationHelper{
    const OAUTH_URL =
        'https://oauth.vk.com/authorize?client_id=3524629&display=page&scope=notify,friends&redirect_uri=http://rivalregions.com/main/vklogin&response_type=code&state=';
    const COOKIE_CHECK_URL = 'https://m.vk.com/id0';

    /**
     * @param string $cookiePath
     * @throws RequestException
     */
    protected function logIn(string $cookiePath) : void{
        if (!file_exists($cookiePath)) {
            $body = $this->curl->get("https://m.vk.com", $headers, 0, $cookiePath);
            file_put_contents('test.txt', $body);
            $this->curl->post(
                Parser::cut(substr($body, strpos($body, 'https://login.vk.com')), '"'),
                ["email" => $this->login, "pass" => $this->password],
                $headers, 1, $cookiePath, $cookiePath);
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
    protected function authorizeRR(string $cookiePath) : void {
        $id = $this->getID();
        $cookieRR = static::getCookieDirectory() . DIRECTORY_SEPARATOR . $id;

        if (file_exists($cookieRR) && ($this->force || $this->checkRRCookies($cookieRR) === false))
            unlink($cookieRR);

        if (!file_exists($cookieRR)) {
            $body =
                $this->curl->get(static::OAUTH_URL, $headers, 1, null, $cookiePath);

            if(preg_match('/https:\/\/login.vk.com\/\?act=grant_access.+\"/', $body)) {
                $this->curl->get(
                    Parser::cut(
                        substr($body, strpos($body, 'https://login.vk.com/?act=grant_access')), '"'),
                    $headers, 1, null, $cookiePath, static::OAUTH_URL);
                $parameters = $this->getParametersFromHeaders($headers);
            } else
                $parameters = $this->getParametersFromBody($body);

            $id = $parameters['id'];
            $accessToken = $parameters['accessToken'];
            $hash = $parameters['hash'];

            $this->curl->get(
                "http://rivalregions.com/?id=$id&id=$id&gl_number=ru&gl_photo=&gl_photo_medium=&gl_photo_big=&tmz_sent=3&wdt_sent=1280&register_locale=ru&stateshow=&access_token=$accessToken&hash=$hash",
                $headers, 1, $cookieRR, null,
                "http://rivalregions.com/?api_url=http://api.vk.com/api.php&access_token=$accessToken&language=0&api_id=3201433&viewer_id=$id&user_id=2018017&stateshow=&auth_key=$hash");
        }

        if (($accountID = $this->checkRRCookies($cookieRR)) !== false) {
            $this->accountID = $accountID;
            $this->cookiePath = $cookieRR;
        } else
            throw new AuthorizationException();
    }

    /**
     * @param string $body
     * @return string[]
     */
    private function getParametersFromBody(string $body) : array{
        preg_match('/name="id" value="\d+">/', $body, $matches);
        $parameters['id'] = Parser::getNumeric($matches[0]);
        preg_match('/name="access_token".+"/', $body, $matches);
        preg_match('/value=".+"/', $matches[0], $matches);
        preg_match('/".+"/',$matches[0], $matches);
        preg_match('/[^"].+[^"]/', $matches[0], $matches);
        $parameters['accessToken'] = trim($matches[0]);
        preg_match('/name="hash".+"/', $body, $matches);
        preg_match('/value=".+"/', $matches[0], $matches);
        preg_match('/".+"/',$matches[0], $matches);
        preg_match('/[^"].+[^"]/', $matches[0], $matches);
        $parameters['hash'] = trim($matches[0]);
        return $parameters;
    }

    /**
     * @param string $headers
     * @return string[]
     */
    private function getParametersFromHeaders(string $headers) : array{
        preg_match_all('/Location.+\n/', $headers, $matches);
        preg_match('/[^(Location: )].+\n/', $matches[0][1],$matches);
        $location = $matches[0];
        preg_match('/access_token=.+?&/', $location,$matches);
        preg_match('/[^(access_token=)].+[^&]/', $matches[0], $matches);
        $parameters['accessToken'] = trim($matches[0]);
        preg_match('/viewer_id=\d+?&/', $location, $matches);
        $parameters['id'] = Parser::getNumeric($matches[0]);
        preg_match('/auth_key=.+\n/', $location, $matches);
        preg_match('/[^(auth_key=)].+\n/', $matches[0],$matches);
        $parameters['hash'] = trim($matches[0]);
        return $parameters;
    }

    /**
     * @throws RequestException
     * @throws \Exception\MakeDirectoryException
     */
    private function getID(){
            $cookie = file_get_contents(static::getCookieDirectory('VK')
                        . DIRECTORY_SEPARATOR . $this->login);
            preg_match('/\tl\t\d+/', $cookie, $matches);
            return Parser::getNumeric($matches[0]);
    }
}