<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 19.08.2018
 * Time: 20:14
 */

namespace RR\Worker;

use RR\Builder\WarBuilder;
use RR\Entity\War;

class WarWorker extends Worker {
    /**
     * @param int $id
     * @return War
     * @throws \RR\Exception\RequestException
     */
    public function getWar(int $id) : War{
        return (new WarBuilder($this->curl->get("http://rivalregions.com/#war/details/$id"), $this->rr))->build();
    }
}