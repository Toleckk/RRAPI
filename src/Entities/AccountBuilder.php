<?php
/**
 * Created by PhpStorm.
 * User: Tolek
 * Date: 07.08.2018
 * Time: 2:39
 */

namespace Entity;

use Util\HTMLParseHelper as Parser;

class AccountBuilder extends Builder{

    protected function parseID(){
        preg_match('/slide\/profile\/\d+/', $this->html, $matches);
        $this->model->id = Parser::getNumeric($matches[0]);
    }

    protected function parseRating(){
        preg_match(//<span action="listed/region" class="slide_karma dot hob2 pointer">{{rating}}</span>
            '/\<span action="listed\/region" class="slide_karma dot hov2 pointer"\>\d+\<\/span\>/',
            $this->html, $matches);
        preg_match('/>\d+</', $matches[0],$matches);
        $this->model->rating = Parser::getNumeric($matches[0]);
    }

    protected function parseExperience(){
        preg_match('/<div action="listed\/gain.+"/',
            Parser::deleteAll('/\./', $this->html), $matches);

        preg_match_all('/\d+/', $matches[0], $matches);
        $this->model->experience = $matches[0][0];
        $this->model->newLevelAt = $matches[0][1];
        $this->model->experiencePerWeek = $matches[0][4];
    }

    protected function parseLevel(){
        preg_match("/;\">.+: \d+ \(\d+ %\)<\/div>/", $this->html, $matches);
        preg_match("/ \d+ /", ($lvlString = $matches[0]), $matches);
        $this->model->level = Parser::getNumeric($matches[0]);
        $this->model->levelProgress
            = Parser::getNumeric(Parser::deleteAll("/$matches[0]/", $lvlString));
    }

    protected function parsePerks(){
        preg_match('/action="listed\/perk\/1".+\n/', $this->html, $matches);
        preg_match_all('/>\d+</', $matches[0], $matches);

        $this->model->strength = Parser::getNumeric($matches[0][0]);
        $this->model->education = Parser::getNumeric($matches[0][1]);
        $this->model->endurance = Parser::getNumeric($matches[0][2]);
    }

    protected function parseDamage(){
        preg_match_all('/<td class="white imp" colspan="2" style="width: 250px;">.+\n.+<\/td>/',
            $this->html, $matches);
        preg_match('/\n.+/', $matches[0][0], $matches);
        $this->model->damage = Parser::getNumeric($matches[0]);
    }

    protected function parseArticles(){
        preg_match('/action="listed\/papers\/.+\n/', $this->html, $matches);
        preg_match_all('/>\+?\d+</', Parser::deleteAll('/\./', $matches[0]), $matches);

        $this->model->articlesCount = Parser::getNumeric($matches[0][0]);
        $this->model->karma = Parser::getNumeric($matches[0][1]) * ($matches[0][1][1] == '+' ? 1 : -1);
    }

    protected function parseWorkExperience(){
        preg_match('/.+action="listed\/work".+\n/', $this->html, $matches);
        preg_match_all('/\d+/', Parser::deleteAll('/\./', $matches[0]), $matches);

        $this->model->workExperienceLimit = $matches[0][0];
        $this->model->workExperience = $matches[0][2];
    }

    //TODO: REFACTOR
    protected function parsePlaces(){
        preg_match_all('/map\/((details)|(state_details))\/\d+/', $this->html, $matches);
        $matches = $matches[0];
        array_shift($matches);

        if(preg_match('/state_details/', $matches[0]))
            $this->model->leaderOf = Parser::getNumeric(array_shift($matches));

        $this->model->region = Parser::getNumeric(array_shift($matches));
        $this->model->residency = Parser::getNumeric(array_shift($matches));

        //if is WP
        if(preg_match('/\<div title=".+" action="'
            . str_replace('/', '\/', $matches[0]) . '"/', $this->html))
            $this->model->workPermission = Parser::getNumeric(array_shift($matches));

        foreach ($matches as $match)
            if(preg_match('/state_details/', $match))
                $this->model->postIn = Parser::getNumeric($match);
            else
                $this->model->governorOf = Parser::getNumeric($match);
    }

    protected function parseNickname(){
        preg_match('/<h1 class="white hide_for_name">.+>/', $this->html, $matches);
        $matches = explode(' ', $matches[0]);
        $this->model->nickname = Parser::deleteAll('/[\[\]]/', $matches[9]);
        $this->model->partyTag = Parser::deleteAll('/<.+>/', $matches[10]);
    }
}