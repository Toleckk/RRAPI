<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 24.07.2018
 * Time: 1:40
 */


namespace RR;

use Authorization\AuthorizationHelper;
use Authorization\Facebook;
use Authorization\Google;
use Authorization\VK;
use Entity\Account;
use Exception\ParseException;
use Util\CURLHelper;
use Util\HTMLParseHelper as Parser;

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

    private function __construct(AuthorizationHelper $authHelper){
        $authHelper->authorization();
        $this->accountID = $authHelper->getAccountID();

        $this->curl = new CURLHelper($authHelper->getCookiePath());
    }

    /**
     * @param string $login
     * @param string $password
     * @param bool $force
     * @return RR
     */
    public static function VK(string $login, string $password, bool $force = false): RR {
        return new static(new VK($login, $password, $force));
    }

    public static function Facebook(string $login, string $password, bool $force = false) : RR{
        return new static(new Facebook($login, $password, $force));
    }

    public static function Google(string $login, string $password, bool $force = false) : RR{
        return new static(new Google($login, $password, $force));
    }

    /**
     * @param int $id
     * @return Account
     * @throws \Exception\RequestException
     */
    public function getAccount(int $id = -1) : Account{
        $htmlBody = $this->curl->get(
            "http://rivalregions.com/slide/profile/"
            . ($id < 0 ? $this->accountID : $id)
            . "?c=" . CURLHelper::milliseconds()
        );

        return Account::build($htmlBody);
    }
}