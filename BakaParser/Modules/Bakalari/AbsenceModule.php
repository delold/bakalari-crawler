<?php

namespace BakaParser\Modules\Bakalari;

use \Symfony\Component\DomCrawler\Crawler;

class AbsenceModule extends \BakaParser\Modules\BaseModule {

    /**
     * 
     * @param mixed[] $request
     * @return \BakaParser\Parameters
     */
    public function getParameters($request = null) {
        $this->parameters->name = "Průběžná absence";
        return $this->parameters;
    }

    /**
     * 
     * @param \Symfony\Component\DomCrawler\Crawler $request
     * @return \BakaParser\Response
     */
    public function parse($request) {
        $rows = $request->filterXPath("//*[@class='dxrp dxrpcontent']//tr");

        $absence = array();
        foreach ($rows as $n => $item) {
            $data = (new Crawler($item))->children();

            if ($n > 0) {
                $lesson = array();

                $lesson['name'] = $data->eq(0)->text();
                $lesson['total'] = $data->eq(1)->text();
                $lesson['missing'] = $data->eq(2)->text();

                $absence['absence'][] = $lesson;
            }
        }


        return $this->response->setResult($absence);
    }

}

?>