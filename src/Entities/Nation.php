<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 10.08.2018
 * Time: 21:56
 */

namespace Entity;

//TODO
use RR\RR;

class Nation extends Model{
    public function __construct(string $id = null, bool $isGreen = false, RR $rr = null){
        parent::__construct($id, $rr);
    }
}