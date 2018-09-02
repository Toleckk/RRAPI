<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 12.08.2018
 * Time: 1:31
 */

namespace RR\Entity;
use RR\RR;

/**
 * Class Medal
 * @package Entity
 * @method int getId()
 * @method string getName()
 * @method int getCount()
 */
class Medal extends Model {

    public function __construct(RR &$rr, int $id, int $count, string $name){
        parent::__construct($rr, $id);
        $this->data->id = $id;
        $this->data->count = $count;
        $this->data->name = $name;
        $this->loaded = true;
    }

    //TODO
    public function getTop(){}
}