<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 31.08.2018
 * Time: 20:05
 */

namespace RR\Worker;

use RR\Constants\ChatLanguages;
use RR\Worker\ChatReader\AsyncChatReader;
use RR\Worker\ChatReader\SyncChatReader;

class ChatWorker extends WebSocketsWorker implements ChatLanguages{
    private $activeChat;

    public function sendMessage(){}

    public function connectToChat(string $room): void{
        $this->send(42, '["rr_room","' . ($this->activeChat = $room) . '_0"]');
    }

    public function connectToPartyChat(string $partyID): void{
        $this->connectToChat('p' . $partyID . 'p');
    }

    //TODO: check if threads enabled
    public function startReading(\Closure $onMessage, bool $async = false){
        ($async ? new AsyncChatReader($this->socket, $onMessage)
            : new SyncChatReader($this->socket, $onMessage))->listen();
    }
}