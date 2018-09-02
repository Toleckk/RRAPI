<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 17.08.2018
 * Time: 19:33
 */

namespace RR\Entity;

/**
 * Class War
 * @package Entity
 * @method Region getAttacker(bool $force = false)
 * @method Region getDefender(bool $force = false)
 * @method Account getTopDamager(bool $force = false)
 * @method string getBuffer()
 * @method string getAttackDamage()
 * @method string getDefendDamage()
 * @method string getTime()
 * @method string getBeginTime()
 * @method string getDamagePerMinute()
 * @method string getEnergy()
 * @method string getCommentsCount()
 * @method string[] getComments()
 * @method Collection getAttackers(bool $force = false)
 * @method Collection getDefenders(bool $force = false)
 */
class War extends Model{}