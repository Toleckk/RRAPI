<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 09.08.2018
 * Time: 13:05
 */

namespace Entity;


use RR\RR;

trait LazyLoaderTrait{
    private $rr;
    protected $loaded = false;

    protected function setRR(&$rr){
        $this->rr = $rr;
    }

    protected function load(){
        $getterName = 'get' . ucfirst(array_pop(explode('\\', static::class)));
        $this->model = $this->rr->$getterName($this->model->id)->model;
        $this->loaded = true;
        return $this;
    }
}