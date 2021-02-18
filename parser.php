<?php

ini_set('display_errors', true);
ini_set('error_reporting', E_ALL);

require 'vendor/autoload.php';

use DiDom\Document;

$document = new Document('https://read.tafsir.one/almukhtasar#pg_-1', true);

$divs = $document->find('#pages-cont > div');

$saArr = [];

foreach($divs as $div) {

    $saArrkey = explode(',', $div->getAttribute('data-sa') )[0];
    $saArrValue = explode(',', $div->getAttribute('data-sa') )[1];

    $saArr[ $saArrkey ][] = $saArrValue;
}


$file = 'result.json';

$mainUrl = 'https://read.tafsir.one/get.php?uth&src=almukhtasar';

// https://read.tafsir.one/get.php?uth&src=almukhtasar&s=2&a=6

$saObj = [];

foreach ($saArr as $key => $value) {

    foreach ($value as $valueKey) {

        echo $key . ':' . $valueKey . ', ';

        $pageUrl = $mainUrl . '&s=' . $key . '&a=' . $valueKey;

        $page = file_get_contents($pageUrl);

        $pageJson = json_decode($page);

        $pageJsonData = $pageJson->{'data'};

        $pageJsonData =  str_replace('\n', PHP_EOL, $pageJsonData);

        $saObj[$key][$valueKey] = $pageJsonData;
    }

}

$result = json_encode($saObj, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

file_put_contents( $file, $result );

die();