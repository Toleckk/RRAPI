<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 20.08.2018
 * Time: 20:09
 */

namespace RR\Builder;


use RR\Entity\Collection;
use RR\Entity\War;
use RR\Util\HTMLParseHelper as Parser;

class DamageHistoryBuilder extends CollectionBuilder{

    protected function parseWars(){
        preg_match_all('/war\/details\/(\d+)/', $this->html, $matches);
        foreach($matches[1] as $id) {
            $collection = new Collection($this->rr);
            $collection->append(['war' => new War($this->rr, $id)]);
            $this->data->append($collection);
        }
    }

    protected function parseBasic(){
        preg_match_all('/rat="(\d+)"/', $this->html, $matches);
        foreach ($this->data as $datum)
            $datum->append(['alpha' => array_shift($matches[1]),
                'damage' => array_shift($matches[1]), 'time' => array_shift($matches[1])]);
    }

    protected function parseBonuses(){
        $matches = Parser::findAll('/<span title=.+\n/', $this->html);
        foreach($this->data as $datum)
            $datum->append(static::makeBonuses(
                Parser::findAll('/([+-](\d+\.*\d*))%/', array_shift($matches), true)[1]));
    }

    private static function makeBonuses(array $parsedArray): array {
        $bonuses = static::makeBasicBonuses($parsedArray);
        if(!empty($parsedArray))
            $bonuses['department'] = array_shift($parsedArray);
        return $bonuses;
    }

    private static function makeBasicBonuses(array &$parsedArray): array{
        return ['strength' => array_shift($parsedArray), 'perks' => array_shift($parsedArray),
            'military_base' => array_shift($parsedArray), 'missile_system' => array_shift($parsedArray),
            'military_academy' => array_shift($parsedArray), 'airport' => array_shift($parsedArray),
            'sea_port' => array_shift($parsedArray), 'distance_penalty' => array_pop($parsedArray)];
    }
}