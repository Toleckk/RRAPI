<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 2:08
 */

namespace Entity;


use RR\RR;

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
 * @method State getLeaderOf()
 * @method State getPostIn()
 * @method Autonomy getGovernorOf()
 * @method Region getRegion()
 * @method Region getResidency()
 * @method WorkPermitable getWorkPermission()
 * @method Party getParty()
 * @method array getDonations()
 * @method Collection getArticles()
 */
class Account extends Model {}