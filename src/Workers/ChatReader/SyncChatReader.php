<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 02.09.2018
 * Time: 22:01
 */

namespace RR\Worker\ChatReader;


class SyncChatReader extends ChatReader {
    public function listen(){
        $this->read();
    }
}