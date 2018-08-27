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
use Util\CURLHelper;
use Worker\AccountWorker;
use Worker\ArticleWorker;
use Worker\GovernmentWorker;
use Worker\RegionWorker;
use Worker\WarWorker;

require_once __DIR__.'/../vendor/autoload.php';

class RR{
    private $accountID;
    public $account;
    public $article;
    public $region;
    public $war;
    public $government;

    /**
     * RR constructor.
     * @param AuthorizationHelper $authHelper
     * @throws \Exception\MakeDirectoryException
     */
    private function __construct(AuthorizationHelper $authHelper){
        $authHelper->authorize();
        $this->accountID = $authHelper->accountID;
        $curl = new CURLHelper($authHelper->cookiePath);

        $this->account = new AccountWorker($this, $curl);
        $this->article = new ArticleWorker($this, $curl);
        $this->region = new RegionWorker($this, $curl);
        $this->war = new WarWorker($this, $curl);
        $this->government = new GovernmentWorker($this, $curl);
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
     * @return int
     */
    public function getAccountID(): int{
        return $this->accountID;
    }
}