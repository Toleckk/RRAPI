<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 07.08.2018
 * Time: 1:03
 */

namespace Entity;

use Util\HTMLParseHelper as Parser;

class Account{
    public $id;
    public $nickname;
    public $partyTag;

    public $rating;
    public $level;
    public $experience;
    public $newLevelAt;
    public $levelProgress;
    public $experiencePerWeek;

    public $strength;
    public $education;
    public $endurance;

    public $damage;

    public $articlesCount;
    public $karma;

    public $workExperienceLimit;
    public $workExperience;

    public $leaderOf;
    public $postIn;
    public $governorOf;

    public $region;
    public $residency;
    public $workPermission;

    /**
     * @param string $html
     * @return Account
     */
    public static function build(string $html) : Account{
        return (new AccountBuilder($html))->build();
    }
}