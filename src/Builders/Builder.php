<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 07.08.2018
 * Time: 13:30
 */

namespace Builder;

use Entity\Model;
use Util\HTMLParseHelper as Parser;

abstract class Builder{
    protected $model;
    protected $html;

    /**
     * Builder constructor.
     * @param \stdClass $model
     * @param string $html
     */
    public function __construct(string $html, Model &$model = null){
        $this->html = $html;
        $this->model = is_null($model) ? new \stdClass() : $model;
    }

    public function build(){
        try {
            foreach ((new \ReflectionClass(static::class))->getMethods() as $method)
                if($name = Parser::find('/^parse\w+$/', $method->getName()))
                    $this->$name();
        } catch (\ReflectionException $e) {}
        return $this->model;
    }
}