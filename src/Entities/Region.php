<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 23:00
 */

namespace Entity;

/**
 * Class Region
 * @package Entity
 * @method Collection getWorkPermits()
 */
class Region extends Model implements WorkPermitable {

    public function applyForWorkPermit(){
        // TODO: Implement applyForWorkPermit() method.
    }

    //TODO delete
    public function getAutonomy(){
        return 35;
    }
}