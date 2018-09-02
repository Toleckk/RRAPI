<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 31.08.2018
 * Time: 20:05
 */

namespace RR\Worker;

use ElephantIO\Client as Elephant;
use ElephantIO\Engine\SocketIO\Version2X;
use RR\Util\HTMLParseHelper as Parser;

abstract class WebSocketsWorker extends Worker {
    /**
     * @var Elephant
     */
    protected $socket;
    protected $accessToken;

    /**
     * @param bool $force
     */
    protected function connectToRR(bool $force = false): void{
        if($force || !$this->socket)
            $this->socket = new Elephant(new Version2X('http://static.rivalregions.com:8880'));
    }

    /**
     * @return string
     * @throws \RR\Exception\RequestException
     */
    protected function getActualToken(): string{
        return $this->accessToken ? $this->accessToken : ($this->accessToken = $this->parseAccessToken());
    }

    /**
     * @param int $code
     * @param string $message
     */
    protected function send(int $code, string $message): void{
        $this->connectToRR();
        $this->socket->getEngine()->write($code, $message);
    }

    /**
     * @return string
     * @throws \RR\Exception\RequestException
     */
    private function parseAccessToken(): string{
        return Parser::find(
            '/<input name="hash" value="(.+)" type="hidden">/',
            $this->curl->get('http://rivalregions.com/main/content'), true)[1];
    }
}