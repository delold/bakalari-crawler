<?php

namespace Skolar\Modules\Bakalari;

use \Symfony\Component\DomCrawler\Crawler;

class VyukaModule extends \Skolar\Modules\BaseModule {

    private $page = null;
    private $subject = null;

    /**
     * 
     * @param mixed[] $request
     * @return \Skolar\Parameters
     */
    public function getParameters($request = null) {
        
        $this->parameters->name = "Přehled výuky";
        $this->parameters->optional = (!empty($request[0]['view'])) 
            ? array('ctl00$cphmain$droppredmety' => $request[0]['view']) 
            : array();
        
        $this->parameters->required = (!empty($request[0]['page']))
            ? array('__CALLBACKID' => 'ctl00$cphmain$roundvyuka$repetk',
                    '__CALLBACKPARAM' => 'c0:KV|2;[];GB|20;12|PAGERONCLICK3|PN' . $request[0]['page'] . ';') 
            : array();

        return $this->parameters;
    }

    /**
     * 
     * @param \Symfony\Component\DomCrawler\Crawler $request
     * @return \Skolar\Response
     */
    public function parse($request) {
        $data = $request->filterXPath("//table[@class='dxgvTable']//tr[@class='dxgvDataRow']");
        $vyuka = array("vyuka" => array());


        foreach ($data as $n => $row) {
            $cells = (new Crawler($row))->filterXPath("./*/td");

            $lesson = array_filter(array_combine(array("date", "lesson", "topic", "detail", "number"), $cells->extract("_text")), function($item) {
                $item = trim($item);
                return !empty($item);
            });
            $lesson['lesson'] = str_replace(". hod", "", $lesson['lesson']);

            $vyuka['vyuka'][] = $lesson;
        }

        //get pages
        if (count($pages = $request->filterXPath('//*[@class="dxgvPagerBottomPanel"]//*[contains(@class, "dxp-num")]')) > 0) {
            $vyuka['pages'] = str_replace(array("[", "]"), "", $pages->extract("_text"));
        }

        //get lessons
        if (count($lessons = $request->filterXPath('//select[@name="ctl00$cphmain$droppredmety"]/option')) > 0) {
            $vyuka['views'] = $lessons->extract(array("_text", "value"));

            array_walk($vyuka["views"], function(&$item) {
                $item = array_combine(["label", "value"], $item);
            });
        }

        return $this->response->setResult($vyuka);
    }

}

?>