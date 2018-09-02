<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 19.08.2018
 * Time: 20:04
 */

namespace RR\Worker;


use RR\RR;
use RR\Util\CURLHelper;

abstract class Worker{
    protected $rr;
    protected $curl;

    public function __construct(RR &$rr, CURLHelper &$curl){
        $this->rr = $rr;
        $this->curl = $curl;
    }
}