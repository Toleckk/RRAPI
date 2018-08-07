<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 07.08.2018
 * Time: 2:39
 */

namespace Entity;

use Util\HTMLParseHelper as Parser;

class AccountBuilder{
    private $account;
    private $html;

    public function __construct(string $html){
        $this->account = new Account();
        $this->html = $html;
    }

    public function build() : Account{
        $this->parseLevel();
        $this->parsePerks();
        $this->parseDamage();
        $this->parseArticles();
        $this->parseWorkExperience();
        $this->parsePlaces();

        return $this->account;
    }

    private function parseLevel(){
        preg_match("/;\">.+: \d+ \(\d+ %\)<\/div>/", $this->html, $matches);
        preg_match("/ \d+ /", ($lvlString = $matches[0]), $matches);
        $this->account->level = Parser::getNumeric($matches[0]);
        $this->account->levelProgress
            = Parser::getNumeric(Parser::deleteAll("/$matches[0]/", $lvlString));

        preg_match(
            '/\<span action="listed\/region" class="slide_karma dot hov2 pointer"\>\d+\<\/span\>/',
            $this->html, $matches);
        preg_match('/>\d+</', $matches[0],$matches);
        $this->account->rating = Parser::getNumeric($matches[0]);

        preg_match('/<div action="listed\/gain" title="\w+: \d+<br>Next level: .+/',
            Parser::deleteAll('/\./', $this->html), $matches);
        preg_match_all('/\d+/', $matches[0], $matches);
        $this->account->experience = $matches[0][0];
        $this->account->newLevelAt = $matches[0][1];
        $this->account->experiencePerWeek = $matches[0][4];
    }

    private function parsePerks(){
        preg_match('/action="listed\/perk\/1".+\n/', $this->html, $matches);
        preg_match_all('/>\d+</', $matches[0], $matches);

        $this->account->strength = Parser::getNumeric($matches[0][0]);
        $this->account->education = Parser::getNumeric($matches[0][1]);
        $this->account->endurance = Parser::getNumeric($matches[0][2]);
    }

    private function parseDamage(){
        preg_match_all('/<td class="white imp" colspan="2" style="width: 250px;">.+\n.+<\/td>/',
            $this->html, $matches);
        preg_match('/\n.+/', $matches[0][0], $matches);
        $this->account->damage = Parser::getNumeric($matches[0]);
    }

    private function parseArticles(){
        preg_match('/action="listed\/papers\/.+\n/', $this->html, $matches);
        preg_match_all('/>\+?\d+</', $matches[0], $matches);

        $this->account->articlesCount = Parser::getNumeric($matches[0][0]);
        $this->account->karma = Parser::getNumeric($matches[0][1]) * ($matches[0][1][1] == '+' ? 1 : -1);
    }

    private function parseWorkExperience(){
        preg_match('/.+action="listed\/work".+\n/', $this->html, $matches);
        preg_match_all('/\d+/', Parser::deleteAll('/\./', $matches[0]), $matches);

        $this->account->workExperienceLimit = $matches[0][0];
        $this->account->workExperience = $matches[0][2];
    }

    private function parsePlaces(){
        preg_match_all('/map\/((details)|(state_details))\/\d+/', $this->html, $matches);
        $matches = $matches[0];
        array_shift($matches);

        if(preg_match('/state_details/', $matches[0]))
            $this->account->leaderOf = Parser::getNumeric(array_shift($matches));

        $this->account->region = Parser::getNumeric(array_shift($matches));
        $this->account->residency = Parser::getNumeric(array_shift($matches));

        //if is WP
        if(preg_match('/\<div title=".+" action="'
            . str_replace('/', '\/', $matches[0]) . '"/', $this->html))
            $this->account->workPermission = Parser::getNumeric(array_shift($matches));

        foreach ($matches as $match)
            if(preg_match('/state_details/', $match))
                $this->account->postIn = Parser::getNumeric($match);
            else
                $this->account->governorOf = Parser::getNumeric($match);
    }
}