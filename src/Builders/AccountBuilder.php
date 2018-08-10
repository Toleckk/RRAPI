<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 07.08.2018
 * Time: 2:39
 */

namespace Builder;

use Util\HTMLParseHelper as Parser;

class AccountBuilder extends Builder{

    protected function parseID(): void{
        $this->model->id = Parser::getNumeric(Parser::find('/slide\/profile\/\d+/', $this->html));
    }

    protected function parseRating(): void{
        $this->model->rating = intval(Parser::getNumeric(Parser::find('/>\d+</',
            Parser::find('/action="listed\/region".+>/', $this->html))));
    }

    protected function parseExperience(): void{
        $matches = Parser::findAll('/\d+/',
            Parser::find('/<div action="listed\/gain.+"/', Parser::deleteAll('/\./', $this->html)));
        $this->model->experience = $matches[0];
        $this->model->newLevelAt = $matches[1];
        $this->model->experiencePerWeek = $matches[4];
    }

    protected function parseLevel(): void{
        $lvlString = intval(Parser::find("/;\">.+: \d+ \(\d+ %\)<\/div>/", $this->html));
        $matches = Parser::find("/ \d+ /", $lvlString);
        $this->model->level = Parser::getNumeric($matches);
        $this->model->levelProgress = Parser::getNumeric(Parser::deleteAll("/$matches/", $lvlString));
    }

    protected function parsePerks(): void{
        $matches = Parser::findAll('/>\d+</',
            Parser::find('/action="listed\/perk\/1".+\n/', $this->html));
        $this->model->strength = Parser::getNumeric($matches[0]);
        $this->model->education = Parser::getNumeric($matches[1]);
        $this->model->endurance = Parser::getNumeric($matches[2]);
    }

    protected function parseDamage(): void{
        $this->model->damage = Parser::getNumeric(Parser::find('/\n.+/',
            Parser::find('/<td class="white imp" colspan="2" style="width: 250px;">.+\n.+<\/td>/',
                $this->html)));
    }

    protected function parseArticles(): void{
        $matches = Parser::findAll('/>\+?\d+</',
            Parser::deleteAll('/\./', Parser::find('/action="listed\/papers\/.+\n/', $this->html)));
        $this->model->articlesCount = Parser::getNumeric($matches[0]);
        $this->model->karma = Parser::getNumeric($matches[1]) * ($matches[1][1] == '+' ? 1 : -1);
    }

    protected function parseWorkExperience(): void{
        $matches = Parser::findAll('/\d+/', Parser::deleteAll('/\./',
            Parser::find('/.+action="listed\/work".+\n/', $this->html)));
        $this->model->workExperienceLimit = $matches[0];
        $this->model->workExperience = $matches[2];
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
        if(!is_null(Parser::find('/state_details/', $matches[0])))
            $this->model->leaderOf = Parser::getNumeric(array_shift($matches));
    }

    private function setBasicPlaces(array &$matches): void{
        $this->model->region = Parser::getNumeric(array_shift($matches));
        $this->model->residency = Parser::getNumeric(array_shift($matches));
    }

    private function setWorkPermissionIfExist(array &$matches): void{
        if(Parser::find('/action="' . addcslashes($matches[0], '/') . '/', $this->html))
            $this->model->workPermission = Parser::getNumeric(array_shift($matches));
    }

    private function setPostsIfExist(array $matches): void{
        foreach ($matches as $match)
            if(Parser::find('/state_details/', $match))
                $this->model->postIn = Parser::getNumeric($match);
            else
                $this->model->governorOf = Parser::getNumeric($match);
    }


    protected function parseNickname(): void{
        $matches = explode(' ', Parser::find('/<h1 class="white hide_for_name">.+>/', $this->html));
        $this->model->partyTag = Parser::deleteAll('/[\[\]]/', $matches[9]);
        $this->model->nickname = Parser::deleteAll('/<.+>/', $matches[10]);
    }

    protected function parseParty(): void{
        $this->model->party = Parser::getNumeric(Parser::find('/slide\/party\/\d+/', $this->html));
    }


    protected function parseDonations(): void{
        $donations = [];
        foreach (Parser::findAll('/tip pointer .+?(<\/span>)/',
                Parser::deleteAll('/&nbsp;/', $this->html)) as $match)
            if(!is_null($key = explode(' ', $match)[2]))
                $donations[static::rightDonationKey($key)]
                    = Parser::deleteAll('/<|>/', Parser::find('/>.+</', $match));
        $this->model->donations = $donations;
    }

    private static function rightDonationKey(string $key): string{
        if($key == 'white')
            return 'money';
        else if ($key == 'yellow')
            return 'gold';
        return $key;
    }
}