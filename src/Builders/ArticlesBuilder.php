<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 2:49
 */

namespace RR\Builder;

use RR\Entity\Article;

class ArticlesBuilder extends CollectionBuilder {
    protected function parseIDs(){
        if (preg_match_all('/news\/show\/(\d+)/', $this->html, $matches))
            for ($j = 0; $j < count($matches[1]); $j += 3)
                $this->data->append(new Article($this->rr, intval($matches[1][$j])));
    }
}