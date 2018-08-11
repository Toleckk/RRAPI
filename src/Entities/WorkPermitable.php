<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 12.08.2018
 * Time: 0:07
 */

namespace Entity;

/**
 * Interface WorkPermitable
 * @package Entity
 * @method Collection getWorkPermits()
 * TODO: change name
 */
interface WorkPermitable{
    public function applyForWorkPermit();
}