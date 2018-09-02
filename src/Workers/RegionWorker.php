<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 19.08.2018
 * Time: 20:17
 */

namespace RR\Worker;

use RR\Builder\RegionBuilder;
use RR\Entity\Region;

class RegionWorker extends Worker {

    //TODO
    public function getRegion(int $id = -1) : Region{
        return (new RegionBuilder('see', $this->rr))->build();
    }
}