<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 19.08.2018
 * Time: 20:03
 */

namespace RR\Worker;

use RR\Builder\AccountBuilder;
use RR\Builder\DamageHistoryBuilder;
use RR\Builder\WarsBuilder;
use RR\Entity\Account;
use RR\Entity\Collection;
use RR\Util\CURLHelper;

class AccountWorker extends Worker{
    /**
     * @param int $id
     * @return Account
     * @throws \RR\Exception\RequestException
     */
    public function getAccount(int $id = -1): Account{
        return (new AccountBuilder(
            $this->curl->get("http://rivalregions.com/slide/profile/"
                . ($id < 0 ? $this->rr->getAccountID() : $id)
                . "?c=" . CURLHelper::milliseconds()),
            $this->rr))->build();
    }

    /**
     * @param int $id
     * @return Collection
     * @throws \RR\Exception\RequestException
     */
    public function getWars(int $id = -1): Collection{
        for($i = 0, $arr = new Collection($this->rr, $id); true; $i += 12)
            if(count($newArr = (new WarsBuilder($this->curl->get("http://rivalregions.com/war/inall/$id"
                    . ($i === 0 ? '?c=' . time() : "/$i")), $this->rr))->build()) > 0)
                $arr->append($newArr);
            else
                return $arr;
    }

    /**
     * @param int $id
     * @return Collection
     * @throws \RR\Exception\RequestException
     */
    public function getDamages(int $id = -1): Collection{
        for($i = 0, $arr = new Collection($this->rr, $id); true; $i += 25)
            if(count($newArr = (new DamageHistoryBuilder($this->curl->get("http://rivalregions.com/slide/damage/$id"
                    . ($i === 0 ? '?c=' . time() : "/$i")), $this->rr))->build()) > 0)
                $arr->append($newArr);
            else
                return $arr;
    }
}