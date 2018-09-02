<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 2:08
 */

namespace RR\Entity;

/**
 * Class Account
 * @package Entity
 * @method string getId()
 * @method string getNickname()
 * @method string getPartyTag()
 * @method int getRating()
 * @method int getLevel()
 * @method string getExperience()
 * @method string getNewLevelAt()
 * @method int getLevelProgress()
 * @method string getExperiencePerWeek()
 * @method string getStrength()
 * @method string getEducation()
 * @method string getEndurance()
 * @method string getDamage()
 * @method int getArticlesCount()
 * @method string getKarma()
 * @method string getWorkExperienceLimit()
 * @method string getWorkExperience()
 * @method State getLeaderOf(bool $force = false)
 * @method State getPostIn(bool $force = false)
 * @method Autonomy getGovernorOf(bool $force = false)
 * @method Region getRegion(bool $force = false)
 * @method Region getResidency(bool $force = false)
 * @method WorkPermitable getWorkPermission(bool $force = false)
 * @method Party getParty(bool $force = false)
 * @method array getDonations()
 * @method Collection getArticles(bool $force = false)
 * @method Collection getWars(bool $force = false)
 * @method Collection getDamageHistory(bool $force = false)
 */
class Account extends Model {}