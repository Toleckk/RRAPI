<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 09.08.2018
 * Time: 14:49
 */

namespace Entity;


use RR\RR;
use Util\HTMLParseHelper;

abstract class Model{
    use LazyLoaderTrait{ LazyLoaderTrait::__construct as private LazyLoaderTrait; }

    private $model;

    public static function build(RR &$rr, string $html){
        $builderName = static::class . 'Builder';
        $instance = new static($rr, (new $builderName($html))->build());
        $instance->loaded = true;
        return $instance;
    }

    private function __construct(RR &$rr, $model){
        $this->LazyLoaderTrait($rr);
        $this->model = $model;
    }

    private function getModel(){
        return ($this->loaded ? $this->model : $this->load());
    }

    public function __call($name, $arguments){
        if(preg_match('/^get\w+$/', $name) && empty($arguments)){
            $property = lcfirst(HTMLParseHelper::deleteAll('/get/', $name));
            return $this->getModel()->$property;
        } else
            return null;
    }

    public function __debugInfo(){
        return (array)$this->model;
    }
}