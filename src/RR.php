<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 1:47
 */

namespace RR;

use RR\Authorization\AuthorizationHelper;
use RR\Authorization\Facebook;
use RR\Authorization\Google;
use RR\Authorization\VK;
use RR\Util\CURLHelper;
use RR\Worker\AccountWorker;
use RR\Worker\ArticleWorker;
use RR\Worker\ChatWorker;
use RR\Worker\GovernmentWorker;
use RR\Worker\RegionWorker;
use RR\Worker\WarWorker;

require_once __DIR__.'/../vendor/autoload.php';

class RR{
    private $accountID;
    public $account;
    public $article;
    public $region;
    public $war;
    public $government;
    public $chat;

    /**
     * RR constructor.
     * @param AuthorizationHelper $authHelper
     * @throws \RR\Exception\MakeDirectoryException
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
        $this->chat = new ChatWorker($this, $curl);
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $force
     * @return RR
     * @throws \RR\Exception\MakeDirectoryException
     */
    public static function VK(string $login, string $password, bool $force = false): RR {
        return new static(new VK($login, $password, $force));
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $force
     * @return RR
     * @throws \RR\Exception\MakeDirectoryException
     */
    public static function Facebook(string $login, string $password, bool $force = false) : RR{
        return new static(new Facebook($login, $password, $force));
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $force
     * @return RR
     * @throws \RR\Exception\MakeDirectoryException
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