<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 17.08.2018
 * Time: 19:54
 */

namespace Builder;


use Entity\Collection;
use Entity\War;
use Util\HTMLParseHelper as Parser;

/**
 * Class WarsBuilder
 * @package Builder
 */
class WarsBuilder extends CollectionBuilder{
    const SIDES_REGEX = '/<td.* action="map\/details\/(\d+)" class="list_avatar pointer( green)?".*<\/td>/';
    const WARS_INFO_REGEX = '/war\/details\/(\d+)" class="list_avatar yellow.*>(.+) \((.*)%\)<\/td>/';


    protected function parseBasic(){
        $sides = Parser::findAll(static::SIDES_REGEX, $this->html);
        $wars = Parser::findAll(static::WARS_INFO_REGEX, $this->html, true);
        for($i = 0; isset($wars) && $i < count($wars[0]); $i++)
            $this->data->append(Collection::fromBuilder($this->rr, new \ArrayObject([
                'damage' => Parser::deleteAll('/\./', $wars[2][$i]), 'player_side' => $this->getSide($sides),
                'damageInPercents' => floatval($wars[3][$i]), 'war' => new War($this->rr, $wars[1][$i])])));
    }

    /**
     * @param array $sides
     * @return string
     */
    private function getSide(array &$sides): string{
        if (preg_match('/pointer green">/', array_shift($sides)))
            return 'attack';
        else if (preg_match('/pointer green">/', array_shift($sides)))
            return 'defend';
        return 'neutral';
    }

}