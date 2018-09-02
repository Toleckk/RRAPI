<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 19.08.2018
 * Time: 20:15
 */

namespace RR\Worker;


use RR\Builder\ArticlesBuilder;
use RR\Entity\Collection;

class ArticleWorker extends Worker {
    /**
     * @param int $id
     * @return Collection
     * @throws \RR\Exception\RequestException
     * @deprecated
     */
    public function getArticles(int $id = -1) : Collection{
        for($i = 0, $arr = new Collection($this, $id, 'Article'); true; $i += 50)
            if (count($newArr = (new ArticlesBuilder($this->curl->get("http://rivalregions.com/listed/papers/$id"
                    . ($i === 0 ? '?c=' . time() : "/0/$i")), $this->rr))->build()) > 0)
                $arr->append($newArr);
            else
                return $arr;
    }
}