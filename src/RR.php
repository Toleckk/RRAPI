<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 1:47
 */

namespace RR;

use Authorization\AuthorizationHelper;
use Authorization\Facebook;
use Authorization\Google;
use Authorization\VK;
use Builder\AccountBuilder;
use Builder\ArticlesBuilder;
use Builder\RegionBuilder;
use Builder\WarBuilder;
use Builder\WarsBuilder;
use Entity\Account;
use Entity\Collection;
use Entity\Region;
use Entity\War;
use Util\CURLHelper;

require_once __DIR__.'/../vendor/autoload.php';

class RR{
    /**
     * @var CURLHelper
     */
    private $curl;

    /**
     * @var int
     */
    private $accountID;

    /**
     * RR constructor.
     * @param AuthorizationHelper $authHelper
     * @throws \Exception\MakeDirectoryException
     */
    private function __construct(AuthorizationHelper $authHelper){
        $authHelper->authorize();
        $this->accountID = $authHelper->accountID;

        $this->curl = new CURLHelper($authHelper->cookiePath);
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $force
     * @return RR
     * @throws \Exception\MakeDirectoryException
     */
    public static function VK(string $login, string $password, bool $force = false): RR {
        return new static(new VK($login, $password, $force));
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $force
     * @return RR
     * @throws \Exception\MakeDirectoryException
     */
    public static function Facebook(string $login, string $password, bool $force = false) : RR{
        return new static(new Facebook($login, $password, $force));
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $force
     * @return RR
     * @throws \Exception\MakeDirectoryException
     */
    public static function Google(string $login, string $password, bool $force = false) : RR{
        return new static(new Google($login, $password, $force));
    }

    /**
     * @param int $id
     * @return Account
     * @throws \Exception\RequestException
     */
    public function getAccount(int $id = -1) : Account{
        return (new AccountBuilder(
            $this->curl->get("http://rivalregions.com/slide/profile/"
                . ($id < 0 ? $this->accountID : $id)
                . "?c=" . CURLHelper::milliseconds()),
            $this))->build();
    }

    /**
     * @param int $id
     * @return Collection
     * @throws \Exception\RequestException
     */
    public function getArticles(int $id = -1) : Collection{
        for($i = 0, $arr = new Collection($this, $id, 'Article'); true; $i += 50)
            if (count($newArr = (new ArticlesBuilder($this->curl->get("http://rivalregions.com/listed/papers/$id"
                    . ($i === 0 ? '?c=' . time() : "/0/$i")), $this))->build()) > 0)
                $arr->append($newArr);
            else
                return $arr;
    }

    public function getRegion(int $id = -1) : Region{
        return (new RegionBuilder('see', $this))->build();
    }

    /**
     * @param int $id
     * @return Collection
     * @throws \Exception\RequestException
     */
    public function getWars(int $id = -1) : Collection{
        for($i = 0, $arr = new Collection($this, $id, 'Article'); true; $i += 12)
            if(count($newArr = (new WarsBuilder($this->curl->get("http://rivalregions.com/war/inall/$id"
                    . ($i === 0 ? '?c=' . time() : "/$i")), $this))->build()) > 0)
                $arr->append($newArr);
            else
                return $arr;
    }

    /**
     * @param int $id
     * @return War
     * @throws \Exception\RequestException
     */
    public function getWar(int $id) : War{
        return (new WarBuilder($this, $this->curl->get("http://rivalregions.com/#war/details/$id")))->build();
    }
}