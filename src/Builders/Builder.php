<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 3:57
 */

namespace RR\Builder;

use RR\RR;
use RR\Util\HTMLParseHelper as Parser;

abstract class Builder{
    protected $html;
    protected $data;
    /**
     * @var RR
     */
    protected $rr;

    /**
     * ArticlesBuilder constructor.
     * @param string $html
     * @param RR $rr
     */
    public function __construct(string $html, RR &$rr){
        $this->html = $html;
        $this->rr = $rr;
    }

    public function build(){
        try {
            foreach ((new \ReflectionClass(static::class))->getMethods() as $method)
                if($name = Parser::find('/^parse\w+$/', $method->getName()))
                    $this->$name();
        } catch (\ReflectionException $e) {}
        return $this->createContainer($this->data);
    }

    protected abstract function createContainer($data);
}