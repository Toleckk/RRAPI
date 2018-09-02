<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 02.09.2018
 * Time: 22:01
 */

namespace RR\Worker\ChatReader;

class AsyncChatReader extends ChatReader {

    public function listen(){
        (new ChatReadingThread($this))->start();
    }
}