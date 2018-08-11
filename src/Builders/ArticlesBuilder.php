<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 2:49
 */

namespace Builder;

use Entity\Account;

class ArticlesBuilder extends CollectionBuilder {

    //TODO
    protected function parseIDs(){
        $this->data->append(new Account($this->rr, '102808314'));
        $this->data->append(new Account($this->rr, '212255944'));
        $this->data->append(new Account($this->rr, '39775966'));
    }
}