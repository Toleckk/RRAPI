<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 02.09.2018
 * Time: 20:30
 */

namespace RR\Worker\ChatReader;

use ElephantIO\Client as Elephant;

abstract class ChatReader{
    private $onMessageReceived;
    private $socket;

    public function __construct(Elephant &$socket,\Closure $onMessageReceived){
        $this->socket = $socket;
        $this->onMessageReceived = $onMessageReceived;
    }

    abstract public function listen();

    public function read(){
       $last = time();
       while(true){
           $this->timeOutUpdate($last);
           $this->handleMessage($last);
       }
   }

   private function timeOutUpdate(int &$time){
       if($time + 25 <= time()) {
           $this->socket->getEngine()->write(2, '');
           $time = time();
       }
   }

   //TODO: change name
   private function handleMessage(int $time){
       if(($response = $this->socket->read(25 - time() + $time)) !== '')
           $this->onMessageReceived->call($this, $response);
   }
}