<?php
/**
 * Created by PhpStorm.
 * User: Toleckk
 * Date: 27.08.2018
 * Time: 0:13
 */

namespace RR\Worker;


use RR\Util\CURLHelper;
use RR\Util\HTMLParseHelper;

class GovernmentWorker extends Worker {
    const LAW_URL = 'http://rivalregions.com/parliament/donew/{{law}}/{{parameters}}/{{id}}'; //23 0 0
    const CANCEL_LAW_URL = 'http://rivalregions.com/parliament/removelaw';
    const EXPLORATION = 18;
    const MILITARY_AGREEMENT = 12;
    const OPEN_BORDER = 23;

    const GOLD = 0;
    const OIL = 3;
    const ORE = 4;
    const URANIUM = 14;
    const DIAMONDS = 15;


    /**
     * @throws \RR\Exception\RequestException
     */
    public function cancelLaw(){
        $this->curl->post(static::CANCEL_LAW_URL, ['c' => $this->parseCHTML()]);
    }

    /**
     * @throws \RR\Exception\RequestException
     */
    public function createOpenBorderLaw(){
        $this->curl->post(
            $this->createLawUrl(static::OPEN_BORDER, 0, 0),
            ['c' => $this->parseCHTML(), 'tmp_gov' => "'0'"]
        );
    }

    /**
     * @param string $capitalID
     * @throws \RR\Exception\RequestException
     */
    public function createMilitaryAgreementLaw(string $capitalID){
        if(isset($capitalID))
            $this->curl->post(
                $this->createLawUrl(static::MILITARY_AGREEMENT, $capitalID, '0'),
                ['c' => $this->parseCHTML(), 'tmp_gov' => $capitalID]
            );
    }

    /**
     * @param int $resource
     * @param array $parameters
     * @throws \RR\Exception\RequestException
     */
    public function createExplorationLaw(int $resource, array $parameters){
        $this->curl->post(
            $this->createLawUrl(static::EXPLORATION,
                $parsed = $this->parseParameters($resource, $parameters), array_pop(array_keys($parameters))),
            ['c' => $this->parseCHTML(), 'tmp_gov' => $parsed]
        );
    }

    private function parseParameters(int $resource, array $parameters){
        $parametersString = $resource . 'M';
        foreach ($parameters as $key => $value)
            $parametersString .= ($key . '-' . $value . 'R');

        return substr($parametersString, 0, -1);
    }

    private function createLawUrl(string $law, string $parameters, string $id): string{
        return str_replace('{{law}}', $law,
            str_replace('{{parameters}}', $parameters,
                str_replace('{{id}}', $id, static::LAW_URL)));
    }

    /**
     * @return string
     * @throws \RR\Exception\RequestException
     */
    private function parseCHTML(): string{
        return HTMLParseHelper::find("/var c_html = '(.+)';/",
            $this->curl->get('http://rivalregions.com/parliament?c=' . CURLHelper::milliseconds()),
            true)[1];
    }
}