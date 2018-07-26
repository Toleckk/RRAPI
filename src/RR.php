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
use Exception\ParseException;
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
     * @throws ParseException
     * @throws \Exception\RequestException
     */
    public function getAccount($id = -1){
        if($id < 0)
            $id = $this->accountID;

        $htmlBody = $this->curl->get(
            "http://rivalregions.com/slide/profile/$id?c=" . CURLHelper::milliseconds());

        $properties = [];

        preg_match("/;\">.+: \d+ \(\d+ %\)<\/div>/", $htmlBody, $matches);
        preg_match("/ \d+ /", ($lvlString = $matches[0]), $matches);
        $properties['level'] = $matches[0];
        preg_match("/\d+/",
            str_replace($properties['level'], '', $lvlString), $matches);
        $properties['level_progress'] = $matches[0];

        preg_match('/<span action="listed\/region" class="slide_karma dot hov2 pointer">\d+<\/span>/',
            $htmlBody, $matches);
        preg_match('/>\d+</', $matches[0], $matches);
        $properties['rating'] = preg_replace('/\D/', '', $matches[0]);

        preg_match('/<span action="listed\/perk\/1".+>/', $htmlBody, $matches);
        preg_match_all('/>\d+</', $matches[0], $matches);
        $properties['strength'] = preg_replace('/\D/', '', $matches[0][0]);
        $properties['knowledge'] = preg_replace('/\D/', '', $matches[0][1]);
        $properties['stamina'] = preg_replace('/\D/', '', $matches[0][2]);

        preg_match('/.* action="listed\/work" .*>/', $htmlBody, $matches);
        preg_match_all('/(\d+\.?)+\S/', $matches[0], $matches);
        $properties['max_work_experience'] = preg_replace('/\D/', '', $matches[0][0]);
        $properties['work_experience'] = preg_replace('/\D/', '', $matches[0][1]);

        preg_match('/.*action="listed\/karma".*>/', $htmlBody, $matches);
        preg_match_all('/[>+-]\d+</', $matches[0],$matches);
        $properties['articles_count'] = preg_replace('/\D/', '', $matches[0][0]);
        $properties['carma'] = ($matches[0][1][0] == '+' ? 1 : -1)
            * preg_replace('/\D/', '', $matches[0][1]);

        preg_match('/<div action="map\/details\/\d+"/', $htmlBody, $matches);
        $properties['region_id'] = preg_replace('/\D/', '', $matches[0]);

        preg_match('/" action=\"map\/details\/\d+/', $htmlBody, $matches);
        $properties['residency_id'] = preg_replace('/\D/', '', $matches[0]);

        foreach ($properties as $property => &$value)
            if(isset($value))
                $value = ctype_digit($temp = trim($value)) ? intval($temp) : $temp;
            else
                throw new ParseException("Can't find property $property. Please, report issue");
        var_dump($properties);
        return $properties;
    }
}