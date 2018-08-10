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
    use LazyLoaderTrait;

    private $model;

    public static function build(RR &$rr, string $html){
        $builderName = str_replace('Entity', 'Builder', static::class . 'Builder');
        $instance = (new $builderName($html, new static(null, $rr)))->build();
        $instance->loaded = true;
        return $instance;
    }

    public function __construct(string $id = null, RR &$rr = null){
        $this->setRR($rr);
        $this->model = new \stdClass();
        $this->model->id = $id;
    }

    private function getModel(){
        return ($this->loaded ? $this->model : $this->load()->model);
    }

    public function __call($name, $arguments){
        if(preg_match('/^get\w+$/', $name)){
            $property = lcfirst(HTMLParseHelper::deleteAll('/get/', $name));
            $value = $this->getModel()->$property;
            return (is_subclass_of($value, self::class) ? $value->load() : $value);
        } else
            return null;
    }

    public function __set(string $name, $value){
        if(is_subclass_of($value, self::class, false))
            $value->setRR($this->rr);
        $this->model->$name = $value;
    }

    public function __debugInfo(){
        return (array)$this->model;
    }
}