<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 2:44
 */

namespace RR\Entity;

use RR\RR;

abstract class Model extends Container {
    /**
     * Account constructor.
     * @param RR|null $rr
     * @param int|null $id
     */
    public function __construct(RR &$rr = null, int $id = null){
        parent::__construct($rr, $id);
        $this->data = new \stdClass();
    }

    public static function fromBuilder(RR &$rr, \stdClass $model){
        $instance = new static($rr);
        $instance->fillData($model);
        return $instance;
    }

    public function __debugInfo(){
        $result = (array)$this->data;
        if(is_null($result['id']))
            $result['id'] = $this->id;
        return $result;
    }
}