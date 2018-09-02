<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 3:09
 */

namespace RR\Entity;


use RR\RR;

abstract class LazyLoader{
    protected $id;
    protected $loaded = false;
    /**
     * @var RR
     */
    protected $rr;

    public function __construct(RR &$rr = null, string $id = null){
        $this->rr = $rr;
        $this->id = $id;
    }

    protected function get(bool $force = false){
        return (($this->loaded && !$force) ? $this : $this->load());
    }

    protected abstract function load();
}