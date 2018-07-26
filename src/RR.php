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
use Util\CURLHelper;

require_once __DIR__.'/../vendor/autoload.php';

class RR{
    /**
     * @var CURLHelper
     */
    private $curl;
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
    public static function VK($login, $password, $force = false): RR {
        return new static(new VK($login, $password, $force));
    }

    public static function Facebook($login, $password, $force = false) : RR{
        return new static(new Facebook($login, $password, $force));
    }

    public static function Google($login, $password, $force = false) : RR{
        return new static(new Google($login, $password, $force));
    }

    /**
     * @param int $id
     * @return mixed|string
     * @throws \Exception\RequestException
     */
    public function getAccount($id = -1){
        if($id < 0)
            $id = $this->accountID;

        return $this->curl->get(
            "http://rivalregions.com/slide/profile/$id?c=" . time()
        );
    }
}