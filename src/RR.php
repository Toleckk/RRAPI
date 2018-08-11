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
use Entity\Account;
use Entity\Collection;
use Entity\Region;
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
        //for debug
        $html =  $this->curl->get("http://rivalregions.com/slide/profile/"
            . ($id < 0 ? $this->accountID : $id)
            . "?c=" . CURLHelper::milliseconds());
        file_put_contents('test.txt', $html);
        //
        return (new AccountBuilder($html, $this))->build();
    }

    public function getArticles(int $id = -1) : Collection{
        return (new ArticlesBuilder('sdfdf', $this))->build();
    }

    public function getRegion(int $id = -1) : Region{
        return (new RegionBuilder('see', $this))->build();
    }
}