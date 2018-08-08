<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 07.08.2018
 * Time: 13:30
 */

namespace Entity;


use Util\HTMLParseHelper as Parser;

abstract class Builder{
    protected $model;
    protected $html;

    /**
     * Builder constructor.
     * @param string $html
     */
    public function __construct(string $html){
        $this->html = $html;
        file_put_contents('test.txt', $html);
        $className = Parser::deleteAll('/Builder/', static::class);
        $this->model = new $className;
    }

    public function build(){
        try {
            foreach ((new \ReflectionClass(static::class))->getMethods() as $method)
                if (preg_match('/^parse\w+$/', ($name = $method->getName())))
                    $this->$name();
        } catch (\ReflectionException $e) {}
        return $this->model;
    }
}