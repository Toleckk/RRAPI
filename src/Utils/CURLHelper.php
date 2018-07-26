<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 24.07.2018
 * Time: 12:52
 */

namespace Util;


use Exception\RequestException;

class CURLHelper{
    public $headers = "";

    /**
     * @var string
     */
    private $cookiePath;

    /**
     * CURLHelper constructor.
     * @param string $cookiePath
     */
    public function __construct($cookiePath = null){
        $this->cookiePath = $cookiePath;
    }

    public function handleHeaderLine($curl, $header_line){
        $this->headers .= $header_line;
        return strlen($header_line);
    }

    /**
     * @param $url
     * @param int $followLocation
     * @param null $headers
     * @param null $cookieJar
     * @param null $cookieFile
     * @return mixed
     * @throws RequestException
     */
    public function get(
        $url, &$headers = null, $followLocation = 0, $cookieJar = null, $cookieFile = null, $referer = null
    ) : string{
        return $this->sendRequest(curl_init(), $url, $headers, $followLocation, $cookieJar, $cookieFile, $referer);
    }

    /**
     * @param $url
     * @param array $values
     * @param $followLocation
     * @param &$headers
     * @param string $cookieJar
     * @param string $cookieFile
     * @return string
     * @throws RequestException
     */
    public function post(
        $url, array $values, &$headers = null, $followLocation = 0, $cookieJar = null, $cookieFile = null, $referer = null
    ) : string{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
        return $this->sendRequest($ch, $url, $headers, $followLocation, $cookieJar, $cookieFile, $referer);
    }

    /**
     * @param $ch
     * @param $url
     * @param null $headers
     * @param int $followLocation
     * @param null $cookieJar
     * @param null $cookieFile
     * @param null $referer
     * @return mixed
     * @throws RequestException
     */
    private function sendRequest(
        $ch, $url, &$headers = null, $followLocation = 0, $cookieJar = null, $cookieFile = null, $referer = null
    ) : string{
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Windows NT 6.3; Win64; x64; rv:61.0) Gecko/20100101 Firefox/61.0');
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, array(&$this, "handleHeaderLine"));

        if ($followLocation)
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        if (isset($cookieJar))
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieJar);
        if (isset($cookieFile))
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        else if(isset($this->cookiePath))
            curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookiePath);
        if (isset($referer))
            curl_setopt($ch, CURLOPT_REFERER, $referer);

        $body = curl_exec($ch);
        curl_close($ch);

        $headers = $this->headers;

        $responseCode = explode(" ",
            HTMLParseHelper::cut(substr($headers, strripos($headers, "HTTP/")), "\n"))[1];
        if($responseCode == 200){
            $this->headers = "";
            return $body;
        } else
            throw new RequestException($headers, $responseCode);
    }
}