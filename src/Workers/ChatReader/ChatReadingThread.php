<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 02.09.2018
 * Time: 22:13
 */

namespace RR\Worker\ChatReader;


class ChatReadingThread extends \Thread{
    private $reader;

    public function __construct(AsyncChatReader $reader){
        $this->reader = $reader;
    }

    public function run(){
        $this->reader->read();
    }
}