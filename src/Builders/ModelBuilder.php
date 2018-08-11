<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 4:05
 */

namespace Builder;

use RR\RR;
use Util\HTMLParseHelper as Parser;

abstract class ModelBuilder extends Builder {

    public function __construct(string $html, RR $rr){
        parent::__construct($html, $rr);
        $this->data = new \stdClass();
    }

    protected function createContainer($data){
        $className = str_replace('Builder', 'Entity',
            Parser::deleteAll('/Builder$/', static::class));
        return $className::fromBuilder($this->rr, $this->data);
    }
}