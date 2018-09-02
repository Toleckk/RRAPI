<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 11.08.2018
 * Time: 2:03
 */

namespace RR\Builder;

use RR\Entity\Collection;
use RR\Entity\Factory;
use RR\Entity\Medal;
use RR\Entity\Party;
use RR\Entity\Region;
use RR\Entity\State;
use RR\Util\HTMLParseHelper as Parser;

class AccountBuilder extends ModelBuilder{

    protected function parseID(): void{
        $this->data->id = Parser::getNumeric(Parser::find('/slide\/profile\/\d+/', $this->html));
    }

    protected function parseRating(): void{
        $this->data->rating = intval(Parser::getNumeric(Parser::find('/>\d+</',
            Parser::find('/action="listed\/region".+>/', $this->html))));
    }

    protected function parseExperience(): void{
        $matches = Parser::findAll('/\d+/',
            Parser::find('/<div action="listed\/gain.+"/', Parser::deleteAll('/\./', $this->html)));
        $this->data->experience = $matches[0];
        $this->data->newLevelAt = $matches[1];
        $this->data->experiencePerWeek = $matches[4];
    }

    protected function parseLevel(): void{
        $matches = Parser::find("/;\">.+: (\d+) \((\d+) %\)<\/div>/", $this->html, true);
        $this->data->level = intval($matches[1]);
        $this->data->levelProgress = intval($matches[2]);
    }

    protected function parsePerks(): void{
        $matches = Parser::findAll('/>\d+</',
            Parser::find('/action="listed\/perk\/1".+\n/', $this->html));
        $this->data->strength = Parser::getNumeric($matches[0]);
        $this->data->education = Parser::getNumeric($matches[1]);
        $this->data->endurance = Parser::getNumeric($matches[2]);
    }

    protected function parseDamage(): void{
        $this->data->damage = Parser::getNumeric(Parser::find('/\n.+/',
            Parser::find('/<td class="white imp" colspan="2" style="width: 250px;">.+\n.+<\/td>/',
                $this->html)));
        $this->data->wars = new Collection($this->rr, $this->data->id, 'War');
        $this->data->damages = new Collection($this->rr, $this->data->id, 'Damage');
    }

    protected function parseArticles(): void{
        $matches = Parser::findAll('/>\+?\d+</',
            Parser::deleteAll('/\./', Parser::find('/action="listed\/papers\/.+\n/', $this->html)));
        $this->data->articlesCount = Parser::getNumeric($matches[0]);
        $this->data->karma = intval(Parser::getNumeric($matches[1]) * ($matches[1][1] == '+' ? 1 : -1));
        $this->data->articles = new Collection($this->rr, $this->data->id, 'Article');
    }

    protected function parseWorkExperience(): void{
        $matches = Parser::findAll('/\d+/', Parser::deleteAll('/\./',
            Parser::find('/.+action="listed\/work".+\n/', $this->html)));
        $this->data->workExperienceLimit = $matches[0];
        $this->data->workExperience = $matches[2];
    }


    protected function parsePlaces(): void{
        $matches = array_slice(
            Parser::findAll('/map\/((details)|(state_details))\/\d+/', $this->html), 1);

        $this->setLeadershipIfLeader($matches);
        $this->setBasicPlaces($matches);
        $this->setWorkPermissionIfExist($matches);
        $this->setPostsIfExist($matches);
    }

    private function setLeadershipIfLeader(array &$matches): void{
        if (!is_null(Parser::find('/state_details/', $matches[0])))
            $this->data->leaderOf = new State($this->rr, Parser::getNumeric(array_shift($matches)));
    }

    private function setBasicPlaces(array &$matches): void{
        $this->data->region = new Region($this->rr, Parser::getNumeric(array_shift($matches)));
        $this->data->residency = new Region($this->rr, Parser::getNumeric(array_shift($matches)));
    }

    private function setWorkPermissionIfExist(array &$matches): void{
        if (Parser::find('/action="' . addcslashes($matches[0], '/') . '/', $this->html)) {
            $linkArr = explode('/', array_shift($matches));
            $className = 'RR\\Entity\\' . ($linkArr[1] === 'state_details' ? 'State' : 'Region');
            $this->data->workPermission = new $className($this->rr, $linkArr[2]);
        }
    }

    private function setPostsIfExist(array $matches): void{
        foreach ($matches as $match)
            if (Parser::find('/state_details/', $match))
                $this->data->postIn = new State($this->rr, Parser::getNumeric($match));
            else
                $this->data->governorOf = $this->rr->getRegion(Parser::getNumeric($match))->getAutonomy();
    }


    protected function parseNickname(): void{
        $matches = explode(' ', Parser::find('/<h1 class="white hide_for_name">.+>/', $this->html));
        $this->data->nickname = Parser::deleteAll('/<.+>/', array_pop($matches));
        if (preg_match('/\[(.+)\]/', array_pop($matches), $matches))
            $this->data->partyTag = $matches[1];
    }

    protected function parseParty(): void{
        $this->data->party = new Party($this->rr,
            Parser::find('/slide\/party\/(\d+)/', $this->html, true)[1]);
    }


    protected function parseDonations(): void{
        $donations = [];
        foreach (Parser::findAll('/tip pointer .+?(<\/span>)/',
            Parser::deleteAll('/&nbsp;/', $this->html)) as $match)
            if (!is_null($key = explode(' ', $match)[2]))
                $donations[static::rightDonationKey($key)]
                    = Parser::deleteAll('/<|>/', Parser::find('/>.+</', $match));
        $this->data->donations = $donations;
    }

    private static function rightDonationKey(string $key): string{
        if ($key == 'white')
            return 'money';
        else if ($key == 'yellow')
            return 'gold';
        return $key;
    }


    protected function parseNations(): void{
        $matches = Parser::findAll('/action="listed\/(\w*)nation\/(\d+|\w+)"/', $this->html, true);
        $arr = [];
        for ($i = 3; $i < count($matches[0]); $i++)
            array_push($arr, [$matches[2][$i], $matches[1][$i] == 'green']);
        $this->data->nations = $arr;
    }

    //TODO search region
    protected function parseHouse(): void{
        preg_match('/".+, .+: (\d+), .+?: (.+?), /', $this->html, $matches);
        $this->data->house = intval($matches[1]);
        $this->data->houseRegion = $matches[2];
    }

    protected function parseFactories(){
        if (preg_match('/factory\/whose\/\d+.+\(.+\): (.+)%.+\n.+?(\d+)/', $this->html, $matches))
            $this->data->factories = new Collection($this->rr, $this->data->id, 'Factory');
        $this->data->factoriesCount = intval($matches[2]);
        $this->data->globalShareOfProduction = floatval($matches[1]);
    }

    protected function parseFactory(){
        if (!is_null($id = Parser::find('/factory\/index\/(\d+)/', $this->html, true)[1]))
            $this->data->factory = new Factory($this->rr, $id);
    }

    protected function parseMedals(){
        if (preg_match_all('/listed\/medals\/(\d+).+title="(.+?)".+\n.*?(\d+)/', $this->html, $matches)) {
            $arr = [];
            for ($i = 0; $i < count($matches[0]); $i++)
                array_push($arr, new Medal($this->rr, $matches[1][$i], $matches[3][$i], $matches[2][$i]));
            $this->data->medals = $arr;
        }
    }

    protected function parseDateTime(){
        $matches = Parser::findAll('/(.+\d+:\d+).+\n/', $this->html, true)[1];
        $this->setNationChangeDate($matches);
        $this->setBaseDateTime($matches);
        $this->setWorkPermissionTime($matches);
    }

    private function setNationChangeDate(array &$matches){
        $this->data->nationChangeAt = array_pop(explode('"', array_pop($matches)));
    }

    private function setBaseDateTime(array &$matches){
        $this->data->registrationDate = trim(array_pop($matches));
        $this->data->lastActivity = trim(array_pop($matches));
        $this->data->residencyTime = Parser::find('/"(.+$)/',
            array_shift($matches), true)[1];
    }

    private function setWorkPermissionTime(array &$matches){
        if (!empty($matches))
            $this->data->workPermissionTime =
                Parser::find('/: (.+)$/', array_shift($matches), true)[1];
    }


    protected function parsePoliticalViews(){
        if (preg_match('/text:.*"&nbsp;(.+)"(.*\n){2}.*selected: 1/', $this->html, $matches))
            $this->data->politicalViews = $matches[1];
        else
            $this->data->politicalViews = Parser::find('/<td class="white imp" colspan="2" style="width: 250px;">
					(\D+)				<\/td>/', $this->html, true)[1];
    }
}