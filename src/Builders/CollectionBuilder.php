<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 4:05
 */

namespace RR\Builder;


use RR\Entity\Collection;
use RR\RR;
use RR\Util\HTMLParseHelper as Parser;

abstract class CollectionBuilder extends Builder {

    public function __construct(string $html, RR $rr){
        parent::__construct($html, $rr);
        $this->data = new \ArrayObject([]);
    }

    protected function createContainer($data){
        return Collection::fromBuilder($this->rr, $this->data,
            Parser::deleteAll('/sBuilder$/', array_pop(explode('\\', static::class))));
    }
}