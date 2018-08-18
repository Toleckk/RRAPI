<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 3:28
 */

namespace Entity;


abstract class Container extends LazyLoader{
    /**
     * @var \stdClass|Container
     */
    protected $data;

    protected function load(){
        $this->data = $this->rr->{
        'get' . (get_class($this) == Collection::class ? ($this->containerType . 's')
            : array_pop(explode('\\', static::class)))}($this->id)->data;
        $this->loaded = true;
        return $this;
    }

    protected function fillData($data){
        $this->data = $data;
        $this->loaded = true;
    }

    public function __call($name, $arguments){
        if (!preg_match('/^get(\w+)$/', $name, $matches))
            return null;

        $value = $this->get()->data->{lcfirst($matches[1])};
        return (is_object($value) && is_subclass_of($value, self::class)
            ? $value->get((bool)$arguments[0]) : $value);
    }
}